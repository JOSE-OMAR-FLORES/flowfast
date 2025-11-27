# 🧪 GUÍA DE PRUEBAS COMPLETA - FLOWFAST SAAS

**Fecha**: 2 de octubre de 2025  
**Versión**: FASE 2 - 90% Completado

---

## 📋 PREREQUISITOS

### 1. Verificar Estado del Sistema

```powershell
# Ejecutar script de verificación
php test_full_flow.php
```

**Resultado Esperado:**
```
✅ Deportes: 5
✅ Venues: 8
✅ Ligas: 4
✅ Equipos: 7
✅ Temporadas: 3
✅ Usuarios: 3
```

### 2. Verificar Usuario Admin

**Email**: `admin@flowfast.com`  
**Password**: (El que hayas configurado)

Si no tienes un admin, ejecuta:
```powershell
php create_admin_temp.php
```

---

## 🌐 PRUEBAS FRONTEND PÚBLICO

### **PRUEBA 1: Página de Inicio**
1. **URL**: `http://flowfast-saas.test/`
2. **Verificar**:
   - ✅ Hero section con título "Gestiona tus Ligas Deportivas"
   - ✅ Sección "Características Principales" (4 cards)
   - ✅ Sección "Ligas Activas" con cards de ligas
   - ✅ Botón "Crear Cuenta Gratis" o "Ir al Dashboard" (si estás logueado)
   - ✅ Footer con links
3. **Acción**: Click en una liga
4. **Resultado Esperado**: Navega a `/league/{slug}`

---

### **PRUEBA 2: Detalle de Liga (Público)**
1. **URL**: `http://flowfast-saas.test/league/liga-premier-de-futbol`
2. **Verificar**:
   - ✅ Nombre de la liga y deporte
   - ✅ Tabs: Inicio, Fixtures, Tabla, Equipos
   - ✅ Información de la temporada actual
   - ✅ Estadísticas generales
3. **Acción**: Click en tab "Fixtures"
4. **Resultado Esperado**: Ver calendario de partidos

---

### **PRUEBA 3: Fixtures Públicos**
1. **URL**: `http://flowfast-saas.test/league/{slug}/fixtures`
2. **Verificar**:
   - ✅ Lista de partidos por jornada
   - ✅ Scores si hay partidos finalizados
   - ✅ Fechas y horarios
   - ✅ Venues (canchas)
3. **Acción**: Verificar que se muestren todos los partidos

---

### **PRUEBA 4: Tabla de Posiciones Pública**
1. **URL**: `http://flowfast-saas.test/league/{slug}/standings`
2. **Verificar**:
   - ✅ Tabla ordenada por puntos
   - ✅ Columnas: Pos, Equipo, PJ, PG, PE, PP, GF, GC, DG, Pts
   - ✅ Logos de equipos
   - ✅ Colores de clasificación (verde para campeón, azul para playoffs)

---

### **PRUEBA 5: Lista de Equipos Pública**
1. **URL**: `http://flowfast-saas.test/league/{slug}/teams`
2. **Verificar**:
   - ✅ Cards de equipos con logos
   - ✅ Información: nombre, entrenador, colores
   - ✅ Estadísticas básicas

---

## 🔐 PRUEBAS BACKEND ADMINISTRATIVO

### **LOGIN**
1. **URL**: `http://flowfast-saas.test/login`
2. **Credenciales**:
   - Email: `admin@flowfast.com`
   - Password: (tu password)
3. **Resultado Esperado**: Redirige a `/admin` (Dashboard)

---

## 📊 MÓDULO: DASHBOARD

### **PRUEBA 6: Dashboard Principal**
1. **URL**: `http://flowfast-saas.test/admin`
2. **Verificar**:
   - ✅ Cards con métricas (Ligas Activas, Equipos, Jugadores, Partidos)
   - ✅ Gráficos estadísticos
   - ✅ Lista de próximos partidos
   - ✅ Sidebar con navegación

