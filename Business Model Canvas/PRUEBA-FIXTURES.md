# 🧪 GUÍA DE PRUEBA: Sistema de Fixtures (Calendario de Partidos)

## 📋 **Requisitos Previos**

Antes de comenzar, asegúrate de tener:
- ✅ Migraciones ejecutadas (`fixtures` y `venues`)
- ✅ Usuario admin o league_manager creado
- ✅ Al menos 1 liga activa
- ✅ Al menos 1 temporada activa
- ✅ Al menos 4 equipos en una temporada (mínimo 2 para generar fixtures)
- ✅ Al menos 1 cancha (venue) creada

**Estado actual:**
- ✅ 8 canchas creadas (2 por cada liga)
- ✅ 6 equipos de seeders
- ✅ 2 temporadas activas

---

## 🎯 **PRUEBA 1: Ver Listado de Fixtures (Vacío)**

### Navegación:
1. Inicia sesión como **admin**
2. Ve al sidebar izquierdo
3. Click en **"Calendario"** (icono de calendario)

### URL Directa:
```
http://localhost/fixtures
```

### Qué Esperar:
- ✅ Página carga sin errores
- ✅ Header: "Calendario de Partidos"
- ✅ Botón verde: "Generar Fixtures"
- ✅ Filtros: Búsqueda, Liga, Temporada, Estado
- ✅ Mensaje: "No hay fixtures disponibles"
- ✅ Icono de calendario vacío

### Screenshot esperado:
```
┌─────────────────────────────────────────────┐
│ Calendario de Partidos    [Generar Fixtures]│
├─────────────────────────────────────────────┤
│ [Buscar...] [Liga▼] [Temporada▼] [Estado▼] │
├─────────────────────────────────────────────┤
│                 📅                          │
│      No hay fixtures disponibles            │
│   Genera fixtures para comenzar             │
└─────────────────────────────────────────────┘
```

---

## 🎯 **PRUEBA 2: Generar Fixtures con Round Robin**

### Navegación:
1. Desde el listado de fixtures
2. Click en botón **"Generar Fixtures"**

### URL Directa:
```
http://localhost/fixtures/generate
```

### Paso a Paso:

#### **2.1 - Configuración Básica**

1. **Seleccionar Liga** (si eres admin)
   - Dropdown muestra las 4 ligas disponibles
   - Selecciona: "Liga Premier de Fútbol"

2. **Seleccionar Temporada**
   - Dropdown se actualiza automáticamente
   - Selecciona: "Temporada 2024"

3. **Seleccionar Cancha Principal** (opcional)
   - Dropdown muestra: "Estadio Principal Liga Premier de Fútbol"
   - Selecciona la primera opción

4. **Fecha de Inicio**
   - Campo de fecha aparece
   - Por defecto: fecha de inicio de la temporada
   - Puedes cambiarla a: **2025-11-01**

#### **2.2 - Configuración del Torneo**

5. **Tipo de Torneo**
   - ✅ Usar Round Robin (marcado por defecto)
   - ☐ Doble Ronda (Ida y Vuelta)
   - Deja solo Round Robin activado

6. **Click en "Generar Vista Previa"**
   - Botón muestra spinner: "Generando..."
   - Espera 1-2 segundos

### Qué Esperar:

#### **Vista Previa Generada:**

```
┌───────────────────────────────────────────────────────┐
│ CONFIGURACIÓN          │ VISTA PREVIA                 │
├───────────────────────────────────────────────────────┤
│ Liga: Premier Fútbol   │ Jornada 1                    │
│ Temporada: 2024        │ ├ Los Tigres vs Águilas      │
│ Cancha: Estadio        │ ├ Leones vs Pumas             │
│ Inicio: 01/11/2025     │                               │
│                        │ Jornada 2                     │
│ [✓] Round Robin        │ ├ Los Tigres vs Leones        │
│ [ ] Doble Ronda        │ ├ Águilas vs Pumas            │
│                        │                               │
│ ┌──────────────────┐   │ Jornada 3                     │
│ │ Resumen          │   │ ├ Los Tigres vs Pumas         │
│ │ Jornadas: 3      │   │ ├ Águilas vs Leones           │
│ │ Partidos: 6      │   │                               │
│ │ Tipo: Una Vuelta │   │ [!] Nota: Esta es solo vista  │
│ └──────────────────┘   │ previa. Click en Confirmar... │
└───────────────────────────────────────────────────────┘
```

