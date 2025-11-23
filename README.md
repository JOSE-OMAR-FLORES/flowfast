# FlowFast SaaS - Sistema de Administración de Ligas Deportivas

## 📋 Descripción General

**FlowFast SaaS** es una plataforma integral de gestión para ligas deportivas amateur y semi-profesionales que automatiza la administración, cobros, programación de partidos y seguimiento financiero de cualquier tipo de liga deportiva.

### 🎯 Objetivo Principal
Facilitar y agilizar la gestión completa de ligas deportivas, desde el registro de equipos hasta la generación de reportes financieros, proporcionando una experiencia moderna, personalizable y eficiente para todos los involucrados.

---

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 12 (PHP 8.3+)
- **Frontend**: Livewire 3 + Alpine.js
- **Estilos**: Tailwind CSS
- **Base de Datos**: MySQL 8.0+
- **Autenticación**: JWT (JSON Web Tokens)
- **Reportes**: PDF Generation (DomPDF/mPDF)

---

## 👥 Tipos de Usuario y Jerarquía

### 1. **Super Administrador** (Propietario del SaaS)
- Gestiona membresías de administradores
- Controla facturación y pagos del SaaS
- Envía notificaciones de renovación
- Acceso completo al sistema

### 2. **Administrador** (Dueño de Liga/Cancha)
- Acceso total a sus ligas y equipos
- Gestiona encargados de liga
- Ve reportes financieros consolidados
- Personaliza marca (logos, colores, etc.)

### 3. **Encargado de Liga/Cancha**
- Funciones delegadas por el administrador
- Gestiona únicamente las ligas asignadas
- Registra árbitros, entrenadores y jugadores
- Ve reportes de sus ligas específicas

### 4. **Árbitros** (Mesa de Anotadores + 2 Silbantes mínimo)
- Inicia y finaliza partidos
- Confirma pagos de equipos
- Gestiona información personal
- Confirma pagos recibidos

### 5. **Entrenador/Encargado de Equipo**
- Crea y gestiona su equipo
- Registra jugadores mediante tokens
- Apela cambios de fechas/horarios

### 6. **Jugadores**
- Perfil básico personal
- Ve información de su equipo
- Acceso limitado a estadísticas

---

## 🔐 Sistema de Autenticación y Tokens

### Flujo de Registro por Tokens
```
Super Admin → Admin (correo/contraseña)
Admin → Encargados de Liga/Árbitros/Entrenadores (tokens únicos)
Encargado → Árbitros/Entrenadores/Jugadores (tokens con restricciones)
Entrenador → Jugadores (tokens multi-uso por equipo)
```

### Características de los Tokens:
- **Información previa incluida**: Tipo de usuario, liga asignada, permisos
- **Tokens de jugadores**: Multi-uso para equipos completos
- **Jerarquía respetada**: Cada nivel solo puede crear tokens del nivel inferior
- **Seguridad**: Expiración automática y uso único (excepto jugadores)

---

## ⚽ Gestión de Ligas y Competencias

### Creación de Liga
El administrador/encargado define:
- **Deporte**: Fútbol, básquet, volley, etc.
- **Cuota de inscripción**: Monto fijo por equipo
- **Pago por partido**: Costo por equipo por partido jugado
- **Penalizaciones**: Multas por incumplimientos
- **Asignación de encargado**: De la lista de registrados

### Sistema de Temporadas y Jornadas
- **Temporadas**: Contenedores de equipos participantes
- **Jornadas**: Sistema Round Robin (simple o doble vuelta)
- **Formatos disponibles**:
  - Liga regular (más puntos = campeón)
  - Liga + Liguilla (primeros lugares compiten por título)

### Programación Automática
El sistema calcula automáticamente:
- **Fecha de inicio**: Definida por encargado
- **Días de juego**: Selección de días de la semana
- **Partidos diarios**: Cantidad máxima por día
- **Horarios**: Franjas horarias disponibles
- **Fecha de fin**: Calculada según equipos y formato

---

## 💰 Sistema Financiero Integral

