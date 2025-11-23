# ⚽ FlowFast SaaS - Sistema de Partidos Amistosos

## 📋 Funcionalidad Completa de Partidos Amistosos

### **🎯 Características Principales**

Los **partidos amistosos** son encuentros organizados fuera del calendario oficial de la liga, pero dentro del mismo sistema de gestión. Permiten que **cualquier equipo de la liga** (de cualquier temporada) pueda enfrentarse entre sí, con flexibilidad total en configuración financiera y logística.

### **🎯 Reglas Importantes para Partidos Amistosos:**

1. **✅ Equipos Permitidos**: Cualquier equipo que haya participado en **cualquier temporada** de la liga
2. **✅ Mismo Deporte**: Ambos equipos deben practicar el **mismo deporte** (validado automáticamente)  
3. **✅ Misma Liga**: Ambos equipos deben pertenecer a la **misma liga** (pero temporadas diferentes están permitidas)
4. **❌ Restricciones**: Un equipo no puede jugar contra sí mismo

**Ejemplo de partidos válidos:**
- Equipo de Temporada 2024 vs Equipo de Temporada 2025 ✅
- Equipo de Fútbol vs Equipo de Básquet ❌ (diferente deporte)
- Equipo de Liga A vs Equipo de Liga B ❌ (diferente liga)

---

## 🏗️ Arquitectura del Sistema

### **📊 Diferencias vs Partidos de Liga**

| **Aspecto** | **Liga Regular** | **Partidos Amistosos** |
|-------------|------------------|-------------------------|
| **Programación** | Automática (Round Robin) | Manual por Admin/Encargado |
| **Equipos** | Solo equipos de la temporada actual | Cualquier equipo de la liga (cualquier temporada) |
| **Costo** | Fijo por liga | Personalizable por partido |
| **Árbitros** | Pago estándar | Pago personalizable |
| **Estadísticas** | Cuenta para tabla | Solo para reportes |
| **Deporte** | Debe coincidir con liga | Debe coincidir con liga |
| **Reportes** | Incluidos automáticamente | Sección específica |

---

## 🎮 Modelos y Relaciones

### **🏟️ Modelo FriendlyMatch**

```php
<?php
// app/Models/FriendlyMatch.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FriendlyMatch extends Model
{
    protected $fillable = [
        'league_id',
        'sport_id',
        'home_team_id',
        'away_team_id',
        'match_date',
        'venue',
        'status',
        'home_score',
        'away_score',
        'match_fee_amount',
        'referee_payment_amount',
        'venue_rental_cost',
        'description',
        'is_public',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'match_fee_amount' => 'decimal:2',
        'referee_payment_amount' => 'decimal:2',
        'venue_rental_cost' => 'decimal:2',
        'is_public' => 'boolean',
    ];

    // Relaciones
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function officials(): HasMany
    {
        return $this->hasMany(FriendlyMatchOfficial::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('match_date', '>', now())
                    ->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Métodos de utilidad
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['scheduled', 'postponed']);
    }

    public function getTotalCost(): float
    {
        return ($this->match_fee_amount * 2) + 
               $this->referee_payment_amount + 
               $this->venue_rental_cost;
    }

    public function getNetProfit(): float
    {
        $totalIncome = $this->match_fee_amount * 2;
        $totalExpenses = $this->referee_payment_amount + $this->venue_rental_cost;
        
        return $totalIncome - $totalExpenses;
    }
}
```

### **👨‍⚖️ Modelo FriendlyMatchOfficial**

```php
<?php
// app/Models/FriendlyMatchOfficial.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendlyMatchOfficial extends Model
{
    protected $fillable = [
        'friendly_match_id',
        'referee_id',
        'role',
        'confirmed',
        'payment_amount',
    ];

    protected $casts = [
        'confirmed' => 'boolean',
        'payment_amount' => 'decimal:2',
    ];

    public function friendlyMatch()
    {
        return $this->belongsTo(FriendlyMatch::class);
    }

    public function referee()
    {
        return $this->belongsTo(Referee::class);
    }
}
```

---

## 🎛️ Servicio de Gestión

