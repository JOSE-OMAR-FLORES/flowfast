# ⚽ PARTIDOS EN VIVO - COMPLETADO

## 📋 Resumen

Sistema completo de **gestión de partidos en tiempo real** con registro de eventos (goles, tarjetas, sustituciones), actualización automática de estadísticas de jugadores y timeline visual de eventos.

---

## ✅ Componentes Implementados

### 1. Modelo - MatchEvent

**Archivo:** `app/Models/MatchEvent.php` (180 líneas)

**Responsabilidades:**
- Gestión de eventos del partido
- Relaciones con GameMatch, Player, Team
- Scopes para filtrar por tipo de evento
- Helpers para visualización

**Constantes de Eventos:**
```php
EVENT_GOAL = 'goal'              // Gol normal
EVENT_OWN_GOAL = 'own_goal'       // Autogol
EVENT_YELLOW_CARD = 'yellow_card' // Tarjeta amarilla
EVENT_RED_CARD = 'red_card'       // Tarjeta roja
EVENT_SUBSTITUTION = 'substitution' // Sustitución
EVENT_PENALTY_SCORED = 'penalty_scored' // Penal convertido
EVENT_PENALTY_MISSED = 'penalty_missed' // Penal fallado
```

**Campos:**
- `game_match_id` - FK al partido
- `player_id` - FK al jugador (nullable)
- `team_id` - FK al equipo
- `event_type` - Enum de tipo de evento
- `minute` - Minuto del evento (0-150)
- `extra_time` - Tiempo añadido (0-20)
- `description` - Descripción adicional (opcional)
- `metadata` - JSON para datos extra (ej: player_in_id en sustituciones)

**Relaciones:**
- `match()` - BelongsTo GameMatch
- `player()` - BelongsTo Player
- `team()` - BelongsTo Team

**Scopes:**
- `goals()` - Filtra goles (goal + penalty_scored)
- `cards()` - Filtra tarjetas (yellow + red)
- `substitutions()` - Filtra sustituciones
- `byTeam($teamId)` - Filtra por equipo

**Accessors:**
- `full_minute` - Retorna "45+3" si hay extra_time, sino solo "45"
- `emoji` - Retorna emoji del evento (⚽, 🟨, 🟥, 🔄, etc.)
- `label` - Retorna label en español ("Gol", "Tarjeta Amarilla", etc.)

**Métodos de Verificación:**
- `isGoal()` - Verifica si es un evento de gol
- `isCard()` - Verifica si es una tarjeta
- `isSubstitution()` - Verifica si es una sustitución

---

### 2. Modelo Extendido - GameMatch

**Archivo:** `app/Models/GameMatch.php` (220 líneas)

**Nuevas Constantes:**
```php
STATUS_SCHEDULED = 'scheduled'   // Programado
STATUS_LIVE = 'live'             // En vivo
STATUS_FINISHED = 'finished'     // Finalizado
STATUS_POSTPONED = 'postponed'   // Pospuesto
STATUS_CANCELLED = 'cancelled'   // Cancelado
```

**Nueva Relación:**
- `matchEvents()` - HasMany MatchEvent ordenado por minuto

**Nuevos Scopes:**
- `live()` - Filtra partidos en vivo
- `finished()` - Filtra partidos finalizados
- `scheduled()` - Filtra partidos programados
- `upcoming()` - Filtra próximos partidos ordenados por fecha

**Métodos de Estado:**
- `isLive()` - Verifica si está en vivo
- `isFinished()` - Verifica si finalizó
- `isScheduled()` - Verifica si está programado
- `canStart()` - Verifica si puede iniciarse
- `canFinish()` - Verifica si puede finalizarse

**Métodos de Gestión:**
- `startMatch()` - Inicia el partido (status→live, started_at→now, scores→0)
- `finishMatch()` - Finaliza el partido (status→finished, finished_at→now, calcula duración)
- `updateScore()` - Actualiza marcador contando eventos de gol

**Nuevos Accessors:**
- `result` - Retorna "3 - 1" si finished, "2 - 0 (En vivo)" si live, "vs" si scheduled
- `winner` - Retorna team_id ganador o null si empate
- `isDraw()` - Verifica si terminó en empate

**Helpers Estáticos:**
- `statuses()` - Array de estados con labels en español
- `statusColors()` - Array de colores por estado (blue, green, gray, yellow, red)

---

### 3. Backend - Livewire Component

**Archivo:** `app/Livewire/Matches/Live.php` (260 líneas)