### Flujos de Dinero Automatizados

#### Ingresos por Partido:
```
Partido Finalizado → 2 Ingresos Automáticos
├── Pago Equipo Local
└── Pago Equipo Visitante
```

#### Egresos por Partido:
```
Partido Finalizado → 1 Egreso Automático
└── Pago a Árbitros (según configuración de temporada)
```

### Métodos de Pago Disponibles
- **💵 Efectivo**: Pago presencial confirmado por encargado/admin
- **💳 Tarjeta**: Procesamiento automático con gateway de pagos
- **Registro detallado**: Cada método queda registrado en el sistema
- **Reportes específicos**: Separación por tipo de pago en reportes

### Sistema de Confirmación de Pagos (Triple Validación)
1. **Equipos**: Marcan como "pagado" y seleccionan método (efectivo/tarjeta)
2. **Encargado/Admin**: Confirma recepción del pago según método
3. **Sistema**: Registra en dashboard financiero con método de pago

#### Para Árbitros:
1. **Encargado/Admin**: Marca pago como realizado y selecciona método
2. **Árbitro**: Confirma recepción del pago
3. **Sistema**: Actualiza estado financiero con método utilizado

### Cuotas de Inscripción
- **Obligatorio**: Antes de participar en temporada
- **Definido**: En creación de liga
- **Verificado**: Por encargado/admin antes de inscripción

### Tipos Detallados de Ingresos y Egresos

#### **📈 INGRESOS (income_types)**
1. **Cuotas de Inscripción (registration_fees)**
   - Monto fijo por equipo por temporada
   - Pago único antes de participar
   - Método: efectivo/tarjeta

2. **Pagos por Partido (match_fees)**
   - Costo por equipo por cada partido jugado
   - Se genera automáticamente al finalizar partido
   - 2 ingresos por partido (equipo local + visitante)

3. **Multas y Penalizaciones (penalty_fees)**
   - Sanciones por incumplimientos
   - Llegadas tarde, conducta antideportiva
   - Monto definido en configuración de liga

4. **Ingresos Adicionales (other_income)**
   - Patrocinios, venta de productos
   - Ingresos extraordinarios
   - Configurables por administrador

#### **📉 EGRESOS (expense_types)**
1. **Pagos a Árbitros (referee_payments)**
   - Mesa de anotadores + 2 silbantes mínimo
   - Monto definido en configuración de temporada
   - 1 egreso automático por partido finalizado

2. **Costos de Cancha (venue_costs)**
   - Alquiler de instalaciones (opcional)
   - Mantenimiento y servicios
   - Si la liga no es dueña de las canchas

3. **Gastos Administrativos (administrative_expenses)**
   - Material deportivo, premiaciones
   - Gastos operativos de la liga
   - Definidos por administrador

4. **Otros Egresos (other_expenses)**
   - Gastos extraordinarios
   - Emergencias o imprevistos
   - Configurables por administrador

#### **🔄 Flujo Automático por Partido**
```
Al Finalizar Partido:
├── INGRESOS AUTOMÁTICOS (2)
│   ├── Equipo Local → [monto_por_partido] → [método_pago]
│   └── Equipo Visitante → [monto_por_partido] → [método_pago]
└── EGRESOS AUTOMÁTICOS (1)
    └── Árbitros → [monto_arbitraje] → [método_pago]

Resultado Neto: (2 × monto_partido) - monto_arbitraje = Ganancia por partido
```

---

## 📊 Dashboard Personalizado por Rol

### Dashboard del Administrador
- **Vista consolidada**: Todas las ligas y finanzas
- **Gestión de usuarios**: Crear/editar encargados
- **Reportes globales**: Ingresos/egresos de todas las ligas
- **Personalización**: Logos, colores, marca personal
- **Gestión de páginas públicas**: Configurar URL, contenido y diseño de liga
- **Analytics públicos**: Estadísticas de visitantes a páginas de liga

