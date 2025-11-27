# 🚀 GUÍA RÁPIDA: ¿Dónde ir para probar Fixtures?

## 📍 **URLs Directas para Probar**

### 1️⃣ **Ver Listado de Fixtures** (Vacío inicialmente)
```
http://localhost/fixtures
```
- **Qué hacer**: Solo observa la interfaz vacía
- **Qué ver**: Filtros, botón "Generar Fixtures", mensaje de tabla vacía

---

### 2️⃣ **Generar Fixtures Automáticamente** (Round Robin)
```
http://localhost/fixtures/generate
```
- **Qué hacer**: 
  1. Seleccionar Liga: **"Liga Premier de Fútbol"**
  2. Seleccionar Temporada: **"Temporada 2024"**
  3. Seleccionar Cancha: **"Estadio Principal"**
  4. Fecha Inicio: **2025-11-01**
  5. Dejar solo: ✅ **Round Robin**
  6. Click: **"Generar Vista Previa"**
  7. Revisar los partidos generados
  8. Click: **"Confirmar y Crear Fixtures"**

- **Qué ver**: 
  - Vista previa con 3 jornadas
  - 6 partidos total (4 equipos)
  - Resumen: "Total de Jornadas: 3, Total de Partidos: 6"

---

### 3️⃣ **Ver Fixtures Generados** (Con Datos)
```
http://localhost/fixtures
```
- **Qué hacer**: Después de generar, vuelve a esta URL
- **Qué ver**: 
  - Tabla con 6 partidos
  - Badges de jornadas (J1, J2, J3)
  - Equipos con sus colores (rojo, dorado, etc.)
  - Estado "Programado" en azul
  - Fechas y horarios

---

## 🎯 **Flujo de Prueba Completo (5 minutos)**