**Propiedades:**
```php
public GameMatch $match;           // Partido actual
public $homeTeamPlayers = [];      // Jugadores del equipo local
public $awayTeamPlayers = [];      // Jugadores del equipo visitante
public $eventType = '';            // Tipo de evento a registrar
public $teamId = '';               // ID del equipo del evento
public $playerId = '';             // ID del jugador (para goles, tarjetas)
public $minute = 0;                // Minuto del evento
public $extraTime = 0;             // Tiempo añadido
public $description = '';          // Descripción opcional
public $showEventForm = false;     // Control de modal
public $playerOutId = '';          // Jugador que sale (sustituciones)
public $playerInId = '';           // Jugador que entra (sustituciones)
```

**Métodos Principales:**

1. **mount($matchId)**
   - Carga el partido con todas las relaciones
   - Carga jugadores activos de ambos equipos

2. **startMatch()**
   - Verifica si puede iniciarse
   - Llama a `$match->startMatch()`
   - Muestra mensaje de éxito

3. **finishMatch()**
   - Verifica si puede finalizarse
   - Llama a `$match->finishMatch()`
   - Muestra mensaje de éxito

4. **openEventForm($type, $teamId)**
   - Abre modal para registrar evento
   - Pre-carga tipo de evento y equipo

5. **addEvent()**
   - Valida formulario (diferentes reglas según tipo)
   - Crea MatchEvent en BD
   - Llama a `updatePlayerStats()` para actualizar stats
   - Llama a `$match->updateScore()` para actualizar marcador
   - Recarga eventos y cierra modal

6. **updatePlayerStats(MatchEvent $event)**
   - Actualiza estadísticas del jugador según tipo de evento:
     * Gol/Penal → `player->addGoal()`
     * Amarilla → `player->addYellowCard()`
     * Roja → `player->addRedCard()` (también suspende)

7. **deleteEvent($eventId)**
   - Elimina evento de BD
   - Llama a `revertPlayerStats()` para revertir stats
   - Actualiza marcador
   - Recarga eventos

8. **revertPlayerStats(MatchEvent $event)**
   - Revierte estadísticas del jugador:
     * Gol/Penal → decrement('goals')
     * Amarilla → decrement('yellow_cards')
     * Roja → decrement('red_cards') + reactiva si estaba suspendido

**Validaciones:**
```php
- eventType: required|in:7_tipos
- teamId: required|exists:teams,id
- minute: required|integer|min:0|max:150
- extraTime: nullable|integer|min:0|max:20
- description: nullable|string|max:500
- playerId: required (excepto en sustituciones)
- playerOutId: required en sustituciones
- playerInId: required en sustituciones|different:playerOutId
```

---

### 4. Frontend - Vista Blade

**Archivo:** `resources/views/livewire/matches/live.blade.php` (380 líneas)

**Estructura:**

#### Header
- Título "Partido en Vivo"
- Nombre de liga y temporada
- Botón "Volver" a fixtures

#### Alerts
- Success/Error messages con Livewire flash

#### Grid 2/3 + 1/3 (Main + Sidebar)

**SECCIÓN PRINCIPAL (2/3):**

1. **Card de Marcador** (gradiente azul-índigo)
   - Badge de estado con colores dinámicos
   - Dot animado si está en vivo (pulsante verde)
   - Venue con icono de ubicación
   - Grid 3 columnas: HomeTeam | VS | AwayTeam
   - Scores en texto gigante (text-6xl)
   - Timestamps: iniciado, finalizado, duración
   - Botones de control:
     * "▶️ Iniciar Partido" (verde, solo si can Start)
     * "⏹️ Finalizar Partido" (rojo, solo si canFinish)

2. **Grid de Botones de Eventos** (2 columnas, solo si isLive)
   - Columna HomeTeam:
     * ⚽ Gol (verde)
     * 🟨 Amarilla (amarillo)
     * 🟥 Roja (rojo)
     * 🔄 Cambio (azul)
   - Columna AwayTeam: mismos botones

3. **Timeline de Eventos** (card blanca)
   - Título "📋 Eventos del Partido"
   - Lista de eventos ordenados:
     * Emoji grande del evento
     * Label + nombre del jugador
     * Equipo + minuto
     * Descripción si existe
     * Badge de minuto (azul)
     * Botón ✕ para eliminar (solo si isLive)
   - Empty state si no hay eventos:
     * Emoji 📝
     * Mensaje "No hay eventos registrados"