### Dashboard del Encargado de Liga
- **Vista específica**: Solo ligas asignadas
- **Gestión limitada**: Equipos y árbitros de su liga
- **Reportes locales**: Finanzas de sus ligas únicamente
- **Programación**: Partidos y temporadas
- **Página pública**: Edición de contenido y configuración de su liga
- **Estadísticas públicas**: Visualización de visitas a su página de liga

### Dashboard del Árbitro
- **Partidos asignados**: Próximos y completados
- **Gestión de pagos**: Confirmaciones pendientes
- **Perfil personal**: Información y disponibilidad
- **Historial**: Partidos arbitrados y pagos recibidos

---

## 🔄 Sistema de Apelaciones

### Flujo de Cambios de Fecha/Horario
```
Equipo Solicitante → Apelación de Cambio
├── Encargado de Liga (Aprobación 1/2)
└── Equipo Contrario (Aprobación 2/2)
    ├── Si AMBOS aprueban → Cambio automático en sistema
    └── Si UNO rechaza → Mantiene fecha original
```

### Ajustes Automáticos
- **Reprogramación**: El sistema ajusta automáticamente las jornadas
- **Notificaciones**: A todos los involucrados sobre el cambio
- **Actualización**: Calendarios y reportes reflejan nuevas fechas

---

## 🌐 Páginas Públicas de Liga

### Funcionalidades de la Página Pública
Cada liga tendrá su propia URL pública accesible para todos los usuarios sin necesidad de registro:

#### **📊 Tabla de Posiciones en Tiempo Real**
- Actualización automática después de cada partido
- Posición, puntos, partidos jugados, ganados, empatados, perdidos
- Diferencia de goles y goles a favor/en contra
- Ordenamiento automático por criterios de desempate

#### **📅 Calendario de Partidos**
- **Próximos partidos**: Fecha, hora, equipos, cancha asignada
- **Partidos en vivo**: Indicador visual de partidos en curso
- **Resultados recientes**: Últimos partidos finalizados con marcadores
- **Filtros**: Por equipo, fecha, jornada específica

#### **🏆 Información General de la Liga**
- Nombre y logo personalizado de la liga
- Temporada actual y formato de competencia
- Número total de equipos y jornadas
- Estadísticas generales (goles totales, promedio por partido, etc.)

#### **📱 Diseño Responsive Público**
- Optimizado para dispositivos móviles
- Carga rápida para audiencias masivas
- SEO optimizado para búsquedas locales
- Compartible en redes sociales

### URL Structure
```
https://flowfast-saas.com/liga/[nombre-liga-slug]
https://flowfast-saas.com/liga/liga-futbol-amateur-2024
https://flowfast-saas.com/liga/basquet-municipal-verano
```

### Personalización de Página Pública
- **Colores de marca**: Aplicados en toda la página
- **Logo de liga**: Visible en header y como favicon
- **Información de contacto**: Datos del administrador/encargado
- **Redes sociales**: Enlaces a perfiles oficiales de la liga

---

## 📈 Sistema de Reportes

### Reportes en PDF Disponibles por Rol:

#### Super Administrador:
- Reporte global de todas las membresías
- Ingresos del SaaS por período
- Estadísticas de uso del sistema

#### Administrador:
- Consolidado financiero de todas sus ligas (separado por efectivo/tarjeta)
- Reporte de equipos y jugadores registrados
- Análisis de rendimiento por liga
- Estadísticas de métodos de pago utilizados

#### Encargado de Liga:
- Finanzas específicas de ligas asignadas (detalle por método de pago)
- Reporte de partidos y resultados
- Estado de pagos por equipo (efectivo/tarjeta)

#### Árbitros:
- Historial de partidos arbitrados
- Reporte de pagos recibidos
- Estadísticas personales

---

## 💳 Plan de Membresías

### Estructura de Suscripción Mensual

#### Plan Básico ($29.99/mes)
- Hasta 2 ligas simultáneas
- Máximo 16 equipos por liga
- Reportes básicos
- Soporte por email