#### **2.3 - Verificar Datos**

**Resumen debe mostrar:**
- Total de Jornadas: **3** (4 equipos - 1 = 3 jornadas)
- Total de Partidos: **6** (4 equipos * 3 / 2 = 6 partidos)
- Tipo: **Una Vuelta**

**Cada partido debe mostrar:**
- Equipo Local vs Equipo Visitante
- Fecha calculada automáticamente
- Número de partido

#### **2.4 - Confirmar Creación**

7. **Click en "Confirmar y Crear Fixtures"**
   - Aparece confirmación: "¿Estás seguro? Esto creará 6 partidos en la base de datos."
   - Click en **"Confirmar"**
   - Botón muestra: "Creando..."
   - Redirecciona a `/fixtures`

### Qué Esperar:
- ✅ Mensaje verde: "6 fixtures generados exitosamente"
- ✅ Tabla muestra los 6 partidos
- ✅ Cada partido tiene:
  - Badge "J1", "J2", "J3" (Jornada)
  - Fecha y hora
  - Equipos con colores
  - Estado "Programado" (azul)

---

## 🎯 **PRUEBA 3: Ver Fixtures Generados (Con Datos)**

### Verificar Tabla Desktop (≥1024px):

**Columnas visibles:**
1. **Jornada**: Badge azul "J1", "J2", etc.
2. **Fecha**: "01/11/2025" + hora
3. **Partido**: 
   - Badge con color del equipo local
   - "vs"
   - Badge con color del equipo visitante
   - Debajo: "Liga Premier de Fútbol - Temporada 2024"
4. **Cancha**: "Estadio Principal..."
5. **Resultado**: "-" (aún no jugado)
6. **Estado**: Badge "Programado" (azul)
7. **Acciones**: "Editar" (azul)

### Ejemplo de Fila:

```
┌──────┬────────────┬─────────────────────────┬──────────────┬──────────┬────────────┬─────────┐
│  J1  │ 01/11/2025 │ [Tigres] vs [Águilas]  │ Estadio...   │    -     │ Programado │ Editar  │
│      │ 14:00      │ Liga - Temporada 2024   │              │          │            │         │
└──────┴────────────┴─────────────────────────┴──────────────┴──────────┴────────────┴─────────┘
```

### Verificar Cards Mobile (<1024px):

**Abre DevTools > Toggle Device Toolbar > iPhone 12 Pro**

Cada card debe mostrar:
```
┌─────────────────────────────────────┐
│ [Jornada 1]         [Programado]    │
├─────────────────────────────────────┤
│ [Los Tigres FC]             3       │
│         VS                          │
│ [Águilas Doradas]           2       │
├─────────────────────────────────────┤
│ Fecha: 01/11/2025 14:00             │
│ Cancha: Estadio Principal...        │
│ Liga: Liga Premier de Fútbol        │
├─────────────────────────────────────┤
│                    [Editar] [Borrar]│
└─────────────────────────────────────┘
```

---

## 🎯 **PRUEBA 4: Filtros y Búsqueda**

### 4.1 - Filtro por Estado
1. Dropdown "Estado" > Seleccionar "Programado"
2. Resultado: Muestra todos los fixtures (todos están programados)
3. Cambiar a "Completado" 
4. Resultado: "No hay fixtures disponibles" (ninguno completado aún)

### 4.2 - Búsqueda por Equipo
1. Campo "Buscar..." > Escribir "Tigres"
2. Resultado: Muestra solo los 3 partidos donde Los Tigres FC juega
3. Borrar búsqueda
4. Resultado: Vuelve a mostrar los 6 partidos

### 4.3 - Filtro por Temporada
1. Dropdown "Temporada" > Seleccionar "Temporada 2024"
2. Resultado: Muestra los 6 fixtures de esa temporada
3. Cambiar a "Temporada 2025"
4. Resultado: "No hay fixtures" (no hay fixtures para 2025)

### 4.4 - Ordenamiento
1. Click en header "Fecha"
2. Resultado: Fixtures se ordenan por fecha ascendente (flecha ↑)
3. Click nuevamente en "Fecha"
4. Resultado: Fixtures se ordenan por fecha descendente (flecha ↓)

---