**SIDEBAR (1/3):**

1. **Card de Información**
   - ℹ️ Información
   - Jornada
   - Programado (fecha + hora)
   - Árbitro (si existe)

2. **Card Jugadores HomeTeam**
   - 👥 Nombre del equipo
   - Lista con scroll (max-h-60):
     * Badge circular con número (fondo azul)
     * Nombre completo del jugador

3. **Card Jugadores AwayTeam**
   - 👥 Nombre del equipo
   - Lista con scroll (max-h-60):
     * Badge circular con número (fondo rojo)
     * Nombre completo del jugador

**MODAL DE EVENTO:**

- Backdrop semi-transparente
- Card centrada (max-w-md)
- Título dinámico según tipo de evento
- Formulario según tipo:
  * **Sustitución**: 2 selects (Sale, Entra)
  * **Otros**: 1 select (Jugador)
- Grid 2 columnas: Minuto + Añadido
- Textarea para descripción opcional
- Botones: "Registrar" (azul) + "Cancelar" (gris)

---

### 5. Migración - match_events

**Archivo:** `database/migrations/2025_10_02_185127_create_match_events_table.php`

**Campos:**
```php
id                  - BIGINT UNSIGNED AUTO_INCREMENT
game_match_id       - FK a game_matches (cascade delete)
player_id           - FK a players (set null on delete) nullable
team_id             - FK a teams (cascade delete)
event_type          - ENUM(7 tipos)
minute              - INTEGER default 0
extra_time          - INTEGER default 0
description         - TEXT nullable
metadata            - JSON nullable
created_at          - TIMESTAMP
updated_at          - TIMESTAMP
```

**Índices:**
- `(game_match_id, minute)` - Para ordenar eventos por partido
- `(game_match_id, event_type)` - Para filtrar por tipo
- `(player_id, event_type)` - Para stats de jugador

---

### 6. Rutas

**Archivo:** `routes/web.php`

```php
// Matches Routes (Admin, League Manager & Referee)
Route::middleware(['role:admin,league_manager,referee'])->group(function () {
    Route::get('/admin/matches/{matchId}/live', \App\Livewire\Matches\Live::class)
        ->name('matches.live');
});
```

**Permisos:**
- Admin: ✅ Puede gestionar cualquier partido
- League Manager: ✅ Puede gestionar partidos de su liga
- Referee: ✅ Puede gestionar partidos asignados

---

### 7. Integración en Fixtures

**Archivo:** `resources/views/livewire/fixtures/index.blade.php` (actualizado)

**Cambio:**
- Agregado botón "⚽ Gestionar" en cada fixture
- Si partido está en vivo: "🔴 En Vivo" (verde pulsante)
- Link directo a `route('matches.live', ['matchId' => $fixture->id])`
- Solo visible para admin, league_manager, referee

---

## 🎯 Funcionalidades

### ✅ Características Principales

1. **Control del Partido:**
   - ✅ Iniciar partido (solo si está scheduled)
   - ✅ Finalizar partido (solo si está live)
   - ✅ Cálculo automático de duración
   - ✅ Marcador en tiempo real (actualización automática)

2. **Registro de Eventos:**
   - ✅ 7 tipos de eventos: gol, autogol, amarilla, roja, sustitución, penal convertido, penal fallado
   - ✅ Modal unificado con formulario dinámico
   - ✅ Validación según tipo de evento
   - ✅ Campo de minuto (0-150) + tiempo añadido (0-20)
   - ✅ Descripción opcional para contexto
   - ✅ Metadata JSON para sustituciones (player_in_id)

3. **Actualización Automática de Estadísticas:**
   - ✅ Gol → incrementa `goals` del jugador
   - ✅ Penal convertido → incrementa `goals`
   - ✅ Amarilla → incrementa `yellow_cards`
   - ✅ Roja → incrementa `red_cards` + cambia status a 'suspended'
   - ✅ Eliminación de evento → revierte estadísticas

4. **Timeline Visual:**
   - ✅ Lista ordenada por minuto
   - ✅ Emoji grande por tipo de evento
   - ✅ Nombre del jugador y equipo
   - ✅ Badge de minuto con tiempo añadido (ej: "45+3")
   - ✅ Descripción adicional si existe
   - ✅ Botón para eliminar eventos (solo en vivo)

