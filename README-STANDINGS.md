# 📊 Tabla de Posiciones (Standings System)

## 📋 Descripción General

Sistema completo de tabla de posiciones que **se actualiza automáticamente** cuando un partido finaliza. Calcula estadísticas, puntos, diferencia de goles y mantiene un historial de los últimos 5 resultados.

---

## 🗄️ Base de Datos

### Tabla: `standings`

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
season_id           BIGINT UNSIGNED (FK → seasons)
team_id             BIGINT UNSIGNED (FK → teams)
played              INT DEFAULT 0           -- Partidos jugados
won                 INT DEFAULT 0           -- Partidos ganados
drawn               INT DEFAULT 0           -- Partidos empatados
lost                INT DEFAULT 0           -- Partidos perdidos
goals_for           INT DEFAULT 0           -- Goles a favor
goals_against       INT DEFAULT 0           -- Goles en contra
goal_difference     INT DEFAULT 0           -- Diferencia de goles
points              INT DEFAULT 0           -- Puntos totales (3 por victoria, 1 por empate)
position            INT NULLABLE            -- Posición en la tabla
form                VARCHAR(10) NULLABLE    -- Últimos 5 resultados (W/D/L)
created_at          TIMESTAMP
updated_at          TIMESTAMP

UNIQUE INDEX: (season_id, team_id)
INDEX: (season_id, points, goal_difference)
```

**Criterios de ordenamiento**:
1. **Puntos** (descendente)
2. **Diferencia de goles** (descendente)
3. **Goles a favor** (descendente)

---

## 🔧 Componentes del Sistema

### 1. Modelo: `Standing.php`

```php
// Relaciones
$standing->season    // Temporada
$standing->team      // Equipo

// Scopes
Standing::ordered()                    // Ordenar por puntos/goles
Standing::forSeason($seasonId)         // Filtrar por temporada

// Atributos calculados
$standing->effectiveness               // Porcentaje de efectividad (0-100)
$standing->goals_for_average          // Promedio de goles a favor
$standing->goals_against_average      // Promedio de goles en contra
```

### 2. Servicio: `StandingsService.php`

#### Métodos principales:

**`recalculateStandings(Season $season)`**
- Limpia standings existentes
- Crea standings para todos los equipos de la temporada
- Procesa todos los partidos completados
- Actualiza posiciones

**`updateStandingsForFixture(Fixture $fixture)`**
- **Se ejecuta automáticamente cuando un partido se completa**
- Actualiza estadísticas de ambos equipos
- Calcula ganador y asigna puntos
- Actualiza racha de resultados (form)
- Recalcula posiciones de toda la tabla

**`initializeStandings(Season $season)`**
- Inicializa standings vacíos para una temporada nueva

### 3. Observer: `FixtureObserver.php`

**Trigger automático** cuando `fixture->status` cambia a `'completed'`:

```php
public function updated(Fixture $fixture): void
{
    if ($fixture->isDirty('status') && $fixture->status === 'completed') {
        // 1. Generar cuotas de partido (2 ingresos)
        GenerateMatchFeesJob::dispatch($fixture)->delay(now()->addMinutes(5));
        
        // 2. Generar pago al árbitro (1 egreso)
        if ($fixture->referee_id) {
            GenerateRefereePaymentsJob::dispatch($fixture)->delay(now()->addMinutes(5));
        }
        
        // 3. Actualizar standings INMEDIATAMENTE ✨
        $this->standingsService->updateStandingsForFixture($fixture);
    }
}
```

### 4. Componente Livewire: `Standings/Index.php`

**Filtros**:
- Liga (dropdown)
- Temporada (dropdown)

**Funcionalidades**:
- Carga automática de standings al seleccionar liga/temporada
- Botón "Recalcular" (solo admin)
- Permisos por roles
- Auto-inicialización si no existen standings

### 5. Vista: `standings/index.blade.php`

**Desktop (Tabla completa)**:
| Pos | Equipo | PJ | PG | PE | PP | GF | GC | DG | Racha | PTS |
|-----|--------|----|----|----|----|----|----|-------|-------|-----|

**Mobile (Cards)**:
```
🥇 1  [Logo] Equipo A                    24 pts
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
PJ: 10    PG-PE-PP: 7-3-0    GF-GC: 25-10
Racha: [W][W][D][W][W]
```

**Características visuales**:
- 🥇🥈🥉 Medallas para top 3
- Colores por fondo para primeras 3 posiciones
- Badges W/D/L con colores (verde/gris/rojo)
- Diferencia de goles con signo + y colores
- Logos de clubes
- Responsive (mobile-first)

---

## 🎯 Flujo de Actualización Automática

```
1. Admin/Referee finaliza partido
   ↓
2. Fixtures/Manage.php llama finishMatch()
   ↓
3. $fixture->status = 'completed'
   $fixture->save()
   ↓