#### Plan Profesional ($49.99/mes)
- Hasta 5 ligas simultáneas
- Equipos ilimitados
- Reportes avanzados
- Personalización completa
- Soporte prioritario

#### Plan Enterprise ($99.99/mes)
- Ligas ilimitadas
- Multi-encargados por liga
- API access
- Reportes personalizados
- Soporte 24/7
- Manager dedicado

### Sistema de Notificaciones
- **7 días antes**: Primera notificación de renovación
- **3 días antes**: Recordatorio urgente
- **Día del vencimiento**: Notificación final
- **Post-vencimiento**: Suspensión gradual de servicios

---

## 🎨 Diseño y UX/UI

### Paleta de Colores Principal
- **Rojo primario**: #DC2626 (Red-600)
- **Rojo secundario**: #EF4444 (Red-500)
- **Rojo oscuro**: #991B1B (Red-800)
- **Grises**: Escala completa para textos y backgrounds
- **Acentos**: Verde para confirmaciones, Amarillo para advertencias

### Componentes de Interface
- **Sidebar desplegable**: Navegación principal con iconos
- **Design responsive**: Optimizado para móvil, tablet y desktop
- **Iconografía**: Font Awesome / Heroicons
- **Componentes**: Cards, modales, formularios dinámicos

### Personalización por Administrador
- **Logo personalizado**: Upload y gestión de imágenes
- **Colores de marca**: Selector de paleta personalizada
- **Nombre de liga**: Branding personalizado en todo el sistema

---

## 🏗️ Arquitectura del Sistema

### Estructura de Base de Datos (MySQL)

#### **Usuarios y Autenticación**
```
users (tabla polimórfica base)
├── user_types (admin, league_manager, referee, coach, player)
├── admins (datos específicos de administradores)
├── league_managers (encargados de liga)
├── referees (árbitros: mesa + silbantes)
├── coaches (entrenadores/encargados de equipo)
└── players (jugadores)

tokens (sistema de invitaciones)
├── token_types (por tipo de usuario)
├── token_usage_tracking
└── token_expiration
```

#### **Ligas y Competencias**
```
leagues
├── sports (fútbol, básquet, volley, etc.)
├── league_settings (configuración de pagos y reglas)
├── league_public_pages (configuración páginas públicas)
└── league_branding (logos, colores personalizados)

seasons (temporadas)
├── teams (equipos por temporada)
├── season_settings (formato: liga regular/liguilla)
└── rounds (jornadas generadas automáticamente)
    └── matches (partidos individuales)
        ├── match_status (programado, en_curso, finalizado)
        ├── match_results (marcadores finales)
        └── match_officials (árbitros asignados)
```

#### **Sistema Financiero Integral**

##### **Tipos de Ingresos**
```
income_types
├── registration_fees (cuotas de inscripción)
├── match_fees (pago por partido por equipo)
├── penalty_fees (multas y penalizaciones)
└── other_income (ingresos adicionales)

incomes (registro de todos los ingresos)
├── income_type_id
├── amount
├── payment_method (efectivo/tarjeta)
├── payment_status (pendiente, pagado, confirmado)
├── payer_info (equipo que paga)
├── receiver_info (liga/admin)
├── confirmation_chain (triple validación)
└── related_match_id (si aplica)
```

##### **Tipos de Egresos**
```
expense_types
├── referee_payments (pagos a árbitros)
├── venue_costs (costos de cancha si aplica)
├── administrative_expenses (gastos administrativos)
└── other_expenses (otros egresos)

expenses (registro de todos los egresos)
├── expense_type_id
├── amount
├── payment_method (efectivo/tarjeta)
├── payment_status (pendiente, pagado, confirmado)
├── payee_info (árbitro/proveedor)
├── payer_info (admin/encargado)
├── confirmation_chain (doble validación)
└── related_match_id (si aplica)
```