---

## 🏆 MÓDULO: LIGAS

### **PRUEBA 7: Lista de Ligas**
1. **URL**: `http://flowfast-saas.test/admin/leagues`
2. **Verificar**:
   - ✅ Tabla con todas las ligas
   - ✅ Columnas: Nombre, Deporte, Descripción, Acciones
   - ✅ Botón "➕ Crear Liga"
   - ✅ Botones "✏️ Editar" por cada liga

---

### **PRUEBA 8: Crear Liga**
1. **URL**: `http://flowfast-saas.test/admin/leagues/create`
2. **Llenar formulario**:
   - Nombre: `Liga de Prueba`
   - Slug: `liga-prueba` (auto-generado)
   - Deporte: Seleccionar uno
   - Descripción: `Liga creada para pruebas`
3. **Click**: "Guardar"
4. **Resultado Esperado**: 
   - ✅ Mensaje "Liga creada exitosamente"
   - ✅ Redirige a lista de ligas
   - ✅ Nueva liga aparece en la tabla

---

### **PRUEBA 9: Editar Liga**
1. **Acción**: Click en "✏️ Editar" en cualquier liga
2. **Modificar**: Cambiar la descripción
3. **Click**: "Guardar"
4. **Resultado Esperado**:
   - ✅ Mensaje "Liga actualizada exitosamente"
   - ✅ Cambios reflejados en la lista

---

## 📅 MÓDULO: TEMPORADAS

### **PRUEBA 10: Lista de Temporadas**
1. **URL**: `http://flowfast-saas.test/admin/seasons`
2. **Verificar**:
   - ✅ Filtro por liga
   - ✅ Tabla con temporadas
   - ✅ Estado (Draft, Upcoming, Active, Completed)
   - ✅ Fechas de inicio y fin
   - ✅ Botón "➕ Crear Temporada"

---

### **PRUEBA 11: Crear Temporada**
1. **URL**: `http://flowfast-saas.test/admin/seasons/create`
2. **Llenar formulario**:
   - Liga: Seleccionar
   - Nombre: `Temporada Apertura 2025`
   - Formato: `Round Robin`
   - Tipo: `Double Round` (ida y vuelta)
   - Fecha Inicio: `2025-01-15`
   - Fecha Fin: `2025-06-30`
   - Días de juego: Marcar `Sábado` y `Domingo`
   - Partidos diarios: `3`
   - Horarios: `10:00`, `14:00`, `18:00`
3. **Click**: "Guardar"
4. **Resultado Esperado**:
   - ✅ Mensaje "Temporada creada exitosamente"
   - ✅ Nueva temporada en lista

---

## 👥 MÓDULO: EQUIPOS

### **PRUEBA 12: Lista de Equipos**
1. **URL**: `http://flowfast-saas.test/admin/teams`
2. **Verificar**:
   - ✅ Filtro por temporada
   - ✅ Cards de equipos con logos
   - ✅ Información: nombre, entrenador, colores
   - ✅ Estado de pago de registro
   - ✅ Botón "➕ Crear Equipo"

---

### **PRUEBA 13: Crear Equipo**
1. **URL**: `http://flowfast-saas.test/admin/teams/create`
2. **Llenar formulario**:
   - Temporada: Seleccionar
   - Nombre: `Equipo Prueba FC`
   - Entrenador: Seleccionar
   - Color Primario: `#FF0000` (rojo)
   - Color Secundario: `#FFFFFF` (blanco)
   - Logo: Subir imagen (opcional)
3. **Click**: "Guardar"
4. **Resultado Esperado**:
   - ✅ Mensaje "Equipo creado exitosamente"
   - ✅ Nuevo equipo aparece con sus colores

---

## 🏃 MÓDULO: JUGADORES (SIN IMPORTACIÓN)

