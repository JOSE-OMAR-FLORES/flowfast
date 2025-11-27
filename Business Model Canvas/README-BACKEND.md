# ⚙️ FlowFast SaaS - Desarrollo del Backend

## 📋 Índice

1. [Configuración Inicial de Laravel](#-configuración-inicial-de-laravel)
2. [Estructura de Modelos](#-estructura-de-modelos)
3. [APIs RESTful](#-apis-restful)
4. [Servicios y Lógica de Negocio](#-servicios-y-lógica-de-negocio)
5. [Jobs y Colas](#-jobs-y-colas)
6. [Algoritmos Específicos](#-algoritmos-específicos)

---

## 🚀 Configuración Inicial de Laravel

### **1. Instalación y Setup del Proyecto**

```bash
# Crear proyecto Laravel 11
composer create-project laravel/laravel flowfast-saas

cd flowfast-saas

# Configurar variables de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=flowfast_saas
# DB_USERNAME=root
# DB_PASSWORD=

# Instalar dependencias adicionales
composer require tymon/jwt-auth
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require laravel/cashier
composer require pusher/pusher-php-server
```

### **2. Configuración de .env Completa**

```bash
# .env
APP_NAME="FlowFast SaaS"
APP_ENV=local
APP_KEY=base64:GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flowfast_saas
DB_USERNAME=root
DB_PASSWORD=

# JWT
JWT_SECRET=GENERATED_JWT_SECRET
JWT_TTL=1440
JWT_REFRESH_TTL=20160

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@flowfast-saas.com"
MAIL_FROM_NAME="${APP_NAME}"

# Stripe para pagos
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret

# Pusher para notificaciones en tiempo real
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Cache y sesiones
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### **3. Configuración de Servicios**

```php
<?php
// config/app.php - Agregar providers
'providers' => [
    // ... otros providers
    App\Providers\AuthServiceProvider::class,
    App\Providers\FinancialServiceProvider::class,
    App\Providers\LeagueServiceProvider::class,
],

// config/services.php
return [
    'stripe' => [
        'model' => App\Models\Admin::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'pusher' => [
        'app_id' => env('PUSHER_APP_ID'),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'cluster' => env('PUSHER_APP_CLUSTER'),
    ],
];
```

---

## 🏗️ Estructura de Modelos

### **1. Modelo Base Abstracto**

```php
<?php
// app/Models/BaseModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    // Scope para filtrar por administrador
    public function scopeForAdmin($query, int $adminId)
    {
        return $query->whereHas('league', function ($q) use ($adminId) {
            $q->where('admin_id', $adminId);
        });
    }

    // Scope para filtrar por liga
    public function scopeForLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    // Formatear fechas automáticamente
    public function getCreatedAtAttribute($value)
    {
        return $this->asDateTime($value)->format('Y-m-d H:i:s');
    }
}
```

### **2. Modelos Principales**

```php
<?php
// app/Models/League.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class League extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'sport_id',
        'admin_id',
        'manager_id',
        'description',
        'registration_fee',
        'match_fee_per_team',
        'penalty_fee',
        'referee_payment',
        'status',
    ];

    protected $casts = [
        'registration_fee' => 'decimal:2',
        'match_fee_per_team' => 'decimal:2',
        'penalty_fee' => 'decimal:2',
        'referee_payment' => 'decimal:2',
    ];

    // Relaciones
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(LeagueManager::class, 'manager_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function publicPage(): HasOne
    {
        return $this->hasOne(LeaguePublicPage::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Métodos de negocio
    public function getCurrentSeason()
    {
        return $this->seasons()
                   ->where('status', 'active')
                   ->latest()
                   ->first();
    }

    public function getTotalIncome(string $period = 'all'): float
    {
        $query = $this->incomes()->where('payment_status', 'confirmed');
        
        if ($period !== 'all') {
            $query->where('created_at', '>=', now()->sub($period));
        }
        
        return $query->sum('amount');
    }

    public function getTotalExpenses(string $period = 'all'): float
    {
        $query = $this->expenses()->where('payment_status', 'confirmed');
        
        if ($period !== 'all') {
            $query->where('created_at', '>=', now()->sub($period));
        }
        
        return $query->sum('amount');
    }

    public function getNetProfit(string $period = 'all'): float
    {
        return $this->getTotalIncome($period) - $this->getTotalExpenses($period);
    }

    // Generar slug automáticamente
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }

    // URL pública de la liga
    public function getPublicUrlAttribute()
    {
        return url("/liga/{$this->slug}");
    }
}
```

```php
<?php
// app/Models/Season.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends BaseModel
{
    protected $fillable = [
        'league_id',
        'name',
        'format',
        'round_robin_type',
        'start_date',
        'end_date',
        'game_days',
        'daily_matches',
        'match_times',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'game_days' => 'array',
        'match_times' => 'array',
    ];

    // Relaciones
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    // Métodos de negocio
    public function generateRounds(): void
    {
        $teams = $this->teams()->where('registration_paid', true)->get();
        
        if ($teams->count() < 2) {
            throw new \Exception('Se necesitan al menos 2 equipos registrados');
        }

        $roundRobinGenerator = new \App\Services\RoundRobinGenerator($teams, $this);
        $roundRobinGenerator->generate();
    }

    public function getStandings()
    {
        return Standing::where('season_id', $this->id)
                      ->orderBy('points', 'desc')
                      ->orderBy('goal_difference', 'desc')
                      ->orderBy('goals_for', 'desc')
                      ->get();
    }

    public function updateStandings(): void
    {
        $standingsService = new \App\Services\StandingsService($this);
        $standingsService->updateAll();
    }

    // Calcular fecha de fin automáticamente
    public function calculateEndDate(): \Carbon\Carbon
    {
        $totalRounds = $this->calculateTotalRounds();
        $gamesPerWeek = count($this->game_days) * $this->daily_matches;
        $weeksNeeded = ceil($totalRounds / $gamesPerWeek);
        
        return $this->start_date->addWeeks($weeksNeeded);
    }

    private function calculateTotalRounds(): int
    {
        $teamCount = $this->teams()->count();
        $baseRounds = $teamCount - 1;
        
        return $this->round_robin_type === 'double' ? $baseRounds * 2 : $baseRounds;
    }
}
```

```php
<?php
// app/Models/Match.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Match extends BaseModel
{
    protected $fillable = [
        'round_id',
        'home_team_id',
        'away_team_id',
        'match_date',
        'venue',
        'status',
        'home_score',
        'away_score',
    ];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    // Relaciones
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function officials(): HasMany
    {
        return $this->hasMany(MatchOfficial::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(MatchAppeal::class);
    }

    // Métodos de negocio
    public function start(): void
    {
        if ($this->status !== 'scheduled') {
            throw new \Exception('El partido ya fue iniciado o finalizado');
        }

        $this->update(['status' => 'in_progress']);
        
        // Enviar notificación en tiempo real
        broadcast(new \App\Events\MatchStarted($this));
    }

    public function finish(int $homeScore, int $awayScore): void
    {
        if ($this->status !== 'in_progress') {
            throw new \Exception('El partido no está en progreso');
        }

        $this->update([
            'status' => 'completed',
            'home_score' => $homeScore,
            'away_score' => $awayScore,
        ]);

        // Generar ingresos y egresos automáticamente
        $this->generateFinancialTransactions();

        // Actualizar tabla de posiciones
        $this->round->season->updateStandings();

        // Enviar notificaciones
        broadcast(new \App\Events\MatchFinished($this));
    }

    private function generateFinancialTransactions(): void
    {
        $league = $this->round->season->league;
        $matchFee = $league->match_fee_per_team;
        $refereePayment = $league->referee_payment;

        // Generar 2 ingresos (equipo local + visitante)
        foreach ([$this->homeTeam, $this->awayTeam] as $team) {
            Income::create([
                'income_type_id' => IncomeType::where('slug', 'match_fee')->first()->id,
                'league_id' => $league->id,
                'season_id' => $this->round->season_id,
                'team_id' => $team->id,
                'match_id' => $this->id,
                'amount' => $matchFee,
                'payment_method' => 'cash', // Default, puede cambiarse después
                'payment_status' => 'pending',
                'description' => "Pago por partido - {$team->name}",
                'created_by' => auth()->id(),
            ]);
        }

        // Generar 1 egreso (pago a árbitros)
        if ($refereePayment > 0) {
            $mainReferee = $this->officials()->where('role', 'main')->first();
            
            if ($mainReferee) {
                Expense::create([
                    'expense_type_id' => ExpenseType::where('slug', 'referee_payment')->first()->id,
                    'league_id' => $league->id,
                    'match_id' => $this->id,
                    'referee_id' => $mainReferee->referee_id,
                    'amount' => $refereePayment,
                    'payment_method' => 'cash',
                    'payment_status' => 'pending',
                    'description' => "Pago por arbitraje - Partido #{$this->id}",
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    public function getResult(): string
    {
        if ($this->status !== 'completed') {
            return 'Pendiente';
        }

        return "{$this->home_score} - {$this->away_score}";
    }

    public function getWinner(): ?Team
    {
        if ($this->status !== 'completed' || $this->home_score === $this->away_score) {
            return null;
        }

        return $this->home_score > $this->away_score ? $this->homeTeam : $this->awayTeam;
    }
}
```

---

## 🔌 APIs RESTful

### **1. Controlador Base**

```php
<?php
// app/Http/Controllers/BaseController.php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function paginatedResponse($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }
}
```

### **2. Controlador de Ligas**

```php
<?php
// app/Http/Controllers/Api/LeagueController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\League;
use App\Http\Requests\League\StoreLeagueRequest;
use App\Http\Requests\League\UpdateLeagueRequest;
use App\Services\LeagueService;
use Illuminate\Http\Request;

class LeagueController extends BaseController
{
    public function __construct(
        private LeagueService $leagueService
    ) {
        $this->middleware(['jwt.auth']);
        $this->middleware(['permission:league.create'])->only(['store']);
        $this->middleware(['permission:league.read'])->only(['index', 'show']);
        $this->middleware(['permission:league.update'])->only(['update']);
        $this->middleware(['permission:league.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $leagues = $this->leagueService->getLeaguesForUser(
            $user,
            $request->get('per_page', 15),
            $request->get('search'),
            $request->get('status'),
            $request->get('sport_id')
        );

        return $this->paginatedResponse($leagues, 'Ligas obtenidas exitosamente');
    }

    public function store(StoreLeagueRequest $request)
    {
        try {
            $league = $this->leagueService->createLeague($request->validated());
            
            return $this->successResponse(
                $league->load(['sport', 'admin', 'manager']),
                'Liga creada exitosamente',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear la liga: ' . $e->getMessage());
        }
    }

    public function show(League $league)
    {
        $this->authorize('view', $league);
        
        $leagueData = $league->load([
            'sport',
            'admin',
            'manager',
            'seasons.teams',
            'publicPage'
        ]);

        // Agregar estadísticas adicionales
        $leagueData->total_income = $league->getTotalIncome();
        $leagueData->total_expenses = $league->getTotalExpenses();
        $leagueData->net_profit = $league->getNetProfit();
        $leagueData->current_season = $league->getCurrentSeason();

        return $this->successResponse($leagueData, 'Liga obtenida exitosamente');
    }

    public function update(UpdateLeagueRequest $request, League $league)
    {
        $this->authorize('update', $league);
        
        try {
            $updatedLeague = $this->leagueService->updateLeague($league, $request->validated());
            
            return $this->successResponse(
                $updatedLeague->load(['sport', 'admin', 'manager']),
                'Liga actualizada exitosamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar la liga: ' . $e->getMessage());
        }
    }

    public function destroy(League $league)
    {
        $this->authorize('delete', $league);
        
        try {
            $this->leagueService->deleteLeague($league);
            
            return $this->successResponse(null, 'Liga eliminada exitosamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar la liga: ' . $e->getMessage());
        }
    }

    // Métodos adicionales específicos
    public function getFinancialSummary(League $league, Request $request)
    {
        $this->authorize('view', $league);
        
        $period = $request->get('period', 'month'); // week, month, quarter, year
        $summary = $this->leagueService->getFinancialSummary($league, $period);
        
        return $this->successResponse($summary, 'Resumen financiero obtenido');
    }

    public function getPublicData(League $league)
    {
        // Esta ruta es pública, no requiere autenticación
        $publicData = $this->leagueService->getPublicLeagueData($league);
        
        return $this->successResponse($publicData, 'Datos públicos de la liga');
    }
}
```

### **3. Request Validation**

```php
<?php
// app/Http/Requests/League/StoreLeagueRequest.php
namespace App\Http\Requests\League;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('league.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191|unique:leagues,name',
            'sport_id' => 'required|exists:sports,id',
            'manager_id' => 'nullable|exists:league_managers,id',
            'description' => 'nullable|string',
            'registration_fee' => 'required|numeric|min:0',
            'match_fee_per_team' => 'required|numeric|min:0',
            'penalty_fee' => 'required|numeric|min:0',
            'referee_payment' => 'required|numeric|min:0',
            'status' => 'in:draft,active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la liga es obligatorio',
            'name.unique' => 'Ya existe una liga con este nombre',
            'sport_id.required' => 'Debes seleccionar un deporte',
            'sport_id.exists' => 'El deporte seleccionado no existe',
            'registration_fee.required' => 'La cuota de inscripción es obligatoria',
            'registration_fee.numeric' => 'La cuota de inscripción debe ser un número',
            'registration_fee.min' => 'La cuota de inscripción no puede ser negativa',
        ];
    }

    protected function prepareForValidation()
    {
        // Agregar admin_id automáticamente
        $this->merge([
            'admin_id' => auth()->user()->userable_id,
        ]);
    }
}
```

### **4. Políticas de Autorización**

```php
<?php
// app/Policies/LeaguePolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\League;

class LeaguePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('league.read');
    }

    public function view(User $user, League $league): bool
    {
        if (!$user->hasPermission('league.read')) {
            return false;
        }

        return $user->canAccessLeague($league->id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('league.create');
    }

    public function update(User $user, League $league): bool
    {
        if (!$user->hasPermission('league.update')) {
            return false;
        }

        return $user->canAccessLeague($league->id);
    }

    public function delete(User $user, League $league): bool
    {
        if (!$user->hasPermission('league.delete')) {
            return false;
        }

        // Solo admin puede eliminar ligas
        return $user->user_type === 'admin' && $league->admin_id === $user->userable_id;
    }
}
```

---

## 🔧 Servicios y Lógica de Negocio

### **1. LeagueService**

```php
<?php
// app/Services/LeagueService.php
namespace App\Services;

use App\Models\League;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class LeagueService
{
    public function getLeaguesForUser(
        User $user,
        int $perPage = 15,
        ?string $search = null,
        ?string $status = null,
        ?int $sportId = null
    ): LengthAwarePaginator {
        $query = League::query();

        // Filtrar según tipo de usuario
        switch ($user->user_type) {
            case 'admin':
                $query->where('admin_id', $user->userable_id);
                break;
                
            case 'league_manager':
                $assignedLeagues = auth()->payload()->get('league_ids', []);
                $query->whereIn('id', $assignedLeagues);
                break;
                
            default:
                // Para otros tipos, obtener liga del contexto
                $leagueId = auth()->payload()->get('league_id');
                $query->where('id', $leagueId);
        }

        // Aplicar filtros
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($sportId) {
            $query->where('sport_id', $sportId);
        }

        return $query->with(['sport', 'admin', 'manager'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    public function createLeague(array $data): League
    {
        $user = auth()->user();
        
        if ($user->user_type !== 'admin') {
            throw new \Exception('Solo administradores pueden crear ligas');
        }

        // Verificar límites de suscripción
        $this->checkSubscriptionLimits($user->userable);

        $league = League::create($data);
        
        // Crear página pública por defecto
        $league->publicPage()->create([
            'is_public' => true,
            'seo_title' => $league->name,
            'seo_description' => $league->description,
        ]);

        return $league;
    }

    public function updateLeague(League $league, array $data): League
    {
        $league->update($data);
        return $league->fresh();
    }

    public function deleteLeague(League $league): void
    {
        // Verificar que no tenga temporadas activas
        if ($league->seasons()->where('status', 'active')->exists()) {
            throw new \Exception('No se puede eliminar una liga con temporadas activas');
        }

        $league->delete();
    }

    public function getFinancialSummary(League $league, string $period): array
    {
        $startDate = $this->getPeriodStartDate($period);
        
        $totalIncome = $league->incomes()
            ->where('payment_status', 'confirmed')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $totalExpenses = $league->expenses()
            ->where('payment_status', 'confirmed')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $incomeByType = $league->incomes()
            ->where('payment_status', 'confirmed')
            ->where('created_at', '>=', $startDate)
            ->with('incomeType')
            ->get()
            ->groupBy('income_type_id')
            ->map(function ($items) {
                return [
                    'type' => $items->first()->incomeType->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            });

        $expensesByType = $league->expenses()
            ->where('payment_status', 'confirmed')
            ->where('created_at', '>=', $startDate)
            ->with('expenseType')
            ->get()
            ->groupBy('expense_type_id')
            ->map(function ($items) {
                return [
                    'type' => $items->first()->expenseType->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            });

        return [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalIncome - $totalExpenses,
            'income_by_type' => $incomeByType->values(),
            'expenses_by_type' => $expensesByType->values(),
        ];
    }

    public function getPublicLeagueData(League $league): array
    {
        if (!$league->publicPage || !$league->publicPage->is_public) {
            throw new \Exception('Esta liga no tiene página pública habilitada');
        }

        $currentSeason = $league->getCurrentSeason();
        
        if (!$currentSeason) {
            return [
                'league' => $league->only(['name', 'sport.name']),
                'message' => 'No hay temporada activa actualmente',
            ];
        }

        return [
            'league' => $league->load('sport'),
            'season' => $currentSeason->load('teams'),
            'standings' => $currentSeason->getStandings(),
            'recent_matches' => $this->getRecentMatches($currentSeason, 10),
            'upcoming_matches' => $this->getUpcomingMatches($currentSeason, 10),
            'public_page' => $league->publicPage,
        ];
    }

    private function checkSubscriptionLimits(\App\Models\Admin $admin): void
    {
        $subscription = $admin->subscription;
        
        if (!$subscription || $subscription->status !== 'active') {
            throw new \Exception('Suscripción inactiva');
        }

        $currentLeagues = $admin->leagues()->count();
        $maxLeagues = $subscription->subscriptionPlan->max_leagues;

        if ($maxLeagues && $currentLeagues >= $maxLeagues) {
            throw new \Exception("Has alcanzado el límite de {$maxLeagues} ligas para tu plan");
        }
    }

    private function getPeriodStartDate(string $period): \Carbon\Carbon
    {
        return match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    private function getRecentMatches($season, int $limit)
    {
        return \App\Models\Match::whereHas('round', function ($q) use ($season) {
                $q->where('season_id', $season->id);
            })
            ->where('status', 'completed')
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getUpcomingMatches($season, int $limit)
    {
        return \App\Models\Match::whereHas('round', function ($q) use ($season) {
                $q->where('season_id', $season->id);
            })
            ->where('status', 'scheduled')
            ->where('match_date', '>', now())
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date', 'asc')
            ->limit($limit)
            ->get();
    }
}
```

---

## ⚙️ Jobs y Colas

### **1. Job para Generar Jornadas**

```php
<?php
// app/Jobs/GenerateSeasonRounds.php
namespace App\Jobs;

use App\Models\Season;
use App\Services\RoundRobinGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSeasonRounds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Season $season
    ) {}

    public function handle(): void
    {
        $teams = $this->season->teams()->where('registration_paid', true)->get();
        
        if ($teams->count() < 2) {
            throw new \Exception('Se necesitan al menos 2 equipos registrados y con pago confirmado');
        }

        $generator = new RoundRobinGenerator($teams, $this->season);
        $generator->generate();

        // Notificar que las jornadas fueron generadas
        \App\Events\SeasonRoundsGenerated::dispatch($this->season);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Error generando jornadas para temporada {$this->season->id}: " . $exception->getMessage());
        
        // Notificar error al administrador
        \App\Events\SeasonRoundsGenerationFailed::dispatch($this->season, $exception->getMessage());
    }
}
```

### **2. Job para Envío de Notificaciones**

```php
<?php
// app/Jobs/SendPaymentNotifications.php
namespace App\Jobs;

use App\Models\AdminSubscription;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Notificaciones 7 días antes
        $subscriptions7Days = AdminSubscription::where('status', 'active')
            ->whereBetween('current_period_end', [
                now()->addDays(6)->startOfDay(),
                now()->addDays(7)->endOfDay()
            ])
            ->whereDoesntHave('paymentNotifications', function ($q) {
                $q->where('notification_type', '7_days')
                  ->whereNotNull('sent_at');
            })
            ->with('admin.user')
            ->get();

        foreach ($subscriptions7Days as $subscription) {
            $subscription->admin->user->notify(
                new PaymentReminderNotification($subscription, '7_days')
            );
            
            $subscription->paymentNotifications()->create([
                'notification_type' => '7_days',
                'sent_at' => now(),
            ]);
        }

        // Notificaciones 3 días antes
        $subscriptions3Days = AdminSubscription::where('status', 'active')
            ->whereBetween('current_period_end', [
                now()->addDays(2)->startOfDay(),
                now()->addDays(3)->endOfDay()
            ])
            ->whereDoesntHave('paymentNotifications', function ($q) {
                $q->where('notification_type', '3_days')
                  ->whereNotNull('sent_at');
            })
            ->with('admin.user')
            ->get();

        foreach ($subscriptions3Days as $subscription) {
            $subscription->admin->user->notify(
                new PaymentReminderNotification($subscription, '3_days')
            );
            
            $subscription->paymentNotifications()->create([
                'notification_type' => '3_days',
                'sent_at' => now(),
            ]);
        }

        // Notificaciones día de vencimiento
        $subscriptionsDueToday = AdminSubscription::where('status', 'active')
            ->whereDate('current_period_end', now()->toDateString())
            ->whereDoesntHave('paymentNotifications', function ($q) {
                $q->where('notification_type', 'due_date')
                  ->whereNotNull('sent_at');
            })
            ->with('admin.user')
            ->get();

        foreach ($subscriptionsDueToday as $subscription) {
            $subscription->admin->user->notify(
                new PaymentReminderNotification($subscription, 'due_date')
            );
            
            $subscription->paymentNotifications()->create([
                'notification_type' => 'due_date',
                'sent_at' => now(),
            ]);
        }
    }
}
```

---

## 🧮 Algoritmos Específicos

### **1. Generador Round Robin**

```php
<?php
// app/Services/RoundRobinGenerator.php
namespace App\Services;

use App\Models\Season;
use App\Models\Round;
use App\Models\Match;
use Illuminate\Support\Collection;

class RoundRobinGenerator
{
    private Collection $teams;
    private Season $season;
    private array $gameDays;
    private array $matchTimes;
    private int $dailyMatches;

    public function __construct(Collection $teams, Season $season)
    {
        $this->teams = $teams;
        $this->season = $season;
        $this->gameDays = $season->game_days;
        $this->matchTimes = $season->match_times;
        $this->dailyMatches = $season->daily_matches;
    }

    public function generate(): void
    {
        $rounds = $this->generateRoundRobinRounds();
        
        if ($this->season->round_robin_type === 'double') {
            $reverseRounds = $this->generateReverseRounds($rounds);
            $rounds = array_merge($rounds, $reverseRounds);
        }

        $this->createMatchesInDatabase($rounds);
    }

    private function generateRoundRobinRounds(): array
    {
        $teams = $this->teams->pluck('id')->toArray();
        $teamCount = count($teams);
        
        if ($teamCount % 2 === 1) {
            $teams[] = null; // Agregar "bye" para número impar de equipos
            $teamCount++;
        }

        $rounds = [];
        $totalRounds = $teamCount - 1;

        for ($round = 0; $round < $totalRounds; $round++) {
            $roundMatches = [];
            
            for ($match = 0; $match < $teamCount / 2; $match++) {
                $home = ($round + $match) % ($teamCount - 1);
                $away = ($teamCount - 1 - $match + $round) % ($teamCount - 1);
                
                if ($match === 0) {
                    $away = $teamCount - 1;
                }

                // Saltar si algún equipo es "bye"
                if ($teams[$home] === null || $teams[$away] === null) {
                    continue;
                }

                $roundMatches[] = [
                    'home_team_id' => $teams[$home],
                    'away_team_id' => $teams[$away],
                ];
            }
            
            $rounds[] = $roundMatches;
        }

        return $rounds;
    }

    private function generateReverseRounds(array $rounds): array
    {
        $reverseRounds = [];
        
        foreach ($rounds as $round) {
            $reverseRound = [];
            
            foreach ($round as $match) {
                $reverseRound[] = [
                    'home_team_id' => $match['away_team_id'],
                    'away_team_id' => $match['home_team_id'],
                ];
            }
            
            $reverseRounds[] = $reverseRound;
        }

        return $reverseRounds;
    }

    private function createMatchesInDatabase(array $rounds): void
    {
        $currentDate = $this->season->start_date->copy();
        
        foreach ($rounds as $roundIndex => $roundMatches) {
            $round = Round::create([
                'season_id' => $this->season->id,
                'round_number' => $roundIndex + 1,
                'name' => "Jornada " . ($roundIndex + 1),
            ]);

            $matchesScheduled = 0;
            $currentRoundDate = $currentDate->copy();

            foreach ($roundMatches as $matchData) {
                // Encontrar próxima fecha disponible
                while (!in_array(strtolower($currentRoundDate->format('l')), $this->gameDays)) {
                    $currentRoundDate->addDay();
                }

                // Calcular horario del partido
                $matchTimeIndex = $matchesScheduled % count($this->matchTimes);
                $matchTime = $this->matchTimes[$matchTimeIndex];
                
                $matchDateTime = $currentRoundDate->copy()->setTimeFromTimeString($matchTime);

                Match::create([
                    'round_id' => $round->id,
                    'home_team_id' => $matchData['home_team_id'],
                    'away_team_id' => $matchData['away_team_id'],
                    'match_date' => $matchDateTime,
                    'status' => 'scheduled',
                ]);

                $matchesScheduled++;

                // Si se alcanzó el límite diario, pasar al siguiente día válido
                if ($matchesScheduled % $this->dailyMatches === 0) {
                    do {
                        $currentRoundDate->addDay();
                    } while (!in_array(strtolower($currentRoundDate->format('l')), $this->gameDays));
                }
            }

            // Actualizar fecha para la siguiente jornada
            $currentDate = $currentRoundDate->copy();
            
            // Agregar fechas de inicio y fin a la jornada
            $firstMatch = $round->matches()->orderBy('match_date')->first();
            $lastMatch = $round->matches()->orderBy('match_date', 'desc')->first();
            
            if ($firstMatch && $lastMatch) {
                $round->update([
                    'start_date' => $firstMatch->match_date->toDateString(),
                    'end_date' => $lastMatch->match_date->toDateString(),
                ]);
            }
        }

        // Actualizar fecha de fin de la temporada
        $lastMatch = Match::whereHas('round', function ($q) {
            $q->where('season_id', $this->season->id);
        })->orderBy('match_date', 'desc')->first();

        if ($lastMatch) {
            $this->season->update([
                'end_date' => $lastMatch->match_date->toDateString(),
            ]);
        }
    }
}
```

---

## 🚀 Próximos Pasos

1. **Implementar todos los modelos** con sus relaciones
2. **Crear controladores REST** para cada entidad
3. **Implementar servicios** de lógica de negocio
4. **Configurar jobs y colas** para procesos pesados
5. **Testing unitario** de algoritmos críticos
6. **Documentación de APIs** con Swagger/OpenAPI

---

*¡El backend de FlowFast SaaS está diseñado para ser robusto, escalable y mantenible!* ⚙️