4. FixtureObserver detecta cambio
   ↓
5. StandingsService->updateStandingsForFixture()
   ↓
6. Actualiza stats de ambos equipos:
   - played++
   - goals_for += score
   - goals_against += opponent_score
   - Determina ganador
   - Asigna puntos (3/1/0)
   - Actualiza form (W/D/L)
   ↓
7. Recalcula posiciones de toda la tabla
   ↓
8. ✅ Tabla actualizada en tiempo real
```

---

## 📍 Rutas

```php
// Accesible para todos los roles autenticados
Route::get('/standings', StandingsIndex::class)->name('standings.index');
```

**URL**: `/standings`

**Roles con acceso**: 
- ✅ admin
- ✅ league_manager
- ✅ coach
- ✅ referee
- ✅ player

---

## 🎨 UI/UX

### Estados de la vista

**1. Sin datos**
```
📊
No hay datos de posiciones
Esta temporada aún no tiene partidos completados.
[Inicializar Tabla] (solo admin)
```

**2. Sin filtros seleccionados**
```
🏆
Selecciona una liga y temporada
Usa los filtros de arriba para ver la tabla de posiciones
```

**3. Con datos**
- Tabla completa ordenada
- Medallas y colores para destacar posiciones
- Racha de resultados visual
- Leyenda explicativa al final

### Acciones disponibles

**Admin**:
- ✅ Ver standings
- ✅ Recalcular standings (botón "Recalcular")

**Otros roles**:
- ✅ Ver standings (solo lectura)

---

## 🔄 Ejemplo de Cálculo

### Antes del partido
```
Equipo A: 15 pts (5 PJ - 5 PG - 0 PE - 0 PP - 15 GF - 5 GC)
Equipo B: 12 pts (5 PJ - 4 PG - 0 PE - 1 PP - 12 GF - 8 GC)
```

### Partido: Equipo A 2 - 3 Equipo B

### Después del partido
```
Equipo A: 15 pts (6 PJ - 5 PG - 0 PE - 1 PP - 17 GF - 8 GC) [Form: W W W W L]
Equipo B: 15 pts (6 PJ - 5 PG - 0 PE - 1 PP - 15 GF - 10 GC) [Form: W W W L W]
```

**Equipo B** queda por encima porque tiene mejor diferencia de goles:
- Equipo B: +5
- Equipo A: +9 → Equipo A sigue primero

---

## 🧪 Testing

### Casos de prueba

1. **Inicializar standings**
   - Crear temporada con equipos
   - Ejecutar `initializeStandings()`
   - Verificar que todos los equipos tienen standing con 0s

2. **Actualizar standings al completar partido**
   - Crear fixture con score
   - Cambiar status a 'completed'
   - Verificar que standings se actualizaron correctamente

3. **Recalcular standings completos**
   - Crear múltiples fixtures completados
   - Ejecutar `recalculateStandings()`
   - Verificar que posiciones sean correctas

4. **Ordenamiento correcto**
   - Crear standings con diferentes puntos/goles
   - Verificar orden: puntos > diferencia > goles_for

---

## 📊 Estadísticas del Sistema

**Archivos creados**:
- `database/migrations/2025_10_02_171957_create_standings_table.php` (40 líneas)
- `app/Models/Standing.php` (100 líneas)
- `app/Services/StandingsService.php` (240 líneas)
- `app/Livewire/Standings/Index.php` (150 líneas)
- `resources/views/livewire/standings/index.blade.php` (300 líneas)

**Archivos modificados**:
- `app/Observers/FixtureObserver.php` (integración)
- `routes/web.php` (ruta)
- `resources/views/layouts/partials/sidebar-nav.blade.php` (4 menús)

**Total**: 830+ líneas de código nuevo

---

## 🚀 Próximas Mejoras

### FASE 2
- [ ] Standings por grupo (si hay grupos en la liga)
- [ ] Histórico de posiciones (gráfico de evolución)
- [ ] Comparador de equipos
- [ ] Exportar a PDF/Excel

### FASE 3
- [ ] Predicciones basadas en racha
- [ ] Probabilidades de campeonato
- [ ] Máximos goleadores integrados

---

## 🎓 Notas Técnicas

### Criterios de desempate

1. **Puntos**
2. **Diferencia de goles**
3. **Goles a favor**
4. **ID del registro** (para consistencia)

### Form (Racha)

- Almacena últimos 5 resultados
- Formato: `WWDLW` (Victoria, Victoria, Empate, Derrota, Victoria)
- Se actualiza automáticamente al completar partido
- Útil para visualizar tendencia del equipo

### Performance

- Índice compuesto en `(season_id, points, goal_difference)` para consultas rápidas
- Transacciones DB para actualización atómica
- Logs para debugging

---

**Creado**: 2 de octubre de 2025  
**Versión**: 1.0  
**Estado**: ✅ Producción