### **PRUEBA 14: Lista de Jugadores**
1. **URL**: `http://flowfast-saas.test/admin/players`
2. **Verificar**:
   - ✅ Filtro por liga y equipo
   - ✅ Buscador por nombre
   - ✅ Tabla con jugadores
   - ✅ Columnas: #, Nombre, Posición, Estado, Equipo, Estadísticas
   - ✅ Botones "➕ Crear" y "📥 Importar CSV/Excel"

---

### **PRUEBA 15: Crear Jugador Manualmente**
1. **URL**: `http://flowfast-saas.test/admin/players/create`
2. **Llenar formulario**:
   - Liga: Seleccionar
   - Equipo: Seleccionar
   - Nombre: `Juan Pérez`
   - Apellido: `González`
   - Email: `juan.perez@test.com`
   - Fecha Nacimiento: `1995-05-20`
   - Número Dorsal: `10`
   - Posición: `Midfielder`
   - Estado: `Active`
   - Foto: Subir imagen (opcional)
3. **Click**: "Guardar"
4. **Resultado Esperado**:
   - ✅ Mensaje "Jugador creado exitosamente"
   - ✅ Aparece en la lista con su número

---

## 📥 MÓDULO: IMPORTACIÓN MASIVA DE JUGADORES

### **PRUEBA 16: Descargar Plantilla CSV**
1. **URL**: `http://flowfast-saas.test/admin/players/import`
2. **Click**: Botón "📥 Descargar Plantilla CSV" en el sidebar
3. **Resultado Esperado**:
   - ✅ Se descarga archivo `players_template.csv`
   - ✅ Contiene 8 columnas: nombre, apellido, email, fecha_nacimiento, numero_dorsal, posicion, estado, telefono
   - ✅ Incluye 4 filas de ejemplo

---

### **PRUEBA 17: Importar Jugadores - Paso 1 (Upload)**
1. **URL**: `http://flowfast-saas.test/admin/players/import`
2. **Llenar formulario**:
   - Liga: Seleccionar
   - Equipo: Seleccionar
   - Archivo: Subir `players_template.csv` (puedes usar el descargado y editarlo en Excel)
3. **Click**: "Continuar"
4. **Resultado Esperado**:
   - ✅ Avanza a Paso 2 (Preview)
   - ✅ Muestra resumen: Total, Válidos, Inválidos
   - ✅ Tabla verde con jugadores válidos
   - ✅ Tabla roja con jugadores inválidos (si hay errores)

---

### **PRUEBA 18: Importar Jugadores - Paso 2 (Preview)**
1. **Verificar**:
   - ✅ Datos parseados correctamente
   - ✅ Posiciones normalizadas (español → inglés)
   - ✅ Estados normalizados
   - ✅ Errores detallados por fila (duplicados, campos faltantes)
2. **Click**: "Importar Jugadores"
3. **Resultado Esperado**:
   - ✅ Avanza a Paso 3 (Result)
   - ✅ Muestra cuántos jugadores se importaron exitosamente
   - ✅ Muestra errores si hubo (con números de fila)

---

### **PRUEBA 19: Importar Jugadores - Paso 3 (Result)**
1. **Verificar**:
   - ✅ Emoji ✅ si todo exitoso, ⚠️ si hubo errores parciales
   - ✅ Contador de jugadores importados
   - ✅ Lista de errores (si hubo)
2. **Click**: "Ver Jugadores"
3. **Resultado Esperado**:
   - ✅ Redirige a `/admin/players`
   - ✅ Jugadores importados aparecen en la lista

---

## 🗓️ MÓDULO: FIXTURES

### **PRUEBA 20: Generar Fixtures**
1. **URL**: `http://flowfast-saas.test/admin/fixtures/generate`
2. **Llenar formulario**:
   - Liga: Seleccionar
   - Temporada: Seleccionar (debe tener al menos 4 equipos)
   - Algoritmo: `Round Robin`
   - Tipo: `Double Round` (ida y vuelta)