### **⚽ FriendlyMatchService**

```php
<?php
// app/Services/FriendlyMatchService.php
namespace App\Services;

use App\Models\FriendlyMatch;
use App\Models\League;
use App\Models\Team;
use App\Models\Referee;
use App\Jobs\GenerateFriendlyMatchFees;
use App\Events\FriendlyMatchCreated;
use Carbon\Carbon;

class FriendlyMatchService
{
    /**
     * Crear un nuevo partido amistoso
     */
    public function createFriendlyMatch(League $league, array $data): FriendlyMatch
    {
        // Validar que los equipos pertenezcan a la liga y el deporte coincida
        $this->validateTeamsAndSport($league, $data);

        $friendlyMatch = FriendlyMatch::create([
            'league_id' => $league->id,
            'sport_id' => $league->sport_id,
            'home_team_id' => $data['home_team_id'],
            'away_team_id' => $data['away_team_id'],
            'match_date' => Carbon::parse($data['match_date']),
            'venue' => $data['venue'] ?? null,
            'status' => 'scheduled',
            'match_fee_amount' => $data['match_fee_amount'] ?? 0,
            'referee_payment_amount' => $data['referee_payment_amount'] ?? 0,
            'venue_rental_cost' => $data['venue_rental_cost'] ?? 0,
            'description' => $data['description'] ?? null,
            'is_public' => $data['is_public'] ?? true,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Asignar árbitros si se proporcionaron
        if (isset($data['referees'])) {
            $this->assignReferees($friendlyMatch, $data['referees']);
        }

        // Generar transacciones financieras automáticamente
        GenerateFriendlyMatchFees::dispatch($friendlyMatch)->delay(now()->addMinutes(1));

        // Notificar a los equipos
        $this->notifyTeams($friendlyMatch);

        broadcast(new FriendlyMatchCreated($friendlyMatch));

        return $friendlyMatch;
    }

    /**
     * Actualizar partido amistoso
     */
    public function updateFriendlyMatch(FriendlyMatch $friendlyMatch, array $data): FriendlyMatch
    {
        if (!$friendlyMatch->canBeEdited()) {
            throw new \Exception('Este partido no puede ser editado');
        }

        $originalData = $friendlyMatch->toArray();

        $friendlyMatch->update($data);

        // Si cambiaron los montos, regenerar transacciones
        if ($this->financialDataChanged($originalData, $data)) {
            $this->regenerateFinancialTransactions($friendlyMatch);
        }

        return $friendlyMatch;
    }

    /**
     * Finalizar partido amistoso con resultado
     */
    public function finishMatch(FriendlyMatch $friendlyMatch, array $result): FriendlyMatch
    {
        $friendlyMatch->update([
            'status' => 'completed',
            'home_score' => $result['home_score'],
            'away_score' => $result['away_score'],
        ]);

        // Marcar árbitros como confirmados si no lo están
        $friendlyMatch->officials()
            ->where('confirmed', false)
            ->update(['confirmed' => true]);

        // Notificar resultado a equipos y árbitros
        $this->notifyMatchResult($friendlyMatch);

        return $friendlyMatch;
    }

    /**
     * Asignar árbitros al partido
     */
    public function assignReferees(FriendlyMatch $friendlyMatch, array $referees): void
    {
        // Limpiar asignaciones previas
        $friendlyMatch->officials()->delete();

        $totalPayment = $friendlyMatch->referee_payment_amount;
        $refereeCount = count($referees);

        foreach ($referees as $refereeData) {
            // Calcular pago proporcional o usar el monto específico
            $paymentAmount = $refereeData['payment_amount'] ?? 
                            ($refereeCount > 0 ? $totalPayment / $refereeCount : 0);

            $friendlyMatch->officials()->create([
                'referee_id' => $refereeData['referee_id'],
                'role' => $refereeData['role'],
                'payment_amount' => $paymentAmount,
                'confirmed' => false,
            ]);

            // Notificar al árbitro
            $referee = Referee::find($refereeData['referee_id']);
            $referee->user->notify(
                new \App\Notifications\FriendlyMatchAssigned($friendlyMatch, $refereeData['role'])
            );
        }
    }

    /**
     * Obtener estadísticas de partidos amistosos
     */
    public function getFriendlyMatchStats(League $league, ?string $period = null): array
    {
        $query = $league->friendlyMatches();

        if ($period) {
            $dateRange = $this->getDateRange($period);
            $query->whereBetween('match_date', $dateRange);
        }

        $matches = $query->get();

        return [
            'total_matches' => $matches->count(),
            'completed_matches' => $matches->where('status', 'completed')->count(),
            'upcoming_matches' => $matches->where('status', 'scheduled')
                                          ->where('match_date', '>', now())->count(),
            'total_revenue' => $matches->sum(function ($match) {
                return $match->match_fee_amount * 2;
            }),
            'total_expenses' => $matches->sum(function ($match) {
                return $match->referee_payment_amount + $match->venue_rental_cost;
            }),
            'net_profit' => $matches->sum(function ($match) {
                return $match->getNetProfit();
            }),
            'average_match_fee' => $matches->avg('match_fee_amount'),
        ];
    }

    /**
     * Generar reporte de partidos amistosos
     */
    public function generateFriendlyMatchReport(League $league, array $options = []): array
    {
        $startDate = Carbon::parse($options['start_date'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($options['end_date'] ?? now()->endOfMonth());

        $matches = $league->friendlyMatches()
            ->whereBetween('match_date', [$startDate, $endDate])
            ->with(['homeTeam', 'awayTeam', 'officials.referee.user'])
            ->get();

        return [
            'report_info' => [
                'league_name' => $league->name,
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ],
                'generated_at' => now()->format('Y-m-d H:i:s'),
            ],
            'summary' => $this->getFriendlyMatchStats($league, 'custom'),
            'matches' => $matches->map(function ($match) {
                return [
                    'id' => $match->id,
                    'date' => $match->match_date->format('Y-m-d H:i'),
                    'teams' => $match->homeTeam->name . ' vs ' . $match->awayTeam->name,
                    'venue' => $match->venue,
                    'status' => $match->status,
                    'result' => $match->isCompleted() ? 
                               $match->home_score . '-' . $match->away_score : 'Pendiente',
                    'financial' => [
                        'match_fee' => $match->match_fee_amount,
                        'total_income' => $match->match_fee_amount * 2,
                        'referee_cost' => $match->referee_payment_amount,
                        'venue_cost' => $match->venue_rental_cost,
                        'net_profit' => $match->getNetProfit(),
                    ],
                ];
            }),
        ];
    }

    private function validateTeamsAndSport(League $league, array $data): void
    {
        $homeTeam = Team::find($data['home_team_id']);
        $awayTeam = Team::find($data['away_team_id']);

        // Verificar que ambos equipos existan
        if (!$homeTeam) {
            throw new \Exception('El equipo local no existe');
        }

        if (!$awayTeam) {
            throw new \Exception('El equipo visitante no existe');
        }

        // Verificar que ambos equipos pertenezcan a la misma liga (cualquier temporada)
        if ($homeTeam->season->league_id !== $league->id) {
            throw new \Exception('El equipo local no pertenece a esta liga');
        }

        if ($awayTeam->season->league_id !== $league->id) {
            throw new \Exception('El equipo visitante no pertenece a esta liga');
        }

        // Verificar que no sea el mismo equipo
        if ($homeTeam->id === $awayTeam->id) {
            throw new \Exception('Un equipo no puede jugar contra sí mismo');
        }

        // Verificar que ambos equipos practiquen el mismo deporte (a través de sus temporadas)
        if ($homeTeam->season->league->sport_id !== $awayTeam->season->league->sport_id) {
            throw new \Exception('Los equipos deben practicar el mismo deporte');
        }
    }

    private function financialDataChanged(array $original, array $new): bool
    {
        $financialFields = ['match_fee_amount', 'referee_payment_amount', 'venue_rental_cost'];
        
        foreach ($financialFields as $field) {
            if (isset($new[$field]) && $original[$field] !== $new[$field]) {
                return true;
            }
        }

        return false;
    }

    private function regenerateFinancialTransactions(FriendlyMatch $friendlyMatch): void
    {
        // Cancelar transacciones pendientes
        $friendlyMatch->incomes()->where('payment_status', 'pending')->delete();
        $friendlyMatch->expenses()->where('payment_status', 'pending')->delete();

        // Regenerar transacciones
        GenerateFriendlyMatchFees::dispatchSync($friendlyMatch);
    }

    private function notifyTeams(FriendlyMatch $friendlyMatch): void
    {
        $friendlyMatch->homeTeam->coach->user->notify(
            new \App\Notifications\FriendlyMatchScheduled($friendlyMatch, 'home')
        );

        $friendlyMatch->awayTeam->coach->user->notify(
            new \App\Notifications\FriendlyMatchScheduled($friendlyMatch, 'away')
        );
    }

    private function notifyMatchResult(FriendlyMatch $friendlyMatch): void
    {
        // Notificar a equipos
        $friendlyMatch->homeTeam->coach->user->notify(
            new \App\Notifications\FriendlyMatchResult($friendlyMatch)
        );

        $friendlyMatch->awayTeam->coach->user->notify(
            new \App\Notifications\FriendlyMatchResult($friendlyMatch)
        );

        // Notificar a árbitros
        foreach ($friendlyMatch->officials as $official) {
            $official->referee->user->notify(
                new \App\Notifications\FriendlyMatchResult($friendlyMatch)
            );
        }
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
```