## 🎯 **PRUEBA 5: Doble Ronda (Ida y Vuelta)**

### Navegación:
1. Ve a `/fixtures/generate`
2. Selecciona la misma liga y temporada
3. Esta vez marca: ✅ **Doble Ronda (Ida y Vuelta)**
4. Click "Generar Vista Previa"

### Qué Esperar:

**Resumen debe mostrar:**
- Total de Jornadas: **6** (3 jornadas × 2 = 6 jornadas)
- Total de Partidos: **12** (6 partidos × 2 = 12 partidos)
- Tipo: **Ida y Vuelta**

**Vista previa debe mostrar:**
- Jornadas 1-3: Partidos de IDA (Local vs Visitante)
- Jornadas 4-6: Partidos de VUELTA (Visitante vs Local - invertidos)

**Ejemplo:**
- Jornada 1: Tigres (casa) vs Águilas (visita)
- Jornada 4: Águilas (casa) vs Tigres (visita)

### **⚠️ NO CONFIRMAR TODAVÍA**
Solo observa la vista previa. No crees fixtures duplicados.

---

## 🎯 **PRUEBA 6: Responsive Design**

### Desktop (≥1024px):
1. Maximiza ventana del navegador
2. Verifica: Tabla con 7 columnas visible
3. Scroll horizontal: NO debe existir

### Tablet (768px - 1023px):
1. Reduce ventana a ~900px de ancho
2. Verifica: Sidebar se colapsa automáticamente
3. Tabla sigue siendo visible y funcional

### Mobile (<768px):
1. Abre DevTools > iPhone 12 Pro (390 × 844)
2. Verifica: 
   - Cards en lugar de tabla
   - Sidebar se oculta (icono hamburguesa visible)
   - Badges de equipos con colores
   - Toda la información visible sin scroll horizontal

---

## 🎯 **PRUEBA 7: Permisos por Rol**

### Como League Manager:
1. Cierra sesión como admin
2. Inicia sesión con usuario league_manager
3. Ve a `/fixtures`
4. Verifica:
   - ✅ Solo ve fixtures de SU liga
   - ✅ Puede acceder a "Generar Fixtures"
   - ✅ NO ve fixtures de otras ligas en los filtros

### Como Coach:
1. Cierra sesión como league_manager
2. Inicia sesión con usuario coach
3. Ve a `/fixtures`
4. Verifica:
   - ✅ Solo ve fixtures donde SUS equipos juegan
   - ❌ NO puede acceder a "Generar Fixtures" (botón no aparece)
   - ✅ Puede ver detalles de los partidos de sus equipos

---

## 🎯 **PRUEBA 8: Algoritmo Round Robin**

### Verificación Manual:

Con 4 equipos (A, B, C, D), Round Robin debe generar:

**Jornada 1:**
- A vs B
- C vs D

**Jornada 2:**
- A vs C
- B vs D

**Jornada 3:**
- A vs D
- B vs C

### Verificar en tu sistema:
1. Ve a `/fixtures`
2. Ordena por "Jornada" (ascendente)
3. Anota los emparejamientos de cada jornada
4. Verifica que:
   - ✅ Cada equipo juega contra todos los demás exactamente 1 vez
   - ✅ Ningún equipo juega contra sí mismo
   - ✅ Los partidos están balanceados (2 partidos por jornada con 4 equipos)

---

## 🎯 **PRUEBA 9: Validaciones y Errores**

### 9.1 - Sin Equipos Suficientes
1. Ve a `/fixtures/generate`
2. Selecciona una temporada con solo 1 equipo
3. Click "Generar Vista Previa"
4. Verifica: Mensaje rojo "Se necesitan al menos 2 equipos para generar fixtures"

### 9.2 - Sin Temporada Seleccionada
1. Ve a `/fixtures/generate`
2. Deja "Temporada" en blanco
3. Click "Generar Vista Previa"
4. Verifica: Mensaje de error "El campo temporada es obligatorio"

### 9.3 - Fecha Pasada
1. Ve a `/fixtures/generate`
2. Selecciona una fecha anterior a hoy (ej: 2024-01-01)
3. Click "Generar Vista Previa"
4. Verifica: Error de validación "La fecha debe ser posterior o igual a hoy"

---

## 🎯 **PRUEBA 10: Cálculo Automático de Fechas**