3. **Click**: "Generar Fixtures"
4. **Resultado Esperado**:
   - ✅ Mensaje "Fixtures generados exitosamente"
   - ✅ Se crean partidos automáticamente
   - ✅ Partidos distribuidos en los días configurados
   - ✅ Horarios asignados según configuración

---

### **PRUEBA 21: Ver Lista de Fixtures**
1. **URL**: `http://flowfast-saas.test/admin/fixtures`
2. **Verificar**:
   - ✅ Filtro por liga y temporada
   - ✅ Filtro por jornada
   - ✅ Tabla agrupada por jornada
   - ✅ Información: Equipos, Fecha, Hora, Venue, Score, Estado
   - ✅ Badges de estado (Programado, En Vivo, Finalizado)
   - ✅ Botón "⚽ Gestionar" para partidos (solo si tienes permisos)

---

### **PRUEBA 22: Editar Fixture**
1. **Acción**: Click en "✏️" en cualquier partido
2. **Modificar**:
   - Fecha: Cambiar a otra fecha
   - Hora: Cambiar hora
   - Venue: Cambiar cancha
   - Árbitro: Asignar árbitro
3. **Click**: "Guardar"
4. **Resultado Esperado**:
   - ✅ Mensaje "Partido actualizado exitosamente"
   - ✅ Cambios reflejados en la lista

---

## ⚽ MÓDULO: PARTIDOS EN VIVO

### **PRUEBA 23: Iniciar Partido**
1. **URL**: `http://flowfast-saas.test/admin/fixtures`
2. **Buscar**: Un partido con estado "Programado"
3. **Click**: Botón "⚽ Gestionar"
4. **URL Nueva**: `http://flowfast-saas.test/admin/matches/{id}/live`
5. **Verificar**:
   - ✅ Scoreboard con 0 - 0
   - ✅ Equipos local y visitante
   - ✅ Botón "▶️ Iniciar Partido" (verde)
   - ✅ Botones de eventos deshabilitados
   - ✅ Timeline vacío
   - ✅ Sidebar con listas de jugadores
6. **Click**: "▶️ Iniciar Partido"
7. **Confirmar**: Click "Sí, iniciar"
8. **Resultado Esperado**:
   - ✅ Mensaje "Partido iniciado"
   - ✅ Badge cambia a "🔴 En Vivo" con animación pulsante
   - ✅ Se habilitan botones de eventos (Gol, Amarilla, Roja, Cambio)
   - ✅ Botón cambia a "⏹️ Finalizar Partido" (rojo)
   - ✅ Se registra hora de inicio

---

### **PRUEBA 24: Registrar Gol**
1. **En partido en vivo**
2. **Click**: Botón "⚽ Gol" del equipo local
3. **En modal**:
   - Jugador: Seleccionar
   - Minuto: `15`
   - Tiempo Extra: (dejar vacío)
   - Descripción: `Gran remate de media distancia`
4. **Click**: "Registrar Evento"
5. **Resultado Esperado**:
   - ✅ Modal se cierra
   - ✅ Score se actualiza: 1 - 0
   - ✅ Aparece en timeline: "⚽ Gol - [Nombre Jugador] - 15'"
   - ✅ Estadística del jugador se incrementa (goals_scored++)
   - ✅ Botón "✕" para eliminar evento

---

### **PRUEBA 25: Registrar Tarjeta Amarilla**
1. **Click**: Botón "🟨 Amarilla" del equipo visitante
2. **En modal**:
   - Jugador: Seleccionar
   - Minuto: `28`
3. **Click**: "Registrar Evento"
4. **Resultado Esperado**:
   - ✅ Aparece en timeline: "🟨 Tarjeta Amarilla - [Nombre] - 28'"
   - ✅ Estadística del jugador: yellow_cards++

---