##### **Control de Pagos**
```
payment_confirmations (sistema de triple validación)
├── confirmation_type (income/expense)
├── step_1_payer (equipo marca como pagado)
├── step_2_receiver (admin/encargado confirma)
├── step_3_system (sistema registra)
├── payment_proof (comprobantes si hay)
└── timestamps (fecha de cada confirmación)

payment_methods
├── cash_transactions (seguimiento efectivo)
├── card_transactions (gateway de pagos)
└── payment_gateways_config (configuración TPV)
```

#### **Sistema de Membresías SaaS**
```
subscription_plans
├── plan_features (límites por plan)
├── plan_pricing (precios mensuales)
└── plan_permissions (funcionalidades permitidas)

admin_subscriptions
├── current_plan
├── billing_cycle
├── payment_history
├── subscription_status
└── usage_metrics (ligas activas, equipos, etc.)

payment_notifications
├── notification_type (7días, 3días, vencimiento)
├── notification_status (enviado, leído, acción tomada)
├── notification_schedule
└── auto_suspension_log
```

#### **Analytics y Reportes**
```
public_page_analytics
├── page_views (visitas por liga)
├── visitor_demographics
├── popular_content (partidos más vistos)
└── engagement_metrics

financial_reports_cache
├── income_summaries (por liga, período, método)
├── expense_summaries (por tipo, período, usuario)
├── profit_loss_reports
└── tax_reports (para administración)
```

#### **Sistema de Apelaciones**
```
match_appeals
├── appeal_type (cambio fecha/hora/cancha)
├── requesting_team
├── affected_match
├── league_manager_approval
├── opposing_team_approval
├── system_auto_adjustment
└── appeal_history
```

### Seguridad y Permisos
- **JWT Authentication**: Tokens seguros con expiración
- **Role-based Access Control**: Permisos granulares por tipo de usuario
- **Data Isolation**: Cada admin ve solo su información
- **API Rate Limiting**: Prevención de abuso del sistema

---

## 🚀 Funcionalidades Clave

### ✅ Gestión Completa de Ligas
- Creación y configuración de ligas multi-deporte
- Sistema de temporadas y equipos
- Programación automática de partidos (Round Robin)

### ✅ Control Financiero Automatizado
- Tracking automático de ingresos y egresos
- Sistema de triple confirmación de pagos
- Reportes financieros detallados en PDF

### ✅ Sistema de Usuarios Jerárquico
- 6 tipos de usuario con permisos específicos
- Sistema de tokens para registro controlado
- Dashboards personalizados por rol

### ✅ Gestión de Arbitraje
- Asignación flexible de árbitros por partido
- Control de pagos a mesa de anotadores y silbantes
- Confirmación bidireccional de pagos

### ✅ Sistema de Apelaciones
- Cambios de fecha/horario con doble aprobación
- Ajuste automático de calendarios
- Notificaciones a todos los involucrados

### ✅ Páginas Públicas de Liga
- Página web pública para cada liga
- Tabla de posiciones en tiempo real
- Calendario de próximos partidos
- Resultados y estadísticas históricas
- Branding personalizado por liga

---

## 📱 Responsive Design

### Breakpoints Principales
- **Mobile**: 320px - 768px (Stack vertical, menú hamburguesa)
- **Tablet**: 768px - 1024px (Sidebar colapsible)
- **Desktop**: 1024px+ (Sidebar completo, múltiples columnas)

### Optimizaciones Móviles
- **Touch-friendly**: Botones y controles adaptados
- **Navegación intuitiva**: Gestos swipe y tap optimizados
- **Carga rápida**: Lazy loading y optimización de imágenes
- **Offline capability**: Funciones básicas sin conexión

---

## 🔧 Instalación y Configuración

### Requisitos del Sistema
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer
- NPM/Yarn

### Variables de Entorno Clave
```env
APP_NAME="FlowFast SaaS"
DB_CONNECTION=mysql
JWT_SECRET=your_jwt_secret_key
MAIL_MAILER=smtp
STRIPE_KEY=your_stripe_key (para pagos)
```

---

## � Estado Actual del Proyecto

### ✅ COMPLETADO (100%)