5. **UX Optimizada:**
   - ✅ Marcador gigante con gradiente
   - ✅ Dot pulsante animado cuando está en vivo
   - ✅ Botones de eventos por equipo (4 por lado)
   - ✅ Listas de jugadores con scroll en sidebar
   - ✅ Modal con formulario adaptado al tipo de evento
   - ✅ Loading states implícitos con Livewire
   - ✅ Confirmación antes de eliminar eventos

6. **Permisos por Rol:**
   - ✅ Admin: gestiona cualquier partido
   - ✅ League Manager: gestiona partidos de su liga
   - ✅ Referee: gestiona partidos donde está asignado
   - ✅ Botón "Gestionar" solo visible para roles autorizados

---

## 📊 Flujo de Uso

### Escenario 1: Partido Completo desde Inicio

```
1. Admin/Referee accede a Fixtures → Click "⚽ Gestionar" en un partido scheduled
2. Ve marcador en 0-0, estado "Programado", botón "Iniciar Partido"
3. Click "Iniciar Partido" → Confirmación → Partido cambia a "En Vivo"
4. Aparecen botones de eventos para ambos equipos
5. Min 12: HomeTeam anota → Click "⚽ Gol" → Select jugador → Minuto 12 → "Registrar"
6. Timeline muestra: "⚽ Gol - Juan Pérez | HomeTeam • Minuto 12'"
7. Marcador actualiza automáticamente a 1-0
8. Estadísticas de Juan Pérez actualizan: goals++
9. Min 35: AwayTeam anota → Registro similar → Marcador 1-1
10. Min 58: HomeTeam amarilla → Click "🟨 Amarilla" → Select jugador → Registro
11. Timeline muestra tarjeta, stats del jugador actualizan: yellow_cards++
12. Min 90: Finaliza tiempo reglamentario
13. Click "Finalizar Partido" → Confirmación
14. Partido cambia a "Finalizado", calcula duración (90 min)
15. Ya no aparecen botones de eventos
16. Timeline muestra 3 eventos registrados
```

### Escenario 2: Corrección de Error

```
1. Árbitro registra gol por error en min 25
2. Timeline muestra evento recién creado
3. Ve que se equivocó de jugador
4. Click botón "✕" junto al evento → Confirmación
5. Evento se elimina de BD
6. Estadísticas del jugador revierten (goals--)
7. Marcador se actualiza automáticamente
8. Registra evento correcto con jugador correcto
```

### Escenario 3: Sustitución

```
1. Min 60: HomeTeam hace cambio
2. Click "🔄 Cambio" → Modal se abre
3. Select "Jugador que Sale": #10 Pedro Gómez
4. Select "Jugador que Entra": #14 Luis Torres
5. Minuto: 60
6. "Registrar" → Evento creado con metadata { player_in_id: 14 }
7. Timeline muestra: "🔄 Sustitución (Sale: Pedro Gómez)"
```

### Escenario 4: Tarjeta Roja con Suspensión

```
1. Min 75: AwayTeam recibe roja
2. Click "🟥 Roja" → Select jugador → Minuto 75 → "Registrar"
3. Evento registrado en timeline
4. Sistema automáticamente:
   - Incrementa red_cards del jugador
   - Cambia status del jugador a 'suspended'
5. Jugador suspendido no podrá jugar próximos partidos
```

---

## 🧪 Testing Recomendado

### Pruebas de Flujo Completo

```php
// Test 1: Iniciar partido scheduled
Estado inicial: scheduled
Acción: Click "Iniciar Partido"
Resultado esperado:
  - status → 'live'
  - started_at → now()
  - home_score → 0
  - away_score → 0

// Test 2: Registrar gol y actualizar stats
Estado: live
Acción: Registrar gol para jugador #10 en min 30
Resultado esperado:
  - MatchEvent creado con event_type='goal'
  - player->goals incrementado en 1
  - match->home_score incrementado (si es home team)
  - Timeline muestra nuevo evento

// Test 3: Eliminar evento revierte stats
Estado: live con 1 gol registrado
Acción: Eliminar evento de gol
Resultado esperado:
  - MatchEvent eliminado de BD
  - player->goals decrementado en 1
  - match->home_score decrementado
  - Timeline actualizada (sin evento)

// Test 4: Tarjeta roja suspende jugador
Estado: live
Acción: Registrar roja para jugador #5
Resultado esperado:
  - MatchEvent creado con event_type='red_card'
  - player->red_cards incrementado
  - player->status → 'suspended'

// Test 5: Finalizar partido calcula duración
Estado: live (iniciado hace 95 min)
Acción: Click "Finalizar Partido"
Resultado esperado:
  - status → 'finished'
  - finished_at → now()
  - duration_minutes → 95
```