### **PRUEBA 26: Registrar Tarjeta Roja**
1. **Click**: Botón "🟥 Roja" del equipo local
2. **En modal**:
   - Jugador: Seleccionar
   - Minuto: `45`
   - Tiempo Extra: `3` (tiempo agregado)
3. **Click**: "Registrar Evento"
4. **Resultado Esperado**:
   - ✅ Aparece en timeline: "🟥 Tarjeta Roja - [Nombre] - 45+3'"
   - ✅ Estadística del jugador: red_cards++
   - ✅ Estado del jugador cambia a "Suspended"

---

### **PRUEBA 27: Registrar Cambio**
1. **Click**: Botón "🔄 Cambio" del equipo local
2. **En modal**:
   - Jugador Sale: Seleccionar
   - Jugador Entra: Seleccionar (diferente)
   - Minuto: `60`
3. **Click**: "Registrar Evento"
4. **Resultado Esperado**:
   - ✅ Aparece en timeline: "🔄 Sustitución - Sale: [Nombre1] Entra: [Nombre2] - 60'"

---

### **PRUEBA 28: Registrar Penal Anotado**
1. **Click**: Botón "⚽ Gol" → Modal
2. **Cambiar tipo**: Buscar opción "Penal Anotado" si existe, o simplemente registrar gol con descripción "Penal"
3. **Resultado Esperado**:
   - ✅ Score se actualiza
   - ✅ Estadística goals_scored++

---

### **PRUEBA 29: Eliminar Evento**
1. **En timeline**: Buscar cualquier evento
2. **Click**: Botón "✕" (solo visible si partido está en vivo)
3. **Confirmar**: Click en confirmación
4. **Resultado Esperado**:
   - ✅ Evento desaparece del timeline
   - ✅ Score se recalcula (si era gol)
   - ✅ Estadísticas del jugador se revierten (goals--, cards--)
   - ✅ Si era tarjeta roja, jugador vuelve a "Active"

---

### **PRUEBA 30: Finalizar Partido**
1. **Click**: Botón "⏹️ Finalizar Partido" (rojo)
2. **Confirmar**: Click "Sí, finalizar"
3. **Resultado Esperado**:
   - ✅ Mensaje "Partido finalizado"
   - ✅ Badge cambia a "Finalizado"
   - ✅ Se deshabilitan botones de eventos
   - ✅ Botón "✕" desaparece de eventos (no se pueden eliminar)
   - ✅ Se registra hora de finalización
   - ✅ Se calcula duración en minutos
   - ✅ Score final queda guardado

---

### **PRUEBA 31: Verificar Tabla de Posiciones Actualizada**
1. **URL**: `http://flowfast-saas.test/admin/standings`
2. **Filtrar**: Liga y temporada del partido jugado
3. **Verificar**:
   - ✅ PJ (Partidos Jugados) incrementado para ambos equipos
   - ✅ PG (Partidos Ganados) incrementado para ganador
   - ✅ PP (Partidos Perdidos) incrementado para perdedor
   - ✅ GF (Goles Favor) actualizado
   - ✅ GC (Goles Contra) actualizado
   - ✅ DG (Diferencia Goles) calculado correctamente
   - ✅ Pts (Puntos) actualizados: +3 ganador, +1 empate
   - ✅ Orden correcto por puntos → DG → GF

---

## 💰 MÓDULO: SISTEMA FINANCIERO

### **PRUEBA 32: Dashboard Financiero**
1. **URL**: `http://flowfast-saas.test/admin/financial/dashboard/{leagueId}`
2. **Verificar**:
   - ✅ Cards con totales: Ingresos, Egresos, Balance
   - ✅ Gráfico de ingresos vs egresos
   - ✅ Lista de últimas transacciones
   - ✅ Filtro por temporada

---