---

## 🎮 Componente Livewire

### **⚽ FriendlyMatchManager**

```php
<?php
// app/Livewire/FriendlyMatchManager.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\League;
use App\Models\FriendlyMatch;
use App\Models\Team;
use App\Models\Referee;
use App\Services\FriendlyMatchService;

class FriendlyMatchManager extends Component
{
    public League $league;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showResultModal = false;
    
    // Formulario de creación
    public $home_team_id;
    public $away_team_id;
    public $match_date;
    public $venue;
    public $match_fee_amount = 0;
    public $referee_payment_amount = 0;
    public $venue_rental_cost = 0;
    public $description;
    public $is_public = true;
    public $notes;
    
    // Árbitros
    public $selectedReferees = [];
    
    // Resultado
    public $selectedMatch;
    public $home_score = 0;
    public $away_score = 0;

    protected $rules = [
        'home_team_id' => 'required|exists:teams,id',
        'away_team_id' => 'required|exists:teams,id|different:home_team_id',
        'match_date' => 'required|date|after:now',
        'venue' => 'nullable|string|max:191',
        'match_fee_amount' => 'required|numeric|min:0',
        'referee_payment_amount' => 'required|numeric|min:0',
        'venue_rental_cost' => 'nullable|numeric|min:0',
        'description' => 'nullable|string|max:500',
        'notes' => 'nullable|string|max:1000',
    ];

    public function createMatch()
    {
        $this->validate();

        $friendlyService = new FriendlyMatchService();

        try {
            $friendlyMatch = $friendlyService->createFriendlyMatch($this->league, [
                'home_team_id' => $this->home_team_id,
                'away_team_id' => $this->away_team_id,
                'match_date' => $this->match_date,
                'venue' => $this->venue,
                'match_fee_amount' => $this->match_fee_amount,
                'referee_payment_amount' => $this->referee_payment_amount,
                'venue_rental_cost' => $this->venue_rental_cost ?? 0,
                'description' => $this->description,
                'is_public' => $this->is_public,
                'notes' => $this->notes,
                'referees' => $this->selectedReferees,
            ]);

            session()->flash('message', 'Partido amistoso creado exitosamente');
            $this->resetForm();
            $this->showCreateModal = false;

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function finishMatch()
    {
        $this->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $friendlyService = new FriendlyMatchService();

        try {
            $friendlyService->finishMatch($this->selectedMatch, [
                'home_score' => $this->home_score,
                'away_score' => $this->away_score,
            ]);

            session()->flash('message', 'Resultado registrado exitosamente');
            $this->showResultModal = false;
            $this->resetResultForm();

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openResultModal(FriendlyMatch $match)
    {
        $this->selectedMatch = $match;
        $this->showResultModal = true;
    }

    public function addReferee()
    {
        $this->selectedReferees[] = [
            'referee_id' => '',
            'role' => 'main',
            'payment_amount' => 0,
        ];
    }

    public function removeReferee($index)
    {
        unset($this->selectedReferees[$index]);
        $this->selectedReferees = array_values($this->selectedReferees);
    }

    private function resetForm()
    {
        $this->home_team_id = null;
        $this->away_team_id = null;
        $this->match_date = null;
        $this->venue = null;
        $this->match_fee_amount = 0;
        $this->referee_payment_amount = 0;
        $this->venue_rental_cost = 0;
        $this->description = null;
        $this->is_public = true;
        $this->notes = null;
        $this->selectedReferees = [];
    }

    private function resetResultForm()
    {
        $this->home_score = 0;
        $this->away_score = 0;
        $this->selectedMatch = null;
    }

    public function render()
    {
        $friendlyMatches = $this->league->friendlyMatches()
            ->with(['homeTeam', 'awayTeam', 'officials.referee.user'])
            ->orderBy('match_date', 'desc')
            ->get();

        // Obtener TODOS los equipos de la liga (de todas las temporadas)
        $teams = Team::whereHas('season', function($query) {
            $query->where('league_id', $this->league->id);
        })->with(['coach.user', 'season'])
          ->orderBy('created_at', 'desc') // Más recientes primero
          ->get();
        $referees = Referee::whereHas('user', function($query) {
            $query->where('status', 'active');
        })->with('user')->get();

        return view('livewire.friendly-match-manager', compact('friendlyMatches', 'teams', 'referees'));
    }
}
```