#### 🎨 Páginas Públicas (6 páginas)
- Home con hero y características
- Directorio de ligas
- Páginas por liga: Home, Fixtures, Standings, Teams
- Design responsive con gradientes modernos
- **Documentación**: `README-PUBLIC-PAGES.md`

#### 💌 Sistema de Invitaciones (Token-based)
- Backend completo (Index, Create, Accept)
- Email con HTML template estilizado
- Aceptación pública con registro
- 4 tipos de roles: league_manager, coach, player, referee
- **Documentación**: `SISTEMA-INVITACIONES-COMPLETADO.md`

#### 🔐 Sistema de Permisos por Roles
- RoleMiddleware funcional
- 18 grupos de rutas protegidas
- 5 roles: admin, league_manager, coach, player, referee
- Validación a nivel de componente
- **Documentación**: `ESTADO-INVITACIONES-Y-PERMISOS.md`

#### 👥 CRUD de Jugadores (Completo)
- Index con 5 filtros avanzados
- Create/Edit con 11 campos
- Gestión de fotos
- Tracking de estadísticas (goles, asistencias, tarjetas)
- Unique jersey validation
- **Documentación**: `CRUD-JUGADORES-COMPLETADO.md`

#### 📥 Importación Masiva de Jugadores
- Soporte CSV y Excel (.xlsx, .xls)
- Validación robusta con vista previa
- Proceso guiado en 3 pasos
- Plantilla descargable
- Normalización español/inglés
- Permisos por rol
- **Documentación**: `README-IMPORTACION-JUGADORES.md`

#### ⚽ Partidos en Vivo
- Gestión en tiempo real de partidos
- 7 tipos de eventos (goles, tarjetas, cambios, penales)
- Actualización automática de estadísticas
- Scoreboard dinámico con animaciones
- Timeline de eventos con emojis
- Control de ciclo de vida (Programado → En Vivo → Finalizado)
- Reversión de eventos (delete con recalculo de stats)
- **Documentación**: `README-PARTIDOS-EN-VIVO.md`

#### 🏆 Sistema de Standings
- Tabla de posiciones dinámica
- 11 métricas (PJ, PG, PE, PP, GF, GC, DG, Pts, etc.)
- Filtrado por liga/temporada
- Orden automático por puntos → DG → GF
- **Documentación**: `README-STANDINGS.md`

#### 🗓️ Sistema de Fixtures (Generación Automática)
- Algoritmo Round Robin
- Soporte Single/Double Round
- Días y horarios configurables
- Validación de venues
- **Documentación**: `README-FRIENDLY-MATCHES.md`, `README-MEJORAS-SEASONS-FIXTURES.md`

#### 💰 Sistema Financiero (4 Partes)
- Dashboard financiero
- Registro de ingresos/egresos
- Reporte por temporada
- Flujo de pagos de árbitros
- **Documentación**: `README-FINANCIAL-PART1.md` hasta `README-FINANCIAL-PART4.md`

#### 🏟️ Core Modules
- Ligas (CRUD completo)
- Temporadas (CRUD + configuración días/horarios)
- Equipos (CRUD con logos)
- Venues (Gestión de canchas)
- **Documentación**: `README-LEAGUES-CRUD.md`, `README-CONFIGURACION-DIAS-HORARIOS.md`

---

### 🚧 Roadmap Pendiente

#### FASE 2 - Módulos de Valor (2-3 semanas)

##### 🎯 PRIORIDAD ALTA
- [ ] **Partidos en Vivo** (~4 horas)
  - Registro de eventos en tiempo real
  - Actualización automática de stats
  - Timeline de eventos
  - Estado del partido (scheduled, live, finished)

- [ ] **Dashboard de Estadísticas** (~4 horas)
  - Gráficos con Chart.js
  - Top scorers / Top assists
  - Análisis de tarjetas
  - Comparativa de equipos

##### 🎯 PRIORIDAD MEDIA
- [ ] **Transferencias de Jugadores** (~2 horas)
  - Mover jugadores entre equipos
  - Historial de transferencias
  - Actualización de jersey number

