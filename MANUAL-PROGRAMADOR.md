# 📘 Manual del Programador - FlowFast SaaS

> **Versión:** 1.0  
> **Fecha:** Noviembre 2025  
> **Framework:** Laravel 12 + Livewire 3  

---

## 📋 Índice

1. [Introducción](#-introducción)
2. [Stack Tecnológico](#-stack-tecnológico)
3. [Requisitos del Sistema](#-requisitos-del-sistema)
4. [Instalación y Configuración](#-instalación-y-configuración)
5. [Estructura del Proyecto](#-estructura-del-proyecto)
6. [Arquitectura del Sistema](#-arquitectura-del-sistema)
7. [Base de Datos](#-base-de-datos)
8. [Modelos y Relaciones](#-modelos-y-relaciones)
9. [Sistema de Autenticación](#-sistema-de-autenticación)
10. [API REST](#-api-rest)
11. [Componentes Livewire](#-componentes-livewire)
12. [Servicios](#-servicios)
13. [Jobs y Colas](#-jobs-y-colas)
14. [Sistema de Rutas](#-sistema-de-rutas)
15. [Guías de Desarrollo](#-guías-de-desarrollo)
16. [Testing](#-testing)
17. [Despliegue](#-despliegue)
18. [Troubleshooting](#-troubleshooting)

---

## 🎯 Introducción

### ¿Qué es FlowFast SaaS?

FlowFast SaaS es una plataforma integral de gestión para ligas deportivas amateur y semi-profesionales. El sistema automatiza:

- 📊 Gestión de ligas, temporadas y equipos
- 🏆 Generación automática de fixtures (Round Robin)
- 💰 Control financiero (ingresos, egresos, pagos)
- 👥 Sistema de usuarios con roles jerárquicos
- 📱 Páginas públicas para cada liga

### Objetivo del Manual

Este manual proporciona toda la información técnica necesaria para:
- Entender la arquitectura del sistema
- Desarrollar nuevas funcionalidades
- Mantener y depurar el código existente
- Desplegar la aplicación en producción

---

## 🛠️ Stack Tecnológico

### Backend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **PHP** | 8.2+ | Lenguaje de programación |
| **Laravel** | 12.x | Framework principal |
| **Laravel Sanctum** | 4.x | Autenticación API |
| **Livewire** | 3.x | Componentes reactivos |

### Frontend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **Alpine.js** | 3.x | Interactividad JS |
| **Tailwind CSS** | 3.x | Estilos |
| **Vite** | 5.x | Build tool |

### Base de Datos
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **MySQL** | 8.0+ | Base de datos principal |

### Herramientas Adicionales
| Tecnología | Propósito |
|------------|-----------|
| **Stripe** | Procesamiento de pagos |
| **PHPSpreadsheet** | Importación/exportación Excel |
| **Laravel Breeze** | Scaffolding de autenticación |

---

## 💻 Requisitos del Sistema

### Requisitos Mínimos

```
PHP >= 8.2
Composer >= 2.0
Node.js >= 18.0
MySQL >= 8.0
```

### Extensiones PHP Requeridas

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_MySQL
- Tokenizer
- XML
- Zip
```

### Configuración Recomendada de PHP

```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
```

---

## ⚙️ Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone https://github.com/JOSE-OMAR-FLORES/flowfast.git
cd flowfast-saas
```

### 2. Instalar Dependencias

```bash
# Dependencias PHP
composer install

# Dependencias Node.js
npm install
```

### 3. Configurar Variables de Entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configuración del .env

```env
# Aplicación
APP_NAME="FlowFast SaaS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flowfast_saas
DB_USERNAME=root
DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8000

# Stripe (opcional)
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="hello@flowfast.me"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Ejecutar Migraciones y Seeders

```bash
# Crear tablas
php artisan migrate

# Datos iniciales (deportes, admin de prueba)
php artisan db:seed
```

### 6. Compilar Assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 7. Iniciar Servidor de Desarrollo

```bash
# Servidor Laravel
php artisan serve

# O usar el script combinado
composer dev
```

---

## 📁 Estructura del Proyecto

```
flowfast-saas/
├── app/
│   ├── Console/             # Comandos Artisan personalizados
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/         # Controladores API REST
│   │   │   │   ├── FixtureController.php
│   │   │   │   ├── InvitationController.php
│   │   │   │   ├── LeagueController.php
│   │   │   │   ├── SeasonController.php
│   │   │   │   ├── SportController.php
│   │   │   │   └── TeamController.php
│   │   │   ├── Auth/        # Controladores de autenticación
│   │   │   └── BaseController.php
│   │   └── Middleware/
│   │       └── CheckUserRole.php
│   ├── Jobs/                # Jobs para colas
│   │   ├── GenerateMatchFeesJob.php
│   │   ├── GenerateRefereePaymentsJob.php
│   │   └── MarkOverdueIncomesJob.php
│   ├── Livewire/            # Componentes Livewire
│   │   ├── Admin/           # Panel de administración
│   │   ├── Coach/           # Área de entrenadores
│   │   ├── Financial/       # Módulo financiero
│   │   ├── Fixtures/        # Gestión de fixtures
│   │   ├── Invitations/     # Sistema de invitaciones
│   │   ├── Leagues/         # CRUD de ligas
│   │   ├── Matches/         # Gestión de partidos
│   │   ├── Payments/        # Gestión de pagos
│   │   ├── Players/         # CRUD de jugadores
│   │   ├── Public/          # Páginas públicas
│   │   ├── Referee/         # Área de árbitros
│   │   ├── Seasons/         # CRUD de temporadas
│   │   ├── Standings/       # Tabla de posiciones
│   │   └── Teams/           # CRUD de equipos
│   ├── Mail/                # Clases de correo
│   │   └── InvitationMail.php
│   ├── Models/              # Modelos Eloquent
│   │   ├── Admin.php
│   │   ├── BaseModel.php
│   │   ├── Coach.php
│   │   ├── Expense.php
│   │   ├── Fixture.php
│   │   ├── GameMatch.php
│   │   ├── Income.php
│   │   ├── InvitationToken.php
│   │   ├── League.php
│   │   ├── LeagueManager.php
│   │   ├── Player.php
│   │   ├── Referee.php
│   │   ├── Round.php
│   │   ├── Season.php
│   │   ├── Sport.php
│   │   ├── Standing.php
│   │   ├── Team.php
│   │   ├── User.php
│   │   └── Venue.php
│   ├── Observers/           # Observers de modelos
│   ├── Providers/           # Service Providers
│   └── Services/            # Lógica de negocio
│       ├── ExpenseService.php
│       ├── FinancialDashboardService.php
│       ├── IncomeService.php
│       ├── RoundRobinService.php
│       ├── StandingsService.php
│       └── StripeService.php
├── config/                  # Configuraciones
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/             # Seeders
├── public/                  # Assets públicos
├── resources/
│   ├── css/                 # Estilos
│   ├── js/                  # JavaScript
│   └── views/
│       ├── components/      # Componentes Blade
│       ├── layouts/         # Layouts principales
│       └── livewire/        # Vistas Livewire
├── routes/
│   ├── api.php              # Rutas API
│   ├── auth.php             # Rutas de autenticación
│   └── web.php              # Rutas web
├── storage/                 # Almacenamiento
└── tests/                   # Tests
```

---

## 🏗️ Arquitectura del Sistema

### Patrón MVC + Service Layer

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENTE                               │
│              (Navegador / API Consumer)                      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                         RUTAS                                │
│              web.php / api.php / auth.php                    │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┼───────────────┐
              ▼               ▼               ▼
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│   MIDDLEWARE    │ │   MIDDLEWARE    │ │   MIDDLEWARE    │
│  auth:sanctum   │ │  role:admin     │ │  CheckUserRole  │
└─────────────────┘ └─────────────────┘ └─────────────────┘
              │               │               │
              └───────────────┼───────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      CONTROLADORES                           │
│         API Controllers / Livewire Components                │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        SERVICIOS                             │
│    RoundRobinService / FinancialService / etc.              │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                         MODELOS                              │
│              Eloquent ORM + Relaciones                       │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      BASE DE DATOS                           │
│                         MySQL                                │
└─────────────────────────────────────────────────────────────┘
```

### Flujo de una Petición Web (Livewire)

```
1. Usuario hace clic en botón
2. Alpine.js/Livewire captura el evento
3. Livewire envía petición AJAX al servidor
4. Middleware verifica autenticación y roles
5. Componente Livewire procesa la lógica
6. Servicio ejecuta lógica de negocio
7. Modelo interactúa con la BD
8. Componente actualiza su estado
9. Livewire envía respuesta con HTML actualizado
10. DOM se actualiza automáticamente
```

### Flujo de una Petición API

```
1. Cliente envía petición HTTP con token Bearer
2. Sanctum valida el token
3. Middleware verifica permisos
4. Controller procesa la petición
5. Servicio ejecuta lógica de negocio
6. Modelo interactúa con la BD
7. Controller formatea respuesta JSON
8. Cliente recibe respuesta
```

---

## 🗄️ Base de Datos

### Diagrama de Entidades Principal

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│    users    │────▶│   admins    │     │   sports    │
│             │     │             │     │             │
│ - id        │     │ - id        │     │ - id        │
│ - email     │     │ - user_id   │     │ - name      │
│ - password  │     │ - name      │     │ - slug      │
│ - user_type │     │ - phone     │     │ - config    │
│ - userable  │     └─────────────┘     └─────────────┘
└─────────────┘                                │
      │                                        │
      ▼                                        ▼
┌─────────────────────────────────────────────────────┐
│                      leagues                         │
│                                                      │
│ - id                - registration_fee               │
│ - name              - match_fee                      │
│ - slug              - penalty_fee                    │
│ - sport_id          - referee_payment                │
│ - admin_id          - status                         │
│ - manager_id        - is_public                      │
└─────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────┐
│                      seasons                         │
│                                                      │
│ - id                - format (league/playoff)       │
│ - league_id         - round_robin_type              │
│ - name              - game_days (JSON)              │
│ - start_date        - time_slots (JSON)             │
│ - end_date          - status                        │
└─────────────────────────────────────────────────────┘
           │                          │
           ▼                          ▼
┌─────────────────┐         ┌─────────────────┐
│      teams      │         │    fixtures     │
│                 │         │                 │
│ - id            │         │ - id            │
│ - name          │         │ - season_id     │
│ - coach_id      │         │ - generated_at  │
│ - logo          │         │ - status        │
└─────────────────┘         └─────────────────┘
         │                           │
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│    players      │         │     rounds      │
│                 │         │                 │
│ - id            │         │ - id            │
│ - team_id       │         │ - fixture_id    │
│ - first_name    │         │ - round_number  │
│ - last_name     │         │ - date          │
│ - jersey_number │         └─────────────────┘
└─────────────────┘                  │
                                     ▼
                          ┌─────────────────┐
                          │  game_matches   │
                          │                 │
                          │ - id            │
                          │ - round_id      │
                          │ - home_team_id  │
                          │ - away_team_id  │
                          │ - home_score    │
                          │ - away_score    │
                          │ - status        │
                          │ - played_at     │
                          └─────────────────┘
```

### Migraciones Importantes

| Archivo | Descripción |
|---------|-------------|
| `create_users_table.php` | Tabla de usuarios con autenticación |
| `create_sports_table.php` | Catálogo de deportes |
| `create_admins_table.php` | Perfil de administradores |
| `create_leagues_table.php` | Ligas principales |
| `create_seasons_table.php` | Temporadas por liga |
| `create_teams_table.php` | Equipos participantes |
| `create_game_matches_table.php` | Partidos individuales |
| `create_incomes_table.php` | Registro de ingresos |
| `create_expenses_table.php` | Registro de egresos |
| `create_invitation_tokens_table.php` | Tokens de invitación |

### Comandos de Base de Datos

```bash
# Ver estado de migraciones
php artisan migrate:status

# Ejecutar migraciones pendientes
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Resetear y volver a migrar
php artisan migrate:fresh

# Migrar con seeders
php artisan migrate:fresh --seed
```

---

## 📊 Modelos y Relaciones

### BaseModel

Todos los modelos extienden de `BaseModel` para funcionalidad común:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;

    // Funcionalidad compartida por todos los modelos
}
```

### User (Modelo Principal de Autenticación)

```php
<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'email',
        'password',
        'user_type',        // admin, league_manager, referee, coach, player
        'userable_id',      // ID del perfil específico
        'userable_type',    // Clase del perfil (Admin, Coach, etc.)
    ];

    // Relación polimórfica con el perfil
    public function userable()
    {
        return $this->morphTo();
    }

    // Helpers de verificación de rol
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function hasRole(string $role): bool
    {
        return $this->user_type === $role;
    }
}
```

### League (Modelo de Liga)

```php
<?php

namespace App\Models;

class League extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'sport_id',
        'admin_id',
        'manager_id',
        'description',
        'is_public',
        'registration_fee',
        'match_fee',
        'penalty_fee',
        'referee_payment',
        'status',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'registration_fee' => 'decimal:2',
        'match_fee' => 'decimal:2',
    ];

    // Relaciones
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function teams(): HasManyThrough
    {
        return $this->hasManyThrough(Team::class, Season::class);
    }

    // Métodos de negocio
    public function getCurrentSeason()
    {
        return $this->seasons()
            ->where('status', 'active')
            ->latest()
            ->first();
    }
}
```

### Mapa de Relaciones

```
User
├── morphTo → userable (Admin, LeagueManager, Referee, Coach, Player)
└── hasMany → invitationTokens

Admin
├── morphOne → user
└── hasMany → leagues

League
├── belongsTo → admin
├── belongsTo → sport
├── belongsTo → manager (LeagueManager)
├── hasMany → seasons
├── hasMany → incomes
├── hasMany → expenses
├── hasMany → venues
└── hasMany → invitationTokens

Season
├── belongsTo → league
├── hasMany → fixtures
├── hasMany → rounds
├── belongsToMany → teams (pivot: season_team)
└── hasMany → gameMatches

Team
├── belongsTo → coach
├── belongsToMany → seasons
├── hasMany → players
├── hasMany → homeMatches (GameMatch)
└── hasMany → awayMatches (GameMatch)

GameMatch
├── belongsTo → round
├── belongsTo → homeTeam (Team)
├── belongsTo → awayTeam (Team)
├── hasMany → matchEvents
└── hasMany → matchOfficials
```

---

## 🔐 Sistema de Autenticación

### Tipos de Usuario

| Tipo | Descripción | Permisos |
|------|-------------|----------|
| `admin` | Dueño de ligas | Acceso total a sus ligas |
| `league_manager` | Encargado de liga | Gestiona ligas asignadas |
| `referee` | Árbitro | Gestiona partidos asignados |
| `coach` | Entrenador | Gestiona su equipo |
| `player` | Jugador | Acceso de solo lectura |

### Middleware de Roles

```php
// app/Http/Middleware/CheckUserRole.php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'No autenticado'], 401);
    }

    if (!in_array($user->user_type, $roles)) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    return $next($request);
}
```

### Uso en Rutas

```php
// Solo admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/leagues/create', LeaguesCreate::class);
});

// Admin o league_manager
Route::middleware(['auth', 'role:admin,league_manager'])->group(function () {
    Route::get('/admin/leagues', LeaguesIndex::class);
});

// Múltiples roles
Route::middleware(['role:admin,league_manager,referee'])->group(function () {
    Route::get('/admin/matches/{matchId}/live', MatchLive::class);
});
```

### Sistema de Tokens de Invitación

```php
// Generar token de invitación
$token = InvitationToken::create([
    'token' => Str::random(32),
    'type' => 'coach',                  // Tipo de usuario a crear
    'issued_by_user_id' => auth()->id(),
    'target_league_id' => $leagueId,    // Liga a la que se asigna
    'email' => $email,
    'expires_at' => now()->addDays(7),
]);

// Enviar email con token
Mail::to($email)->send(new InvitationMail($token));
```

---

## 🔌 API REST

### Estructura de Respuestas

Todas las respuestas API siguen un formato estándar definido en `BaseController`:

```php
// Respuesta exitosa
{
    "success": true,
    "message": "Liga creada exitosamente",
    "data": {
        "id": 1,
        "name": "Liga de Fútbol",
        ...
    }
}

// Respuesta de error
{
    "success": false,
    "message": "Error de validación",
    "errors": {
        "name": ["El nombre es requerido"]
    }
}

// Respuesta paginada
{
    "success": true,
    "message": "Ligas obtenidas",
    "data": [...],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### Endpoints Principales

#### Autenticación

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/auth/login` | Iniciar sesión |
| POST | `/api/auth/logout` | Cerrar sesión |
| GET | `/api/auth/me` | Usuario actual |
| POST | `/api/auth/refresh` | Refrescar token |

#### Ligas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/leagues` | Listar ligas |
| POST | `/api/leagues` | Crear liga |
| GET | `/api/leagues/{id}` | Ver liga |
| PUT | `/api/leagues/{id}` | Actualizar liga |
| DELETE | `/api/leagues/{id}` | Eliminar liga |

#### Temporadas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/seasons` | Listar temporadas |
| POST | `/api/seasons` | Crear temporada |
| GET | `/api/seasons/{id}` | Ver temporada |
| PUT | `/api/seasons/{id}` | Actualizar temporada |
| POST | `/api/seasons/{id}/activate` | Activar temporada |

#### Fixtures

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/seasons/{id}/fixture/preview` | Preview del fixture |
| POST | `/api/seasons/{id}/fixture/generate` | Generar fixture |
| DELETE | `/api/seasons/{id}/fixture/clear` | Eliminar fixture |

### Ejemplo de Controlador API

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\League;

class LeagueController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $query = League::query()->with(['sport', 'admin']);

        // Filtros
        if ($request->has('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $leagues = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($leagues, 'Ligas obtenidas');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'sport_id' => 'required|exists:sports,id',
            'registration_fee' => 'nullable|numeric|min:0',
        ]);

        $validated['admin_id'] = auth()->user()->userable_id;
        
        $league = League::create($validated);

        return $this->successResponse($league, 'Liga creada', 201);
    }
}
```

---

## ⚡ Componentes Livewire

### Estructura de un Componente

```php
<?php

namespace App\Livewire\Leagues;

use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Propiedades públicas (estado del componente)
    public $search = '';
    public $statusFilter = '';

    // Query string (sincroniza con URL)
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    // Resetear paginación al buscar
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Método de eliminación
    public function deleteLeague($id)
    {
        $league = League::findOrFail($id);
        
        if (auth()->user()->user_type !== 'admin') {
            session()->flash('error', 'No autorizado');
            return;
        }

        $league->delete();
        session()->flash('success', 'Liga eliminada');
    }

    // Renderizado
    public function render()
    {
        $leagues = League::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.leagues.index', [
            'leagues' => $leagues,
        ]);
    }
}
```

### Vista Blade del Componente

```blade
{{-- resources/views/livewire/leagues/index.blade.php --}}
<div>
    {{-- Barra de búsqueda --}}
    <div class="mb-4">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            placeholder="Buscar ligas..."
            class="w-full px-4 py-2 border rounded-lg"
        >
    </div>

    {{-- Tabla de ligas --}}
    <table class="w-full">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Deporte</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leagues as $league)
            <tr>
                <td>{{ $league->name }}</td>
                <td>{{ $league->sport->name }}</td>
                <td>{{ $league->status }}</td>
                <td>
                    <a href="{{ route('leagues.edit', $league) }}">Editar</a>
                    <button wire:click="deleteLeague({{ $league->id }})">
                        Eliminar
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Paginación --}}
    {{ $leagues->links() }}
</div>
```

### Componentes Principales

| Componente | Ubicación | Descripción |
|------------|-----------|-------------|
| `AdminDashboard` | `Livewire/AdminDashboard.php` | Panel principal admin |
| `Leagues/Index` | `Livewire/Leagues/Index.php` | Lista de ligas |
| `Leagues/Create` | `Livewire/Leagues/Create.php` | Crear liga |
| `Seasons/Index` | `Livewire/Seasons/Index.php` | Lista de temporadas |
| `Fixtures/Generate` | `Livewire/Fixtures/Generate.php` | Generar fixture |
| `Matches/Live` | `Livewire/Matches/Live.php` | Partido en vivo |
| `Financial/Dashboard` | `Livewire/Financial/Dashboard.php` | Dashboard financiero |
| `Players/Import` | `Livewire/Players/Import.php` | Importar jugadores |

---

## 🔧 Servicios

### RoundRobinService

Servicio para generación de fixtures usando algoritmo Round Robin:

```php
<?php

namespace App\Services;

use App\Models\Season;

class RoundRobinService
{
    /**
     * Generar fixture completo para una temporada
     */
    public function generateFixture(Season $season): array
    {
        $teams = $season->teams()->get();
        $teamsCount = $teams->count();

        // Si es impar, agregar BYE
        $hasBye = $teamsCount % 2 !== 0;
        if ($hasBye) {
            $teams->push((object) ['id' => null, 'name' => 'BYE']);
            $teamsCount++;
        }

        $rounds = $this->generateRounds($teams->toArray(), $season);

        return [
            'total_rounds' => count($rounds),
            'total_matches' => $this->countMatches($rounds),
            'has_bye' => $hasBye,
            'rounds' => $rounds
        ];
    }

    /**
     * Algoritmo Round Robin para generar rondas
     */
    private function generateRounds(array $teams, Season $season): array
    {
        $teamsCount = count($teams);
        $totalRounds = $teamsCount - 1;
        $rounds = [];

        for ($roundNumber = 1; $roundNumber <= $totalRounds; $roundNumber++) {
            $roundMatches = [];

            for ($i = 0; $i < $teamsCount / 2; $i++) {
                $team1 = $teams[$i];
                $team2 = $teams[$teamsCount - 1 - $i];

                if ($team1['id'] !== null && $team2['id'] !== null) {
                    $roundMatches[] = [
                        'home_team_id' => $team1['id'],
                        'away_team_id' => $team2['id'],
                    ];
                }
            }

            $rounds[] = [
                'round_number' => $roundNumber,
                'matches' => $roundMatches,
            ];

            $this->rotateTeams($teams);
        }

        return $rounds;
    }

    /**
     * Rotar equipos (mantener primero fijo)
     */
    private function rotateTeams(array &$teams): void
    {
        $last = array_pop($teams);
        array_splice($teams, 1, 0, [$last]);
    }
}
```

### FinancialDashboardService

```php
<?php

namespace App\Services;

use App\Models\League;

class FinancialDashboardService
{
    public function getSummary(League $league, string $period = 'month'): array
    {
        $dateFrom = $this->getDateFrom($period);

        $totalIncome = $league->incomes()
            ->where('created_at', '>=', $dateFrom)
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalExpense = $league->expenses()
            ->where('created_at', '>=', $dateFrom)
            ->where('status', 'confirmed')
            ->sum('amount');

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => $totalIncome - $totalExpense,
            'period' => $period,
        ];
    }
}
```

### StandingsService

```php
<?php

namespace App\Services;

use App\Models\Season;

class StandingsService
{
    public function calculate(Season $season): array
    {
        $standings = [];

        foreach ($season->teams as $team) {
            $standings[$team->id] = [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'points' => 0,
            ];
        }

        // Calcular estadísticas de cada partido
        foreach ($season->gameMatches()->where('status', 'finished')->get() as $match) {
            $this->updateStandings($standings, $match);
        }

        // Ordenar por puntos, diferencia de goles, goles a favor
        return collect($standings)
            ->sortByDesc(fn($s) => [$s['points'], $s['goal_difference'], $s['goals_for']])
            ->values()
            ->toArray();
    }
}
```

---

## ⏰ Jobs y Colas

### Jobs Disponibles

| Job | Descripción | Trigger |
|-----|-------------|---------|
| `GenerateMatchFeesJob` | Genera ingresos por partido | Al finalizar partido |
| `GenerateRefereePaymentsJob` | Genera egresos de árbitros | Al finalizar partido |
| `MarkOverdueIncomesJob` | Marca ingresos vencidos | Scheduler diario |

### Ejemplo de Job

```php
<?php

namespace App\Jobs;

use App\Models\GameMatch;
use App\Models\Income;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMatchFeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private GameMatch $match
    ) {}

    public function handle(): void
    {
        $season = $this->match->round->fixture->season;
        $league = $season->league;
        $matchFee = $league->match_fee;

        // Ingreso equipo local
        Income::create([
            'league_id' => $league->id,
            'season_id' => $season->id,
            'game_match_id' => $this->match->id,
            'team_id' => $this->match->home_team_id,
            'type' => 'match_fee',
            'amount' => $matchFee,
            'description' => "Cuota de partido - {$this->match->homeTeam->name}",
            'status' => 'pending',
        ]);

        // Ingreso equipo visitante
        Income::create([
            'league_id' => $league->id,
            'season_id' => $season->id,
            'game_match_id' => $this->match->id,
            'team_id' => $this->match->away_team_id,
            'type' => 'match_fee',
            'amount' => $matchFee,
            'description' => "Cuota de partido - {$this->match->awayTeam->name}",
            'status' => 'pending',
        ]);
    }
}
```

### Configuración de Colas

```bash
# .env
QUEUE_CONNECTION=database

# Ejecutar worker
php artisan queue:work

# Ejecutar con reintentos
php artisan queue:work --tries=3
```

---

## 🛤️ Sistema de Rutas

### Rutas Web (web.php)

```php
<?php

// Rutas públicas (sin autenticación)
Route::get('/', PublicHome::class)->name('public.home');
Route::get('/leagues', PublicLeagues::class)->name('public.leagues');
Route::get('/league/{slug}', LeagueHome::class)->name('public.league');
Route::get('/league/{slug}/fixtures', LeagueFixtures::class);
Route::get('/league/{slug}/standings', LeagueStandings::class);

// Ruta de invitaciones
Route::get('/invite/{token}', InvitationsAccept::class)->name('invite.accept');

// Rutas autenticadas
Route::middleware(['auth'])->group(function () {
    
    // Dashboard admin (admin y league_manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/leagues', LeaguesIndex::class)->name('leagues.index');
        Route::get('/admin/seasons', SeasonsIndex::class)->name('seasons.index');
    });

    // Solo admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/leagues/create', LeaguesCreate::class);
    });

    // Área de árbitros
    Route::middleware(['role:referee'])->prefix('referee')->name('referee.')->group(function () {
        Route::get('/dashboard', RefereeDashboard::class)->name('dashboard');
        Route::get('/my-payments', RefereePayments::class)->name('my-payments');
    });

    // Área de entrenadores
    Route::middleware(['role:coach'])->prefix('coach')->name('coach.')->group(function () {
        Route::get('/dashboard', CoachDashboard::class)->name('dashboard');
        Route::get('/teams', TeamsIndex::class)->name('teams.index');
        Route::get('/players', PlayersIndex::class)->name('players.index');
    });
});
```

### Rutas API (api.php)

```php
<?php

// Rutas públicas
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/sports', [SportController::class, 'index']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Recursos CRUD
    Route::apiResource('leagues', LeagueController::class);
    Route::apiResource('seasons', SeasonController::class);
    Route::apiResource('teams', TeamController::class);

    // Fixtures
    Route::prefix('seasons/{season}/fixture')->group(function () {
        Route::get('/preview', [FixtureController::class, 'preview']);
        Route::post('/generate', [FixtureController::class, 'generate']);
        Route::delete('/clear', [FixtureController::class, 'clear']);
    });

    // Invitaciones
    Route::prefix('invitations')->group(function () {
        Route::get('/', [InvitationController::class, 'index']);
        Route::post('/coach', [InvitationController::class, 'generateCoachToken']);
        Route::post('/referee', [InvitationController::class, 'generateRefereeToken']);
    });
});
```

---

## 📝 Guías de Desarrollo

### Crear un Nuevo Modelo

```bash
# Crear modelo con migración, factory y seeder
php artisan make:model NuevoModelo -mfs

# Estructura generada:
# - app/Models/NuevoModelo.php
# - database/migrations/xxxx_create_nuevo_modelos_table.php
# - database/factories/NuevoModeloFactory.php
# - database/seeders/NuevoModeloSeeder.php
```

### Crear un Componente Livewire

```bash
# Crear componente
php artisan make:livewire MiModulo/MiComponente

# Estructura generada:
# - app/Livewire/MiModulo/MiComponente.php
# - resources/views/livewire/mi-modulo/mi-componente.blade.php
```

### Crear un Controlador API

```bash
# Crear controlador API
php artisan make:controller Api/MiController --api

# Registrar rutas en routes/api.php
Route::apiResource('mi-recurso', MiController::class);
```

### Crear un Servicio

```php
<?php
// app/Services/MiServicio.php

namespace App\Services;

class MiServicio
{
    public function ejecutar(): mixed
    {
        // Lógica de negocio
    }
}

// Uso en controlador o componente:
$servicio = new MiServicio();
$resultado = $servicio->ejecutar();

// O con inyección de dependencias:
public function __construct(
    private MiServicio $miServicio
) {}
```

### Crear un Job

```bash
# Crear job
php artisan make:job MiJob

# Despachar job
MiJob::dispatch($parametros);

# Despachar con delay
MiJob::dispatch($parametros)->delay(now()->addMinutes(5));
```

### Convenciones de Código

```php
// Nombres de clases: PascalCase
class LeagueController {}

// Nombres de métodos: camelCase
public function getActiveSeasons() {}

// Nombres de variables: camelCase
$totalIncome = 0;

// Constantes: UPPER_SNAKE_CASE
const MAX_TEAMS_PER_LEAGUE = 20;

// Nombres de tablas: snake_case plural
// game_matches, invitation_tokens

// Nombres de columnas: snake_case
// home_team_id, created_at

// Nombres de rutas: kebab-case
// /admin/league-managers
```

---

## 🧪 Testing

### Configuración

```xml
<!-- phpunit.xml -->
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=LeagueTest

# Con coverage
php artisan test --coverage
```

### Ejemplo de Test

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\League;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_league(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        $user = User::factory()->create([
            'user_type' => 'admin',
            'userable_id' => $admin->id,
            'userable_type' => Admin::class,
        ]);

        // Act
        $response = $this->actingAs($user)->post('/api/leagues', [
            'name' => 'Liga de Prueba',
            'sport_id' => 1,
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('leagues', [
            'name' => 'Liga de Prueba',
        ]);
    }

    public function test_coach_cannot_create_league(): void
    {
        $coach = Coach::factory()->create();
        $user = User::factory()->create([
            'user_type' => 'coach',
            'userable_id' => $coach->id,
            'userable_type' => Coach::class,
        ]);

        $response = $this->actingAs($user)->post('/api/leagues', [
            'name' => 'Liga de Prueba',
            'sport_id' => 1,
        ]);

        $response->assertStatus(403);
    }
}
```

---

## 🚀 Despliegue

### Requisitos de Producción

```
- PHP 8.2+ con OPcache
- MySQL 8.0+
- Nginx o Apache
- SSL/TLS habilitado
- Supervisor para colas
```

### Configuración de Producción

```env
# .env.production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://flowfast.me

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=flowfast_prod
DB_USERNAME=flowfast_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Comandos de Despliegue

```bash
# Instalar dependencias (sin dev)
composer install --no-dev --optimize-autoloader

# Compilar assets
npm run build

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrar base de datos
php artisan migrate --force

# Reiniciar colas
php artisan queue:restart
```

### Configuración de Supervisor

```ini
[program:flowfast-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/flowfast/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/flowfast/worker.log
stopwaitsecs=3600
```

---

## 🔧 Troubleshooting

### Errores Comunes

#### Error: "Class not found"

```bash
# Regenerar autoload
composer dump-autoload

# Limpiar cache
php artisan cache:clear
php artisan config:clear
```

#### Error: "SQLSTATE[HY000]"

```bash
# Verificar conexión de BD
php artisan db:show

# Verificar configuración
php artisan config:show database
```

#### Error: "Token mismatch"

```bash
# Limpiar sesiones
php artisan session:clear

# Verificar CSRF
# Asegurar que @csrf está en los formularios
```

#### Livewire no actualiza

```blade
{{-- Verificar wire:model --}}
wire:model.live="propiedad"  {{-- Tiempo real --}}
wire:model="propiedad"       {{-- Al enviar --}}
wire:model.blur="propiedad"  {{-- Al perder foco --}}
```

### Comandos de Debug

```bash
# Ver logs en tiempo real
php artisan pail

# Ver rutas registradas
php artisan route:list

# Ver migraciones pendientes
php artisan migrate:status

# Tinker (REPL)
php artisan tinker
>>> User::find(1);
>>> League::with('seasons')->first();
```

### Logs

```php
// Escribir en log
Log::info('Mensaje informativo', ['data' => $data]);
Log::error('Error crítico', ['exception' => $e->getMessage()]);

// Ubicación de logs
storage/logs/laravel.log
```

---

## 📚 Referencias

### Documentación Oficial

- [Laravel 12](https://laravel.com/docs)
- [Livewire 3](https://livewire.laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev/start-here)

### Archivos de Documentación del Proyecto

| Archivo | Descripción |
|---------|-------------|
| `README.md` | Documentación general |
| `README-BACKEND.md` | Desarrollo backend |
| `README-DATABASE.md` | Diseño de base de datos |
| `README-AUTH.md` | Sistema de autenticación |
| `README-FRONTEND.md` | Desarrollo frontend |
| `README-FINANCIAL-*.md` | Sistema financiero |
| `PROGRESO-*.md` | Progreso de desarrollo |

---

## 📞 Soporte

Para dudas técnicas o reporte de bugs:

- **Repositorio:** github.com/JOSE-OMAR-FLORES/flowfast
- **Email:** soporte@flowfast.me
- **Web:** https://flowfast.me

---

*Manual del Programador - FlowFast SaaS*  
*Versión 1.0 - Noviembre 2025*