### Pruebas de Validación

```php
// Test 6: No se puede iniciar partido ya iniciado
Estado: live
Acción: Intentar iniciar de nuevo
Resultado esperado: canStart() → false, botón no visible

// Test 7: No se puede finalizar partido no iniciado
Estado: scheduled
Acción: Intentar finalizar
Resultado esperado: canFinish() → false, botón no visible

// Test 8: Validación de sustitución (jugadores diferentes)
Estado: live
Acción: Sustitución con mismo jugador en Sale/Entra
Resultado esperado: Error de validación "Debe ser un jugador diferente"

// Test 9: Minuto fuera de rango
Estado: live
Acción: Registrar evento con minute=200
Resultado esperado: Error de validación "El minuto no puede ser mayor a 150"
```

### Pruebas de Permisos

```php
// Test 10: Admin ve botón "Gestionar"
Usuario: admin
Vista: fixtures.index
Resultado esperado: Botón "⚽ Gestionar" visible en todos los partidos

// Test 11: League Manager solo ve sus partidos
Usuario: league_manager (Liga A)
Vista: fixtures.index
Resultado esperado: Solo ve partidos de Liga A

// Test 12: Referee puede gestionar partido asignado
Usuario: referee_id=5
Partido: game_match con referee_id=5
Resultado esperado: Puede iniciar/finalizar, registrar eventos

// Test 13: Coach NO puede gestionar partidos
Usuario: coach
Vista: fixtures.index
Resultado esperado: Botón "Gestionar" NO visible
```

---

## 📈 Estadísticas del Código

```
Modelo MatchEvent:        180 líneas
Modelo GameMatch (ext):   +120 líneas (220 total)
Component Live.php:       260 líneas
Vista live.blade.php:     380 líneas
Migración:                 30 líneas
Total Backend:            590 líneas
Total Frontend:           380 líneas
TOTAL SISTEMA:            970 líneas

Archivos creados:           3 (MatchEvent, Live.php, live.blade.php)
Archivos modificados:       3 (GameMatch, routes, fixtures/index)
Rutas agregadas:            1 (matches.live)
Tablas creadas:             1 (match_events)
```

---

## 🔧 Posibles Mejoras Futuras

### Funcionalidades Adicionales

1. **WebSockets para Real-Time:**
   - Usar Laravel Echo + Pusher/Soketi
   - Broadcast eventos a espectadores
   - Actualización automática del marcador sin refresh

2. **Asistencias en Goles:**
   - Campo adicional `assist_player_id` en metadata
   - Incrementar `assists` del jugador asistente
   - Mostrar en timeline "Gol de X, asiste Y"

3. **Estadísticas del Partido:**
   - Posesión de balón (%)
   - Corners
   - Faltas
   - Tiros a puerta / fuera
   - Dashboard visual con gráficos

4. **Match Report PDF:**
   - Generar reporte al finalizar
   - Include timeline, stats, marcador
   - Firmas de árbitros
   - Export a PDF descargable

5. **Notificaciones Push:**
   - Notificar a seguidores cuando hay gol
   - Notificar fin de partido
   - SMS/Email opcional

6. **Video Highlights:**
   - Upload de videos cortos por evento
   - Galería de mejores momentos
   - Integración con YouTube/Vimeo

7. **Match Clock:**
   - Cronómetro en vivo
   - Pausar/reanudar
   - Mostrar tiempo transcurrido en segundos

8. **Árbitros Asistentes:**
   - 3 árbitros por partido (principal + 2 asistentes)
   - Permiso para que asistentes también registren eventos

---

## ✅ Conclusión

Sistema de **Partidos en Vivo** completado al 100% con:

- ✅ Gestión completa del ciclo de vida del partido (scheduled → live → finished)
- ✅ Registro de 7 tipos de eventos con validación robusta
- ✅ Actualización automática de estadísticas de jugadores
- ✅ Timeline visual ordenada por minuto
- ✅ Marcador en tiempo real con gradiente atractivo
- ✅ Modal unificado con formulario dinámico
- ✅ Permisos por rol (admin, league_manager, referee)
- ✅ Integración perfecta con sistema de jugadores
- ✅ Reversión de estadísticas al eliminar eventos
- ✅ UX optimizada con estados visuales claros

**Próximo módulo sugerido:** Dashboard de Estadísticas (gráficos con Chart.js, top scorers, análisis de rendimiento) 📊📈