### **PRUEBA 33: Registrar Ingreso**
1. **URL**: `http://flowfast-saas.test/admin/financial/income/create`
2. **Llenar formulario**:
   - Liga: Seleccionar
   - Temporada: Seleccionar
   - Concepto: `Inscripción Equipo`
   - Monto: `5000.00`
   - Método de Pago: `Transferencia`
   - Fecha: Hoy
   - Descripción: `Pago inscripción Equipo Prueba FC`
3. **Click**: "Guardar"
4. **Resultado Esperado**:
   - ✅ Mensaje "Ingreso registrado"
   - ✅ Aparece en dashboard financiero
   - ✅ Balance se actualiza

---

### **PRUEBA 34: Registrar Egreso**
1. **URL**: `http://flowfast-saas.test/admin/financial/expense/create`
2. **Llenar formulario**:
   - Liga: Seleccionar
   - Temporada: Seleccionar
   - Concepto: `Pago Árbitro`
   - Monto: `500.00`
   - Método de Pago: `Efectivo`
   - Fecha: Hoy
   - Descripción: `Pago árbitro partido Jornada 1`
3. **Click**: "Guardar"
4. **Resultado Esperado**:
   - ✅ Mensaje "Egreso registrado"
   - ✅ Balance se actualiza (descuenta)

---

## 🏆 MÓDULO: STANDINGS (TABLA DE POSICIONES)

### **PRUEBA 35: Ver Tabla de Posiciones Admin**
1. **URL**: `http://flowfast-saas.test/admin/standings`
2. **Verificar**:
   - ✅ Filtro por liga y temporada
   - ✅ Tabla ordenada automáticamente
   - ✅ Logos de equipos
   - ✅ 11 columnas métricas
   - ✅ Colores de clasificación
   - ✅ Actualización automática después de cada partido

---

## 🔍 PRUEBAS DE VALIDACIÓN Y ERRORES

### **PRUEBA 36: Validación Jugador Duplicado (Jersey)**
1. **URL**: `http://flowfast-saas.test/admin/players/create`
2. **Intentar crear jugador con número de dorsal ya existente en el mismo equipo**
3. **Resultado Esperado**:
   - ✅ Error: "El número de dorsal ya está en uso en este equipo"

---

### **PRUEBA 37: Validación Import CSV - Formato Incorrecto**
1. **URL**: `http://flowfast-saas.test/admin/players/import`
2. **Subir archivo con columnas incorrectas**
3. **Resultado Esperado**:
   - ✅ Error: "El archivo no tiene el formato correcto"

---

### **PRUEBA 38: Validación Import CSV - Datos Inválidos**
1. **Subir CSV con**:
   - Email inválido: `jugador@test` (sin .com)
   - Posición inválida: `Portador` (en lugar de Portero/Goalkeeper)
   - Fecha nacimiento futura: `2030-01-01`
2. **Resultado Esperado**:
   - ✅ Paso 2 muestra estas filas en tabla roja (inválidos)
   - ✅ Errores específicos por fila
   - ✅ Solo filas válidas se importan

---

### **PRUEBA 39: Validación Partido en Vivo - No se puede eliminar**
1. **Intentar**: Editar o eliminar un partido con estado "En Vivo"
2. **Resultado Esperado**:
   - ✅ Error o botón deshabilitado
   - ✅ Mensaje: "No se puede modificar un partido en vivo"

---

### **PRUEBA 40: Validación Evento - Jugador Obligatorio**
1. **En partido en vivo**
2. **Registrar gol sin seleccionar jugador**
3. **Resultado Esperado**:
   - ✅ Error de validación
   - ✅ Campo jugador se marca en rojo
   - ✅ Mensaje: "Debe seleccionar un jugador"

---

## 🔒 PRUEBAS DE PERMISOS Y ROLES