```
┌─────────────────────────────────────────────────────────────────┐
│                     INICIO                                      │
│                       ↓                                         │
│  1. LOGIN como ADMIN                                            │
│     └→ http://localhost/login                                   │
│        Email: test@example.com (tu admin)                       │
│        Password: password                                       │
│                       ↓                                         │
│  2. IR A FIXTURES (VACÍO)                                       │
│     └→ http://localhost/fixtures                                │
│        ✓ Ver interfaz vacía                                     │
│        ✓ Ver botón "Generar Fixtures"                           │
│                       ↓                                         │
│  3. CLICK EN "GENERAR FIXTURES"                                 │
│     └→ http://localhost/fixtures/generate                       │
│        ✓ Seleccionar Liga Premier                               │
│        ✓ Seleccionar Temporada 2024                             │
│        ✓ Seleccionar Cancha (opcional)                          │
│        ✓ Fecha: 2025-11-01                                      │
│        ✓ Round Robin activado                                   │
│                       ↓                                         │
│  4. CLICK "GENERAR VISTA PREVIA"                                │
│     └→ Esperar 1-2 segundos                                     │
│        ✓ Ver 3 jornadas                                         │
│        ✓ Ver 6 partidos                                         │
│        ✓ Verificar emparejamientos:                             │
│          • J1: Tigres vs Águilas, Leones vs Pumas               │
│          • J2: Tigres vs Leones, Águilas vs Pumas               │
│          • J3: Tigres vs Pumas, Águilas vs Leones               │
│                       ↓                                         │
│  5. CLICK "CONFIRMAR Y CREAR FIXTURES"                          │
│     └→ Confirmar en diálogo                                     │
│        ✓ Esperar creación (1-2 seg)                             │
│        ✓ Redirige a /fixtures                                   │
│                       ↓                                         │
│  6. VER FIXTURES GENERADOS                                      │
│     └→ http://localhost/fixtures                                │
│        ✓ Mensaje: "6 fixtures generados exitosamente"           │
│        ✓ Tabla con 6 filas                                      │
│        ✓ Badges de colores en equipos                           │
│        ✓ Estados "Programado" en azul                           │
│                       ↓                                         │
│  7. PROBAR FILTROS                                              │
│     └→ Buscar "Tigres"                                          │
│        ✓ Ver solo 3 partidos                                    │
│     └→ Filtro Estado: "Completado"                              │
│        ✓ Ver mensaje vacío (ninguno completado)                 │
│     └→ Ordenar por "Fecha"                                      │
│        ✓ Ver flechita de ordenamiento                           │
│                       ↓                                         │
│  8. PROBAR RESPONSIVE                                           │
│     └→ F12 > Toggle Device Toolbar > iPhone 12 Pro             │
│        ✓ Ver cards en lugar de tabla                            │
│        ✓ Badges con colores visibles                            │
│        ✓ Sin scroll horizontal                                  │
│                       ↓                                         │
│                    ✅ ÉXITO                                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎨 **Navegación por Sidebar**

```
┌────────────────────────────────────┐
│ FlowFast SaaS              [≡]     │
├────────────────────────────────────┤
│                                    │
│  🏠 Dashboard                      │
│  🏆 Ligas                          │
│  📅 Temporadas                     │
│  👥 Equipos                        │
│  📋 Calendario  ← AQUÍ ESTÁ       │
│  ⚽ Partidos                        │
│  📧 Invitaciones                   │
│  📊 Reportes                       │
│  ⚙️ Configuración                  │
│                                    │
└────────────────────────────────────┘
```

**Ruta en sidebar:**
1. Inicia sesión
2. Sidebar izquierdo
3. Click en: **"Calendario"** (icono 📋)
4. Listo, estás en `/fixtures`

---

## 📱 **Probando en Mobile**

### Opción 1: DevTools (Recomendado)
```
1. F12 (Abrir DevTools)
2. Ctrl + Shift + M (Toggle Device Toolbar)
3. Seleccionar: iPhone 12 Pro (390 × 844)
4. Navegar a: http://localhost/fixtures
5. Verificar: Cards verticales
```

### Opción 2: Responsive Mode Manual
```
1. Reducir ventana del navegador manualmente
2. Hacer ventana de ~400px de ancho
3. Navegar a: http://localhost/fixtures
4. Verificar: Cards verticales
```

---

## ⚠️ **Errores Comunes y Soluciones Rápidas**

### Error 1: "Undefined type 'App\Models\Fixture'"
**Solución**: Ya está resuelto, modelo creado ✅

### Error 2: Fixtures no aparecen después de generar
**Verificar en terminal**:
```bash
php artisan tinker --execute="dump(DB::table('fixtures')->count());"
```
Debe retornar 6 (o el número de fixtures generados)

### Error 3: Botón "Generar Fixtures" no aparece
**Causa**: Usuario no es admin ni league_manager
**Solución**: Inicia sesión con cuenta admin

### Error 4: "No hay canchas disponibles"
**Solución**: Ya ejecutaste `seed_venues.php` ✅

---

## 🎯 **Checklist de 30 Segundos**

Marca cada uno después de probar:

- [ ] ✅ Login como admin funciona
- [ ] ✅ Sidebar muestra "Calendario"
- [ ] ✅ `/fixtures` carga sin errores (vacío)
- [ ] ✅ Botón "Generar Fixtures" visible
- [ ] ✅ `/fixtures/generate` carga sin errores
- [ ] ✅ Formulario muestra ligas y temporadas
- [ ] ✅ Vista previa genera 6 partidos
- [ ] ✅ Confirmación crea fixtures en BD
- [ ] ✅ Tabla muestra 6 fixtures con colores
- [ ] ✅ Mobile muestra cards (no tabla)

---

## 🎬 **Video Tutorial (Paso a Paso)**

### Minuto 0:00 - Login
```
1. Abrir: http://localhost/login
2. Email: test@example.com
3. Password: password
4. Click: "Log In"
```

### Minuto 0:30 - Navegar a Fixtures
```
5. Sidebar izquierdo
6. Click: "Calendario"
7. URL cambia a: /fixtures
8. Ver: Página vacía con mensaje
```

### Minuto 1:00 - Generar Fixtures
```
9. Click: "Generar Fixtures" (botón verde)
10. Seleccionar: "Liga Premier de Fútbol"
11. Seleccionar: "Temporada 2024"
12. Fecha: 2025-11-01
13. Dejar: Round Robin activado
14. Click: "Generar Vista Previa"
```

### Minuto 2:00 - Ver Preview
```
15. Esperar generación (1-2 seg)
16. Scroll en vista previa
17. Verificar: 3 jornadas, 6 partidos
18. Leer resumen: "Total de Partidos: 6"
```

### Minuto 2:30 - Confirmar
```
19. Click: "Confirmar y Crear Fixtures"
20. Diálogo: Click "Confirmar"
21. Esperar redirección
22. Ver mensaje: "6 fixtures generados exitosamente"
```

### Minuto 3:00 - Verificar Fixtures
```
23. Ver tabla con 6 filas
24. Verificar columnas: Jornada, Fecha, Partido, etc.
25. Ver badges con colores de equipos
26. Ver estado "Programado" en azul
```

### Minuto 3:30 - Probar Filtros
```
27. Buscar: "Tigres"
28. Ver: Solo 3 resultados
29. Borrar búsqueda
30. Ver: 6 resultados de nuevo
```

### Minuto 4:00 - Mobile
```
31. F12 > Device Toolbar
32. iPhone 12 Pro
33. Ver: Cards en lugar de tabla
34. Scroll vertical
35. ✅ PRUEBA COMPLETA
```

---

## 🎁 **Bonus: Generar Doble Ronda**

Si quieres probar doble ronda (ida y vuelta):

```
1. Ir a: /fixtures/generate
2. Seleccionar misma liga/temporada
3. Marcar: ✅ Doble Ronda (Ida y Vuelta)
4. Click: "Generar Vista Previa"
5. Verificar: 6 jornadas, 12 partidos
6. Jornadas 1-3: Partidos de IDA
7. Jornadas 4-6: Partidos de VUELTA (invertidos)
8. NO CONFIRMAR (para no duplicar datos)
```

---

## 📞 **¿Necesitas Ayuda?**

Si algo no funciona:
1. Revisar archivo: `PRUEBA-FIXTURES.md` (guía completa)
2. Revisar logs: `storage/logs/laravel.log`
3. Verificar migraciones: `php artisan migrate:status`
4. Verificar datos: `php artisan tinker` → `Fixture::count()`

---

**¡Disfruta probando el sistema de Fixtures! 🎉**