---

## 📊 Integración en Reportes

### **Actualización del Dashboard Financiero**

Los partidos amistosos se incluyen automáticamente en todos los reportes financieros gracias a las foreign keys `friendly_match_id` en las tablas `incomes` y `expenses`.

### **Métricas Específicas**

```php
// En FinancialDashboardService, agregar:
private function getFriendlyMatchMetrics(League $league, array $dateRange): array
{
    $friendlyMatches = $league->friendlyMatches()
        ->whereBetween('match_date', $dateRange)
        ->get();

    $friendlyIncome = $league->incomes()
        ->whereNotNull('friendly_match_id')
        ->whereBetween('created_at', $dateRange)
        ->where('payment_status', 'confirmed')
        ->sum('amount');

    $friendlyExpenses = $league->expenses()
        ->whereNotNull('friendly_match_id')
        ->whereBetween('created_at', $dateRange)
        ->where('payment_status', 'confirmed')
        ->sum('amount');

    return [
        'total_friendly_matches' => $friendlyMatches->count(),
        'completed_friendly_matches' => $friendlyMatches->where('status', 'completed')->count(),
        'friendly_revenue' => $friendlyIncome,
        'friendly_expenses' => $friendlyExpenses,
        'friendly_net_profit' => $friendlyIncome - $friendlyExpenses,
    ];
}
```

---

## 🚀 Beneficios del Sistema de Amistosos

### **✅ Para Administradores**
- **Flexibilidad total**: Configurar costos específicos por partido
- **Control financiero**: Seguimiento separado de ingresos/egresos
- **Automatización**: Generación automática de transacciones
- **Reportes integrados**: Métricas incluidas en dashboard principal

### **✅ Para Equipos**
- **Práctica adicional**: Partidos fuera del calendario oficial
- **Transparencia**: Costos claros antes del partido
- **Notificaciones**: Alertas automáticas de partidos y pagos

### **✅ Para Árbitros**
- **Ingresos adicionales**: Oportunidades extra de arbitraje
- **Pagos personalizables**: Montos específicos por partido
- **Sistema de confirmación**: Proceso de pago estructurado

---

*¡Los partidos amistosos complementan perfectamente el ecosistema de FlowFast SaaS, agregando flexibilidad sin comprometer la estructura del sistema!* ⚽💰✨