### **PRUEBA 41: Admin - Acceso Completo**
1. **Login como Admin**
2. **Verificar acceso a**:
   - ✅ Dashboard
   - ✅ Ligas (create, edit, delete)
   - ✅ Temporadas (create, edit, delete)
   - ✅ Equipos (create, edit, delete)
   - ✅ Jugadores (create, edit, delete, import)
   - ✅ Fixtures (generate, edit, delete)
   - ✅ Partidos en Vivo (gestionar)
   - ✅ Financiero (dashboard, ingresos, egresos)
   - ✅ Standings (ver)

---

### **PRUEBA 42: League Manager - Acceso Limitado**
1. **Crear usuario League Manager** (si no existe)
2. **Asignar a una liga específica**
3. **Login como League Manager**
4. **Verificar**:
   - ✅ Solo ve su liga asignada
   - ✅ Puede gestionar temporadas de su liga
   - ✅ Puede gestionar equipos de su liga
   - ✅ Puede gestionar jugadores de su liga
   - ✅ Puede gestionar fixtures de su liga
   - ✅ Puede gestionar partidos en vivo de su liga
   - ✅ NO puede crear nuevas ligas
   - ✅ NO puede ver otras ligas

---

### **PRUEBA 43: Referee - Acceso Mínimo**
1. **Crear usuario Referee** (si no existe)
2. **Login como Referee**
3. **Verificar**:
   - ✅ Puede ver partidos asignados
   - ✅ Puede gestionar partido en vivo (solo los asignados a él)
   - ✅ NO puede crear/editar ligas, equipos, jugadores
   - ✅ NO puede ver dashboard financiero

---

## 📊 PRUEBAS DE RENDIMIENTO

### **PRUEBA 44: Import Masivo - 50 Jugadores**
1. **Crear CSV con 50 jugadores**
2. **Importar**
3. **Verificar**:
   - ✅ Tiempo de procesamiento < 10 segundos
   - ✅ Todos los jugadores importados correctamente
   - ✅ Sin errores de memoria
   - ✅ Vista previa se carga sin problemas

---

### **PRUEBA 45: Generar Fixtures - Liga de 20 Equipos**
1. **Crear temporada con 20 equipos**
2. **Generar fixtures Double Round Robin**
3. **Verificar**:
   - ✅ Genera 380 partidos (20 equipos = 19 jornadas x 10 partidos x 2 vueltas)
   - ✅ Tiempo de generación < 15 segundos
   - ✅ No hay equipos repetidos en misma jornada
   - ✅ Distribución correcta en venues

---

### **PRUEBA 46: Tabla de Posiciones - Liga con 100+ Partidos**
1. **Filtrar temporada con muchos partidos jugados**
2. **Verificar**:
   - ✅ Tabla se carga < 2 segundos
   - ✅ Cálculos correctos
   - ✅ Orden correcto

---

## 🌐 PRUEBAS DE RESPONSIVE

### **PRUEBA 47: Vista Móvil - Home Público**
1. **Abrir en móvil o DevTools modo responsive (375px)**
2. **Verificar**:
   - ✅ Menú hamburguesa funcional
   - ✅ Cards de ligas en columna única
   - ✅ Botones táctiles grandes
   - ✅ Textos legibles sin zoom

---

### **PRUEBA 48: Vista Móvil - Dashboard Admin**
1. **Abrir en móvil**
2. **Verificar**:
   - ✅ Sidebar colapsable
   - ✅ Cards apilados verticalmente
   - ✅ Tablas con scroll horizontal
   - ✅ Formularios adaptados

---

### **PRUEBA 49: Vista Tablet - Partido en Vivo**
1. **Abrir en tablet (768px)**
2. **Verificar**:
   - ✅ Scoreboard ocupa full width
   - ✅ Botones de eventos en 2 columnas
   - ✅ Timeline legible
   - ✅ Sidebar colapsable

---

## 🔄 PRUEBAS DE INTEGRACIÓN