### Configuración de Temporada:
1. Ve a la temporada que estás usando
2. Verifica campos:
   - `start_date`: 2024-11-01
   - `game_days`: JSON array con días de juego (ej: [0, 3] = Domingo y Miércoles)
   - `match_times`: JSON array con horarios (ej: ["14:00", "16:00"])

### Generar Fixtures:
1. Genera fixtures con fecha inicio: 2025-11-01 (Sábado)
2. Con `game_days = [0]` (solo Domingos)
3. Verifica que los fixtures se generan:
   - Jornada 1: 2025-11-02 (Domingo siguiente)
   - Jornada 2: 2025-11-09 (Domingo +7 días)
   - Jornada 3: 2025-11-16 (Domingo +7 días)

### Horarios Alternados:
1. Si `match_times = ["14:00", "16:00", "18:00"]`
2. Verifica que los partidos de una misma jornada tienen horarios diferentes:
   - Partido 1: 14:00
   - Partido 2: 16:00
   - Partido 3: 18:00 (si hay 3 partidos en la jornada)

---

## ✅ **Checklist Final de Verificación**

Marca cada ítem después de probarlo:

### Funcionalidad Básica:
- [ ] Listado de fixtures muestra correctamente
- [ ] Filtros funcionan (liga, temporada, estado)
- [ ] Búsqueda por equipo funciona
- [ ] Ordenamiento por columnas funciona
- [ ] Paginación funciona (si hay >15 fixtures)

### Generación de Fixtures:
- [ ] Vista previa se genera correctamente
- [ ] Resumen muestra datos correctos (jornadas, partidos)
- [ ] Round Robin genera emparejamientos justos
- [ ] Doble ronda invierte local/visitante
- [ ] Fechas se calculan según game_days
- [ ] Horarios se alternan según match_times
- [ ] Confirmación crea fixtures en BD

### Responsive Design:
- [ ] Desktop muestra tabla completa
- [ ] Mobile muestra cards
- [ ] Sidebar colapsa en mobile
- [ ] No hay scroll horizontal en mobile
- [ ] Badges de equipos con colores visibles

### Seguridad y Permisos:
- [ ] Admin ve todos los fixtures
- [ ] League Manager ve solo sus ligas
- [ ] Coach ve solo sus equipos
- [ ] Botón "Generar" solo para admin/league_manager
- [ ] Validaciones funcionan correctamente

---

## 🐛 **Problemas Comunes y Soluciones**

### Problema 1: "No hay fixtures disponibles" después de generar
**Causa**: Error en la creación de fixtures
**Solución**: 
```bash
php artisan tinker --execute="dump(DB::table('fixtures')->count());"
```
Si retorna 0, revisar logs de Laravel

### Problema 2: Equipos sin colores en badges
**Causa**: Equipos sin primary_color/secondary_color
**Solución**:
```bash
php artisan tinker --execute="DB::table('teams')->whereNull('primary_color')->update(['primary_color' => '#000000', 'secondary_color' => '#FFFFFF']);"
```

### Problema 3: Fechas incorrectas
**Causa**: game_days no está en formato JSON
**Solución**: Editar temporada y asegurarse que game_days sea `[0,3]` (array JSON válido)

### Problema 4: Error 500 al generar
**Causa**: Venue_id nulo
**Solución**: Dejar campo de cancha en blanco (es opcional) o crear canchas con el script seed_venues.php

---

## 📸 **Screenshots Esperados**

### 1. Listado Vacío:
![Listado Vacío](esperado: tabla vacía con mensaje "No hay fixtures disponibles")

### 2. Vista Previa Round Robin:
![Vista Previa](esperado: 3 jornadas con 6 partidos total)

### 3. Fixtures Generados:
![Fixtures Generados](esperado: tabla con 6 partidos, jornadas 1-3, estado "Programado")

### 4. Mobile Cards:
![Mobile](esperado: cards verticales con badges de equipos y colores)

---

## 🎓 **Conclusión**

Si completaste todas las pruebas sin errores, el sistema de Fixtures está funcionando correctamente. 

**Próximos pasos sugeridos:**
1. Crear componente Edit para modificar fixtures individuales
2. Implementar registro de resultados (marcadores)
3. Crear tabla de posiciones automática basada en resultados
4. Agregar notificaciones de próximos partidos

**¡Excelente trabajo! 🎉**
