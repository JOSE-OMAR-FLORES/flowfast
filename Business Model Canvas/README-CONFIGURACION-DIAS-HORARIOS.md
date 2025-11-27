# 📅 Configuración de Días y Horarios de Partidos

## 🎯 ¿Dónde se Configura?

La configuración de **días de juego** y **horarios** se hace en cada **Temporada** (Season).

---

## 📊 Campos de Configuración

### 1. `game_days` - Días de la Semana
Define **qué días** se juegan los partidos.

**Formato aceptado:**
- **Palabras en inglés**: `["monday", "wednesday", "friday"]`
- **Números (0-6)**: `[1, 3, 5]` donde:
  - `0` = Domingo (Sunday)
  - `1` = Lunes (Monday)
  - `2` = Martes (Tuesday)
  - `3` = Miércoles (Wednesday)
  - `4` = Jueves (Thursday)
  - `5` = Viernes (Friday)
  - `6` = Sábado (Saturday)

**Ejemplo actual:**
```json
{
  "game_days": ["wednesday", "saturday"]
}
```
Significa: Los partidos se juegan los **Miércoles y Sábados**.

---

### 2. `match_times` - Horarios
Define **a qué horas** se juegan los partidos.

**Formato:** Array de strings en formato HH:MM (24 horas)

**Ejemplo actual:**
```json
{
  "match_times": ["18:00", "19:30", "21:00"]
}
```
Significa: Los partidos pueden ser a las **6:00 PM, 7:30 PM o 9:00 PM**.

---

### 3. `daily_matches` - Partidos por Día
Define **cuántos partidos** se pueden jugar en un mismo día.

**Ejemplo actual:**
```json
{
  "daily_matches": 3
}
```
Significa: Máximo **3 partidos por día**.

---

## 🔄 ¿Cómo Funciona el Algoritmo?

### Distribución de Fechas

El algoritmo **alterna** entre los días configurados:

**Ejemplo con 5 equipos (10 partidos en Round Robin simple):**

Si configuras:
- **Días**: Martes, Jueves, Sábado (`[2, 4, 6]`)
- **Partidos por día**: 2

**Distribución:**
- **Jornada 1** (2 partidos) → **Martes** (fecha inicial)
- **Jornada 2** (2 partidos) → **Jueves** (2 días después)
- **Jornada 3** (2 partidos) → **Sábado** (2 días después)
- **Jornada 4** (2 partidos) → **Martes** (siguiente semana)
- **Jornada 5** (2 partidos) → **Jueves** (siguiente semana)

### Distribución de Horarios

Los horarios se **alternan secuencialmente** entre los partidos:

Si configuras:
```json
["14:00", "16:00", "18:00"]
```

**Asignación:**
- Partido 1 → 14:00
- Partido 2 → 16:00
- Partido 3 → 18:00
- Partido 4 → 14:00 (vuelve a empezar)
- Partido 5 → 16:00
- ...

---

## 📝 Configuración Actual de tus Temporadas

### Temporada Primavera 2024 (Liga Premier de Fútbol)
```json
{
  "game_days": ["wednesday", "saturday"],
  "match_times": ["18:00", "19:30", "21:00"],
  "daily_matches": 3
}
```
**Interpretación:**
- Partidos los **Miércoles y Sábados**
- Horarios: **6:00 PM, 7:30 PM, 9:00 PM**
- Hasta **3 partidos por día**

---

### Temporada Verano 2024 (Liga Nacional de Baloncesto)
```json
{
  "game_days": ["friday", "sunday"],
  "match_times": ["16:00", "17:45", "19:30", "21:15"],
  "daily_matches": 4
}
```
**Interpretación:**
- Partidos los **Viernes y Domingos**
- Horarios: **4:00 PM, 5:45 PM, 7:30 PM, 9:15 PM**
- Hasta **4 partidos por día**

---

### Temporada Apertura 2024 (Liga Juvenil de Voleibol)
```json
{
  "game_days": ["thursday", "saturday"],
  "match_times": ["18:30", "20:00"],
  "daily_matches": 2
}
```
**Interpretación:**
- Partidos los **Jueves y Sábados**
- Horarios: **6:30 PM, 8:00 PM**
- Hasta **2 partidos por día**

---

## 🛠️ ¿Cómo Cambiar la Configuración?

### Opción 1: Desde la Interfaz de Seasons (Recomendado)

Ve a la sección de **Temporadas** y edita los campos:
- `game_days`
- `match_times`
- `daily_matches`

### Opción 2: Directamente en Base de Datos

```sql
UPDATE seasons 
SET 
  game_days = '["2", "4", "6"]',  -- Martes, Jueves, Sábado
  match_times = '["14:00", "16:00", "18:00"]',
  daily_matches = 2
WHERE id = 1;
```

### Opción 3: En el Seeder

Al crear temporadas, especifica:

```php
Season::create([
    'name' => 'Mi Temporada',
    'league_id' => 1,
    'game_days' => ['tuesday', 'thursday', 'saturday'], // o [2, 4, 6]
    'match_times' => ['14:00', '16:00', '18:00'],
    'daily_matches' => 2,
    // ... otros campos
]);
```

---

## ✅ Mejoras Aplicadas

1. ✅ El código ahora **acepta ambos formatos**:
   - Palabras: `"wednesday"` → convierte a `3`
   - Números: `3` → mantiene `3`

2. ✅ **Conversión automática** de días en `Generate.php`

3. ✅ **Validación** para evitar errores si el formato es incorrecto

---

## 🧪 Ejemplo Práctico

**Configuración:**
```json
{
  "game_days": ["2", "4", "6"],  // Martes, Jueves, Sábado
  "match_times": ["14:00", "16:00"],
  "daily_matches": 2,
  "start_date": "2025-10-07"  // Un martes
}
```

**Con 5 equipos (10 partidos):**

| Jornada | Partidos | Fecha | Día | Horarios |
|---------|----------|-------|-----|----------|
| 1 | 2 | 2025-10-07 | Martes | 14:00, 16:00 |
| 2 | 2 | 2025-10-09 | Jueves | 14:00, 16:00 |
| 3 | 2 | 2025-10-11 | Sábado | 14:00, 16:00 |
| 4 | 2 | 2025-10-14 | Martes | 14:00, 16:00 |
| 5 | 2 | 2025-10-16 | Jueves | 14:00, 16:00 |

---

## 🎯 Resumen

- **`game_days`**: Define QUÉ DÍAS se juega (palabras o números 0-6)
- **`match_times`**: Define A QUÉ HORAS se juega (formato HH:MM)
- **`daily_matches`**: Define CUÁNTOS PARTIDOS por día
- **Fecha inicial**: Se define al generar fixtures en `/fixtures/generate`

El algoritmo distribuye automáticamente los partidos según esta configuración. 🎉