### **PRUEBA 50: Flujo Completo - De Liga a Partido**
1. **Crear Liga** → ✅
2. **Crear Temporada con configuración de días/horarios** → ✅
3. **Crear 4 Equipos** → ✅
4. **Importar 50 Jugadores** (repartidos en los 4 equipos) → ✅
5. **Generar Fixtures Double Round Robin** → ✅
6. **Verificar calendario generado automáticamente** → ✅
7. **Iniciar un partido** → ✅
8. **Registrar 3 goles, 2 amarillas, 1 roja, 2 cambios** → ✅
9. **Finalizar partido** → ✅
10. **Ver tabla de posiciones actualizada** → ✅
11. **Ver estadísticas de jugadores actualizadas** → ✅
12. **Ver fixture público** → ✅

**Resultado Esperado**: ✅ TODO FUNCIONA CORRECTAMENTE DE PRINCIPIO A FIN

---

## 📝 CHECKLIST FINAL

Marca con ✅ cada módulo probado:

- [ ] ✅ Página Pública (Home, Ligas, Fixtures, Standings, Teams)
- [ ] ✅ Login/Register
- [ ] ✅ Dashboard Admin
- [ ] ✅ CRUD Ligas
- [ ] ✅ CRUD Temporadas
- [ ] ✅ CRUD Equipos
- [ ] ✅ CRUD Jugadores
- [ ] ✅ Importación Masiva CSV/Excel
- [ ] ✅ Generación Automática de Fixtures
- [ ] ✅ Gestión de Partidos en Vivo
- [ ] ✅ Registro de Eventos (Goles, Tarjetas, Cambios)
- [ ] ✅ Actualización Automática de Estadísticas
- [ ] ✅ Tabla de Posiciones Dinámica
- [ ] ✅ Sistema Financiero
- [ ] ✅ Permisos por Roles
- [ ] ✅ Responsive Design
- [ ] ✅ Validaciones

---

## 🎯 MÉTRICAS DE ÉXITO

### **Funcionalidad**
- ✅ 0 errores críticos
- ✅ 0 errores de validación no manejados
- ✅ Todas las rutas accesibles
- ✅ Todas las relaciones de BD funcionando

### **Performance**
- ✅ Página pública carga < 2 segundos
- ✅ Dashboard admin carga < 3 segundos
- ✅ Import 50 jugadores < 10 segundos
- ✅ Generar fixtures 20 equipos < 15 segundos

### **UX**
- ✅ Mensajes de éxito/error claros
- ✅ Formularios intuitivos
- ✅ Navegación lógica
- ✅ Responsive en mobile/tablet

### **Data Integrity**
- ✅ Estadísticas se actualizan correctamente
- ✅ Tabla de posiciones calcula bien
- ✅ No hay duplicados de jersey_number
- ✅ Eventos no se pueden eliminar después de finalizar partido

---

## 🐛 REPORTE DE BUGS

Si encuentras algún error, documentalo así:

```
PRUEBA #: [Número]
MÓDULO: [Nombre del módulo]
DESCRIPCIÓN: [Qué estabas haciendo]
ERROR: [Mensaje de error o comportamiento inesperado]
PASOS PARA REPRODUCIR:
1. ...
2. ...
3. ...
RESULTADO ESPERADO: [Qué debería pasar]
RESULTADO ACTUAL: [Qué pasó realmente]
```

---

## 📚 DOCUMENTACIÓN DE REFERENCIA

- **README-IMPORTACION-JUGADORES.md**: Detalles técnicos del sistema de importación
- **README-PARTIDOS-EN-VIVO.md**: Arquitectura del sistema de partidos en vivo
- **PROGRESO-FASE-2.md**: Estado actual del desarrollo
- **test_full_flow.php**: Script de verificación rápida del sistema

---

## 🎉 ¡LISTO PARA PROBAR!

Ejecuta las pruebas en orden y ve marcando con ✅ cada una completada.

**Tiempo estimado para pruebas completas**: 3-4 horas

**¡Buena suerte! 🚀**
