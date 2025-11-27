# 📖 Manual de Usuario - FlowFast SaaS

> **Sistema de Administración de Ligas Deportivas**  
> **Versión:** 1.0  
> **Fecha:** Noviembre 2025

---

## 📋 Índice

1. [Introducción](#-introducción)
2. [Primeros Pasos](#-primeros-pasos)
3. [Tipos de Usuario](#-tipos-de-usuario)
4. [Guía para Administradores](#-guía-para-administradores)
5. [Guía para Encargados de Liga](#-guía-para-encargados-de-liga)
6. [Guía para Entrenadores](#-guía-para-entrenadores)
7. [Guía para Árbitros](#-guía-para-árbitros)
8. [Guía para Jugadores](#-guía-para-jugadores)
9. [Sistema Financiero](#-sistema-financiero)
10. [Páginas Públicas](#-páginas-públicas)
11. [Preguntas Frecuentes](#-preguntas-frecuentes)

---

## 🎯 Introducción

### ¿Qué es FlowFast?

**FlowFast** es una plataforma integral diseñada para facilitar la gestión completa de ligas deportivas amateur y semi-profesionales. Con FlowFast podrás:

- ⚽ Organizar ligas de múltiples deportes
- 📅 Generar calendarios de partidos automáticamente
- 💰 Controlar ingresos y egresos de tu liga
- 👥 Gestionar equipos, jugadores y árbitros
- 📊 Ver estadísticas y tabla de posiciones en tiempo real
- 🌐 Tener una página pública para tu liga

### ¿Para quién es FlowFast?

| Usuario | Descripción |
|---------|-------------|
| **Administradores** | Dueños de ligas o canchas deportivas |
| **Encargados de Liga** | Personas que gestionan una liga específica |
| **Entrenadores** | Responsables de equipos |
| **Árbitros** | Jueces de los partidos |
| **Jugadores** | Participantes de los equipos |

---

## 🚀 Primeros Pasos

### 1. Acceder a la Plataforma

1. Abre tu navegador web (Chrome, Firefox, Safari, Edge)
2. Ingresa a la dirección: `https://flowfast.me`
3. Haz clic en **"Iniciar Sesión"**

### 2. Iniciar Sesión

![Login Screen]

1. Ingresa tu **correo electrónico**
2. Ingresa tu **contraseña**
3. Haz clic en el botón **"Entrar"**

> 💡 **Nota:** Si no tienes cuenta, necesitas una invitación del administrador de tu liga.

### 3. Registrarse con Invitación

Si recibiste un enlace de invitación por correo electrónico:

1. Haz clic en el enlace del correo
2. Completa el formulario de registro:
   - Nombre completo
   - Correo electrónico (ya viene prellenado)
   - Contraseña (mínimo 8 caracteres)
   - Confirmar contraseña
3. Haz clic en **"Crear Cuenta"**
4. Serás redirigido automáticamente a tu dashboard

### 4. Recuperar Contraseña

Si olvidaste tu contraseña:

1. En la pantalla de login, haz clic en **"¿Olvidaste tu contraseña?"**
2. Ingresa tu correo electrónico
3. Revisa tu bandeja de entrada
4. Haz clic en el enlace de recuperación
5. Crea una nueva contraseña

---

## 👥 Tipos de Usuario

### Jerarquía del Sistema

```
🏆 Super Administrador (Propietario de FlowFast)
    │
    └── 👔 Administrador (Dueño de Liga/Cancha)
            │
            ├── 📋 Encargado de Liga
            │       │
            │       ├── ⚽ Entrenador
            │       │       │
            │       │       └── 🏃 Jugador
            │       │
            │       └── 🎯 Árbitro
            │
            └── (Puede crear directamente)
                    │
                    ├── ⚽ Entrenador
                    ├── 🎯 Árbitro
                    └── 🏃 Jugador
```

### Permisos por Rol

| Función | Admin | Encargado | Entrenador | Árbitro | Jugador |
|---------|:-----:|:---------:|:----------:|:-------:|:-------:|
| Crear ligas | ✅ | ❌ | ❌ | ❌ | ❌ |
| Gestionar temporadas | ✅ | ✅ | ❌ | ❌ | ❌ |
| Crear equipos | ✅ | ✅ | ❌ | ❌ | ❌ |
| Gestionar jugadores | ✅ | ✅ | ✅ | ❌ | ❌ |
| Generar fixtures | ✅ | ✅ | ❌ | ❌ | ❌ |
| Registrar marcadores | ✅ | ✅ | ❌ | ✅ | ❌ |
| Ver finanzas | ✅ | ✅ | ❌ | ❌ | ❌ |
| Ver standings | ✅ | ✅ | ✅ | ✅ | ✅ |
| Enviar invitaciones | ✅ | ✅ | ✅* | ❌ | ❌ |

*Solo puede invitar jugadores a su equipo

---

## 👔 Guía para Administradores

### Tu Dashboard

Al iniciar sesión como administrador, verás tu panel principal con:

```
┌────────────────────────────────────────────────────────────┐
│  📊 DASHBOARD DE ADMINISTRADOR                              │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐       │
│  │ Ligas   │  │ Equipos │  │Jugadores│  │Partidos │       │
│  │   5     │  │   24    │  │   156   │  │   48    │       │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘       │
│                                                             │
│  📅 Próximos Partidos          💰 Resumen Financiero       │
│  ├── Tigres vs Leones (Hoy)    ├── Ingresos: $15,000      │
│  ├── Águilas vs Pumas (Mañana) ├── Egresos:  $5,000       │
│  └── ...                        └── Balance:  $10,000      │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

### Menú Lateral (Sidebar)

| Sección | Descripción |
|---------|-------------|
| 🏠 **Dashboard** | Panel principal con estadísticas |
| 🏆 **Ligas** | Crear y gestionar ligas |
| 📅 **Temporadas** | Administrar temporadas por liga |
| 👥 **Equipos** | Ver y gestionar equipos |
| 🏃 **Jugadores** | Gestión de jugadores |
| 📊 **Fixtures** | Calendario y partidos |
| 🏅 **Standings** | Tabla de posiciones |
| 💰 **Finanzas** | Dashboard financiero |
| ✉️ **Invitaciones** | Enviar tokens de registro |
| ⚙️ **Configuración** | Ajustes de la cuenta |

---

### Crear una Nueva Liga

**Paso 1:** Ve al menú **Ligas** → **Nueva Liga**

**Paso 2:** Completa el formulario:

| Campo | Descripción | Ejemplo |
|-------|-------------|---------|
| Nombre | Nombre de la liga | "Liga de Fútbol Primavera 2025" |
| Deporte | Tipo de deporte | Fútbol, Básquet, Volley, etc. |
| Descripción | Descripción opcional | "Torneo amateur de primavera" |
| Cuota de inscripción | Monto por equipo | $500.00 |
| Pago por partido | Costo por partido | $100.00 |
| Pago a árbitros | Monto por partido | $150.00 |
| Penalización | Multa por incumplimiento | $50.00 |

**Paso 3:** Haz clic en **"Crear Liga"**

---

### Crear una Temporada

**Paso 1:** Ve al menú **Temporadas** → **Nueva Temporada**

**Paso 2:** Completa el formulario:

| Campo | Descripción |
|-------|-------------|
| Liga | Selecciona la liga |
| Nombre | Ej: "Temporada Primavera 2025" |
| Formato | Liga regular o Liga + Playoffs |
| Tipo de vuelta | Ida y vuelta, Solo ida |
| Fecha de inicio | Cuándo comienza |
| Días de juego | Lunes, Miércoles, Viernes, etc. |
| Horarios | 18:00, 19:30, 21:00, etc. |

**Paso 3:** Haz clic en **"Crear Temporada"**

---

### Registrar Equipos

**Opción 1: Crear equipo manualmente**

1. Ve a **Equipos** → **Nuevo Equipo**
2. Completa:
   - Nombre del equipo
   - Liga/Temporada
   - Logo (opcional)
   - Colores del equipo
3. Clic en **"Crear Equipo"**

**Opción 2: Invitar entrenador para que registre su equipo**

1. Ve a **Invitaciones** → **Nueva Invitación**
2. Selecciona tipo: **Entrenador**
3. Ingresa el correo del entrenador
4. Selecciona la liga
5. Enviar invitación

---

### Generar Fixture (Calendario de Partidos)

**Paso 1:** Ve a **Fixtures** → **Generar Fixture**

**Paso 2:** Selecciona la temporada

**Paso 3:** Verás una vista previa del calendario:

```
📅 PREVIEW DEL FIXTURE

Jornada 1 - Sábado 15 de Marzo
├── 18:00  Tigres vs Leones
├── 19:30  Águilas vs Pumas
└── 21:00  Lobos vs Halcones

Jornada 2 - Sábado 22 de Marzo
├── 18:00  Leones vs Águilas
├── 19:30  Pumas vs Lobos
└── 21:00  Halcones vs Tigres

... (continúa)
```

**Paso 4:** Revisa que todo esté correcto

**Paso 5:** Haz clic en **"Confirmar y Generar"**

> ⚠️ **Importante:** Una vez generado, el fixture se puede modificar pero afectará los demás partidos.

---

### Invitar Usuarios

**Paso 1:** Ve a **Invitaciones** → **Nueva Invitación**

**Paso 2:** Selecciona el tipo de usuario:

| Tipo | ¿Quién lo recibe? |
|------|-------------------|
| Encargado de Liga | Persona que gestionará una liga específica |
| Entrenador | Responsable de un equipo |
| Árbitro | Juez de partidos |
| Jugador | Miembro de un equipo |

**Paso 3:** Completa los datos:
- Correo electrónico
- Liga asignada (si aplica)
- Equipo asignado (para jugadores)

**Paso 4:** Haz clic en **"Enviar Invitación"**

El usuario recibirá un correo con un enlace único para registrarse.

---

## 📋 Guía para Encargados de Liga

### Tu Dashboard

Como encargado de liga, verás información específica de las ligas que tienes asignadas:

```
┌────────────────────────────────────────────────────────────┐
│  📊 MIS LIGAS ASIGNADAS                                     │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  🏆 Liga de Fútbol Primavera                               │
│     ├── 8 equipos registrados                              │
│     ├── 56 partidos programados                            │
│     ├── 12 partidos jugados                                │
│     └── Estado: Activa                                      │
│                                                             │
│  📅 Próximos Partidos de HOY                               │
│  ├── 18:00 - Tigres vs Leones                              │
│  ├── 19:30 - Águilas vs Pumas                              │
│  └── 21:00 - Lobos vs Halcones                             │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

### Funciones Disponibles

Como encargado de liga puedes:

- ✅ Gestionar temporadas de tus ligas
- ✅ Registrar y editar equipos
- ✅ Agregar jugadores
- ✅ Generar y modificar fixtures
- ✅ Registrar resultados de partidos
- ✅ Asignar árbitros a partidos
- ✅ Ver y gestionar finanzas de tu liga
- ✅ Enviar invitaciones a entrenadores, árbitros y jugadores

### Registrar Resultado de un Partido

**Paso 1:** Ve a **Fixtures** y busca el partido

**Paso 2:** Haz clic en **"Gestionar Partido"** o **"Partido en Vivo"**

**Paso 3:** En la pantalla del partido:

```
┌────────────────────────────────────────────────────────────┐
│                    ⚽ PARTIDO EN VIVO                       │
├────────────────────────────────────────────────────────────┤
│                                                             │
│         TIGRES            vs            LEONES             │
│                                                             │
│           ┌───┐                         ┌───┐              │
│           │ 2 │                         │ 1 │              │
│           └───┘                         └───┘              │
│         [−] [+]                       [−] [+]              │
│                                                             │
│  ⏱️ Estado: En Curso                                       │
│                                                             │
│  [Iniciar Partido]  [Pausar]  [Finalizar Partido]         │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

**Paso 4:** 
- Usa los botones **[+]** y **[−]** para ajustar el marcador
- Haz clic en **"Finalizar Partido"** cuando termine

**Paso 5:** El sistema automáticamente:
- Actualiza la tabla de posiciones
- Genera los cobros a los equipos
- Genera el pago a árbitros

---

## ⚽ Guía para Entrenadores

### Tu Dashboard

Al iniciar sesión como entrenador verás:

```
┌────────────────────────────────────────────────────────────┐
│  ⚽ DASHBOARD DE ENTRENADOR                                 │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  👥 Mi Equipo: TIGRES FC                                   │
│     ├── Liga: Liga de Fútbol Primavera                     │
│     ├── Jugadores: 15                                      │
│     └── Posición actual: 3°                                │
│                                                             │
│  📅 Próximos Partidos                                      │
│  ├── Sáb 15 Mar - 18:00 - vs Leones                       │
│  └── Sáb 22 Mar - 19:30 - vs Águilas                      │
│                                                             │
│  📊 Estadísticas del Equipo                                │
│  ├── Partidos jugados: 5                                   │
│  ├── Ganados: 3 | Empates: 1 | Perdidos: 1               │
│  └── Goles: 12 a favor, 6 en contra                       │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

### Gestionar tu Equipo

**Ver plantilla de jugadores:**

1. Ve a **Mi Equipo** → **Jugadores**
2. Verás la lista completa con:
   - Nombre del jugador
   - Número de camiseta
   - Posición
   - Estadísticas (goles, asistencias, tarjetas)

### Agregar Jugadores

**Opción 1: Agregar manualmente**

1. Ve a **Jugadores** → **Nuevo Jugador**
2. Completa el formulario:
   - Nombre completo
   - Fecha de nacimiento
   - Número de camiseta
   - Posición
   - Teléfono (opcional)
   - Foto (opcional)
3. Haz clic en **"Guardar"**

**Opción 2: Invitar jugador por correo**

1. Ve a **Invitaciones** → **Invitar Jugador**
2. Ingresa el correo del jugador
3. El jugador recibirá un enlace para registrarse

**Opción 3: Importar desde Excel**

1. Ve a **Jugadores** → **Importar**
2. Descarga la plantilla de Excel
3. Llena los datos de los jugadores
4. Sube el archivo
5. Revisa y confirma

### Ver Calendario y Partidos

1. Ve a **Fixtures** para ver todos los partidos
2. Filtra por tu equipo para ver solo tus partidos
3. Cada partido muestra:
   - Fecha y hora
   - Equipo rival
   - Ubicación/Cancha
   - Estado (Programado, En curso, Finalizado)

### Ver Tabla de Posiciones

1. Ve a **Standings**
2. Verás la tabla completa con:

```
┌────┬──────────────┬────┬────┬────┬────┬────┬────┬─────┐
│ #  │ Equipo       │ PJ │ PG │ PE │ PP │ GF │ GC │ PTS │
├────┼──────────────┼────┼────┼────┼────┼────┼────┼─────┤
│ 1  │ Leones       │ 5  │ 4  │ 1  │ 0  │ 15 │ 3  │ 13  │
│ 2  │ Águilas      │ 5  │ 4  │ 0  │ 1  │ 12 │ 5  │ 12  │
│ 3  │ ⭐ Tigres    │ 5  │ 3  │ 1  │ 1  │ 12 │ 6  │ 10  │
│ 4  │ Pumas        │ 5  │ 2  │ 2  │ 1  │ 8  │ 6  │ 8   │
│ ...│ ...          │ ...│ ...│ ...│ ...│ ...│ ...│ ... │
└────┴──────────────┴────┴────┴────┴────┴────┴────┴─────┘

PJ = Partidos Jugados | PG = Ganados | PE = Empatados
PP = Perdidos | GF = Goles a Favor | GC = Goles en Contra
```

### Ver Pagos Pendientes

1. Ve a **Pagos** → **Mis Pagos**
2. Verás los pagos pendientes de tu equipo:
   - Cuota de inscripción
   - Pagos por partido
   - Multas (si aplica)

---

## 🎯 Guía para Árbitros

### Tu Dashboard

```
┌────────────────────────────────────────────────────────────┐
│  🎯 DASHBOARD DE ÁRBITRO                                    │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  📅 Mis Partidos Asignados                                 │
│                                                             │
│  ┌─ HOY ────────────────────────────────────────────────┐  │
│  │ 18:00 - Tigres vs Leones                             │  │
│  │ Liga: Liga de Fútbol Primavera                       │  │
│  │ Cancha: Campo Norte                                  │  │
│  │ [Ver Detalles] [Iniciar Partido]                     │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  📊 Mis Estadísticas                                       │
│  ├── Partidos arbitrados: 15                               │
│  ├── Este mes: 5                                           │
│  └── Pagos pendientes: 2                                   │
│                                                             │
│  💰 Mis Pagos                                              │
│  ├── Confirmados: $1,500                                   │
│  └── Pendientes: $300                                      │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

### Gestionar un Partido

**Antes del partido:**

1. Revisa los detalles del partido asignado
2. Confirma tu asistencia

**Durante el partido:**

1. Haz clic en **"Iniciar Partido"** cuando comience
2. Actualiza el marcador en tiempo real:
   - Usa los botones **[+]** para agregar goles
   - Registra eventos importantes
3. Al terminar, haz clic en **"Finalizar Partido"**

**Después del partido:**

1. Verifica que el marcador final sea correcto
2. El sistema generará automáticamente tu pago

### Confirmar Pagos Recibidos

1. Ve a **Mis Pagos**
2. Verás los pagos pendientes de confirmación
3. Cuando recibas el pago del administrador:
   - Haz clic en **"Confirmar Recibido"**
   - El pago quedará marcado como completado

---

## 🏃 Guía para Jugadores

### Tu Dashboard

```
┌────────────────────────────────────────────────────────────┐
│  🏃 MI PERFIL DE JUGADOR                                    │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  👤 Juan Pérez García                                      │
│  📍 Equipo: Tigres FC                                      │
│  #️⃣ Número: 10                                             │
│  🎯 Posición: Delantero                                    │
│                                                             │
│  📊 Mis Estadísticas                                       │
│  ├── ⚽ Goles: 8                                           │
│  ├── 🎯 Asistencias: 5                                     │
│  ├── 🟨 Tarjetas amarillas: 2                              │
│  └── 🟥 Tarjetas rojas: 0                                  │
│                                                             │
│  📅 Próximo Partido                                        │
│  └── Sáb 15 Mar - 18:00 - vs Leones                       │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

### Funciones Disponibles

Como jugador puedes:

- ✅ Ver tu perfil y estadísticas
- ✅ Ver información de tu equipo
- ✅ Consultar el calendario de partidos
- ✅ Ver la tabla de posiciones
- ✅ Actualizar tu foto de perfil

### Ver Calendario

1. Ve a **Fixtures**
2. Verás todos los partidos de tu equipo
3. Cada partido muestra fecha, hora y rival

### Ver Tabla de Posiciones

1. Ve a **Standings**
2. Verás la posición actual de tu equipo en la liga

---

## 💰 Sistema Financiero

### Tipos de Ingresos

| Tipo | Descripción | ¿Cuándo se genera? |
|------|-------------|---------------------|
| **Cuota de inscripción** | Pago único por temporada | Al inscribir equipo |
| **Pago por partido** | Cobro por cada partido | Automático al finalizar partido |
| **Multas** | Penalizaciones | Manual, según incumplimiento |
| **Otros ingresos** | Patrocinios, ventas | Manual |

### Tipos de Egresos

| Tipo | Descripción | ¿Cuándo se genera? |
|------|-------------|---------------------|
| **Pago a árbitros** | Por partido arbitrado | Automático al finalizar partido |
| **Costos de cancha** | Alquiler de instalaciones | Manual |
| **Gastos administrativos** | Trofeos, material, etc. | Manual |
| **Otros gastos** | Varios | Manual |

### Sistema de Confirmación de Pagos

Los pagos pasan por un sistema de triple validación:

```
┌─────────────────────────────────────────────────────────────┐
│                  FLUJO DE CONFIRMACIÓN                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1️⃣ EQUIPO marca como "Pagado"                              │
│      │                                                       │
│      ▼                                                       │
│  2️⃣ ADMINISTRADOR confirma recepción                        │
│      │                                                       │
│      ▼                                                       │
│  3️⃣ SISTEMA registra el ingreso como confirmado             │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Dashboard Financiero (Solo Admin/Encargado)

```
┌────────────────────────────────────────────────────────────┐
│  💰 DASHBOARD FINANCIERO                                    │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  📈 Resumen del Período                                    │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │  Ingresos   │  │  Egresos    │  │  Balance    │        │
│  │  $15,000    │  │  $5,000     │  │  $10,000    │        │
│  │  ↑ 15%      │  │  ↓ 5%       │  │  ↑ 25%      │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│                                                             │
│  📊 Desglose de Ingresos                                   │
│  ├── Cuotas de inscripción: $5,000 (33%)                  │
│  ├── Pagos por partido: $8,000 (53%)                      │
│  ├── Multas: $500 (3%)                                    │
│  └── Otros: $1,500 (10%)                                  │
│                                                             │
│  📋 Pagos Pendientes: 5                                    │
│  [Ver todos] [Exportar PDF]                                │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

---

## 🌐 Páginas Públicas

### ¿Qué son las Páginas Públicas?

Cada liga tiene una página web pública accesible para cualquier persona, sin necesidad de iniciar sesión. Es ideal para:

- Aficionados que quieren seguir la liga
- Familiares de jugadores
- Patrocinadores
- Prensa local

### URL de tu Liga

```
https://flowfast.me/league/[nombre-de-tu-liga]

Ejemplo:
https://flowfast.me/league/liga-futbol-primavera-2025
```

### Contenido de la Página Pública

| Sección | Descripción |
|---------|-------------|
| **Inicio** | Información general de la liga |
| **Fixtures** | Calendario completo de partidos |
| **Standings** | Tabla de posiciones actualizada |
| **Teams** | Lista de equipos participantes |

### Compartir en Redes Sociales

Cada página incluye botones para compartir en:
- 📘 Facebook
- 🐦 Twitter
- 📱 WhatsApp
- 🔗 Copiar enlace

---

## ❓ Preguntas Frecuentes

### Acceso y Cuenta

**¿Cómo creo una cuenta?**
> Necesitas recibir una invitación del administrador de tu liga. No es posible registrarse sin invitación.

**¿Olvidé mi contraseña, qué hago?**
> En la pantalla de login, haz clic en "¿Olvidaste tu contraseña?" e ingresa tu correo. Recibirás un enlace para crear una nueva.

**¿Puedo cambiar mi correo electrónico?**
> Sí, ve a tu Perfil → Editar → Cambia el correo y guarda los cambios.

### Ligas y Temporadas

**¿Cuántas ligas puedo crear?**
> Depende de tu plan. El plan básico permite 2 ligas, el profesional 5, y el enterprise ilimitadas.

**¿Puedo tener varias temporadas activas?**
> Sí, puedes tener múltiples temporadas en una misma liga.

**¿Qué pasa si necesito cambiar la fecha de un partido?**
> Los administradores y encargados pueden modificar fechas. El sistema de apelaciones permite solicitar cambios que deben ser aprobados.

### Fixtures

**¿Cómo funciona la generación automática de fixtures?**
> El sistema usa el algoritmo Round Robin para generar un calendario donde todos los equipos juegan entre sí, respetando los días y horarios configurados.

**¿Puedo modificar un fixture ya generado?**
> Sí, pero los cambios pueden afectar otros partidos. Se recomienda hacer cambios antes de que inicie la temporada.

**¿Qué es un "BYE"?**
> Cuando hay un número impar de equipos, el equipo con "BYE" descansa esa jornada (no juega).

### Finanzas

**¿Los pagos se generan automáticamente?**
> Sí, al finalizar cada partido se generan automáticamente los cobros a los equipos y el pago a árbitros.

**¿Puedo agregar ingresos o gastos manuales?**
> Sí, los administradores y encargados pueden registrar ingresos y gastos adicionales manualmente.

**¿Cómo exporto un reporte financiero?**
> En el Dashboard Financiero, haz clic en "Exportar PDF" para descargar un reporte completo.

### Equipos y Jugadores

**¿Cuántos jugadores puede tener un equipo?**
> No hay límite establecido, cada equipo puede registrar los jugadores que necesite.

**¿Puedo transferir un jugador a otro equipo?**
> Esta funcionalidad está en desarrollo. Por ahora, se debe eliminar al jugador de un equipo y agregarlo al nuevo.

**¿Cómo subo la foto de un jugador?**
> Al crear o editar un jugador, hay una opción para subir foto. Formatos aceptados: JPG, PNG. Tamaño máximo: 2MB.

### Soporte

**¿Cómo contacto al soporte técnico?**
> Envía un correo a soporte@flowfast.me o usa el chat de ayuda dentro de la plataforma.

**¿Hay tutoriales en video?**
> Sí, visita nuestro canal de YouTube para ver tutoriales paso a paso.

---

## 📞 Soporte y Contacto

### ¿Necesitas Ayuda?

| Canal | Contacto |
|-------|----------|
| 📧 **Email** | soporte@flowfast.me |
| 💬 **Chat** | Disponible dentro de la plataforma |
| 📱 **WhatsApp** | +XX XXXX XXXX |
| 📺 **Tutoriales** | youtube.com/flowfast |

### Horario de Atención

| Día | Horario |
|-----|---------|
| Lunes a Viernes | 9:00 AM - 6:00 PM |
| Sábados | 9:00 AM - 2:00 PM |
| Domingos | Solo emergencias |

---

## 📝 Glosario

| Término | Definición |
|---------|------------|
| **Fixture** | Calendario de partidos generado automáticamente |
| **Jornada** | Conjunto de partidos que se juegan en la misma fecha |
| **Round Robin** | Sistema donde todos los equipos juegan entre sí |
| **Standings** | Tabla de posiciones de la liga |
| **BYE** | Descanso de un equipo cuando hay número impar de participantes |
| **Token** | Código único de invitación para registrarse |
| **Dashboard** | Panel principal con información resumida |
| **Temporada** | Período de competición dentro de una liga |

---

*Manual de Usuario - FlowFast SaaS*  
*Versión 1.0 - Noviembre 2025*  
*© 2025 FlowFast - Todos los derechos reservados*