- [ ] **Sistema de Suspensiones** (~3 horas)
  - Gestión automática por tarjetas rojas
  - Multas por acumulación de amarillas
  - Dashboard de suspensiones activas

- [ ] **Notificaciones** (~3 horas)
  - Email notifications
  - In-app notifications
  - Recordatorios de partidos

#### FASE 3 - Optimizaciones (1-2 semanas)
- [ ] **Responsive Design** (móviles/tablets)
- [ ] **Performance** (caching, lazy loading)
- [ ] **Testing** (PHPUnit, Pest)
- [ ] **SEO** (meta tags, sitemaps)

#### FASE 4 - Features Avanzadas (2-3 semanas)
- [ ] **Sistema de Apelaciones**
- [ ] **Personalización de Marca**
- [ ] **Multi-idioma**
- [ ] **Reportes PDF avanzados**

---

## 📦 Archivos de Documentación

```
README.md                                    (este archivo - overview general)
README-AUTH.md                              (sistema de autenticación)
README-BACKEND.md                           (arquitectura backend)
README-DATABASE.md                          (estructura de base de datos)
README-FRONTEND.md                          (componentes frontend)
README-LEAGUES-CRUD.md                      (gestión de ligas)
README-CONFIGURACION-DIAS-HORARIOS.md       (config de temporadas)
README-FRIENDLY-MATCHES.md                  (partidos amistosos)
README-MEJORAS-SEASONS-FIXTURES.md          (mejoras fixtures)
README-STANDINGS.md                         (tabla de posiciones)
README-FINANCIAL-PART1.md hasta PART4.md   (sistema financiero)
SISTEMA-INVITACIONES-COMPLETADO.md          (invitaciones token-based)
ESTADO-INVITACIONES-Y-PERMISOS.md           (verificación permisos)
CRUD-JUGADORES-COMPLETADO.md                (gestión jugadores)
README-IMPORTACION-JUGADORES.md             (importación CSV/Excel) ← NUEVO
README-SIDEBAR-SUBMENUS.md                  (navegación sidebar)
README-NUEVO-SIDEBAR.md                     (rediseño sidebar)
README-RESPONSIVE-SIDEBAR.md                (sidebar responsive)
SIDEBAR-FIX-README.md                       (fixes sidebar)
PROGRESO-DESARROLLO.md                      (tracking progreso)
PROGRESO-FASE-2.md                          (tracking fase 2)
```

---

## 📋 Roadmap de Desarrollo

### Fase 1: Core System ✅ COMPLETADO
- ✅ Autenticación JWT y sistema de roles
- ✅ CRUD básico de usuarios y ligas
- ✅ Dashboard base para cada tipo de usuario

### Fase 2: Liga Management ✅ COMPLETADO + EN PROGRESO
- ✅ Sistema de temporadas y equipos
- ✅ Generación automática de jornadas (Round Robin)
- ✅ Gestión básica de partidos
- ✅ Páginas públicas con tabla de posiciones y calendario
- ✅ CRUD de jugadores con stats
- ✅ Importación masiva CSV/Excel ← NUEVO
- 🚧 Partidos en vivo (siguiente)
- 🚧 Dashboard de estadísticas
- 🚧 Transferencias de jugadores

### Fase 3: Financial System ✅ COMPLETADO
- ✅ Sistema de pagos automático
- ✅ Confirmaciones de pago triple validación
- ✅ Reportes financieros en PDF

### Fase 4: Advanced Features 🚧 PLANEADO
- [ ] Sistema de apelaciones
- [ ] Personalización de marca
- [ ] Optimización responsive

### Fase 5: SaaS Features 🚧 PLANEADO
- [ ] Sistema de membresías
- [ ] Notificaciones automatizadas
- [ ] Panel de super administrador

---

## 📞 Contacto y Soporte

**Desarrollador Principal**: [Tu Nombre]
**Email**: [tu-email@ejemplo.com]
**Repositorio**: [URL del repositorio]

---

*Este documento será actualizado conforme avance el desarrollo del proyecto.*
