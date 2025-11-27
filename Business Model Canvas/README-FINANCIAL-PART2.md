# 💰 FlowFast SaaS - Sistema Financiero (Parte 2)

## 📋 Implementación y Servicios

### **Enlaces de Navegación:**
- ← [Parte 1: Fundamentos](README-FINANCIAL-PART1.md)
- → [Parte 3: Reportes y Analytics](README-FINANCIAL-PART3.md)
- → [Parte 4: Membresías SaaS](README-FINANCIAL-PART4.md)

---

## 💼 Servicios Principales

### **🔄 IncomeService - Gestión de Ingresos**

```php
<?php
// app/Services/IncomeService.php
namespace App\Services;

use App\Models\Income;
use App\Models\Team;
use App\Models\Match;
use App\Models\League;
use App\Jobs\GenerateMatchFees;
use App\Jobs\GenerateLateFees;
use App\Events\IncomeGenerated;

class IncomeService
{
    /**
     * Generar cuota de inscripción manual
     */
    public function generateRegistrationFee(Team $team, array $data): Income
    {
        $income = Income::create([
            'league_id' => $team->league_id,
            'team_id' => $team->id,
            'income_type' => 'registration_fee',
            'amount' => $data['amount'],
            'description' => "Cuota de inscripción - {$team->name}",
            'due_date' => $data['due_date'] ?? now()->addDays(7),
            'payment_status' => 'pending',
            'generated_by' => auth()->id(),
            'metadata' => [
                'season_id' => $team->league->current_season_id,
                'manual_generation' => true,
                'notes' => $data['notes'] ?? null,
            ],
        ]);

        // Notificar al equipo
        $team->coach->user->notify(
            new \App\Notifications\NewIncomeGenerated($income)
        );

        broadcast(new IncomeGenerated($income));
        
        return $income;
    }

    /**
     * Generar pago por partido (automático)
     */
    public function generateMatchFee(Match $match): array
    {
        $incomes = [];
        $league = $match->league;
        $matchFeeAmount = $league->settings['match_fee_amount'] ?? 50;

        foreach ([$match->home_team_id, $match->away_team_id] as $teamId) {
            $team = Team::find($teamId);
            
            $income = Income::create([
                'league_id' => $league->id,
                'team_id' => $teamId,
                'match_id' => $match->id,
                'income_type' => 'match_fee',
                'amount' => $matchFeeAmount,
                'description' => "Pago por partido - {$team->name} vs " . 
                               ($teamId === $match->home_team_id 
                                   ? $match->awayTeam->name 
                                   : $match->homeTeam->name),
                'due_date' => now()->addDays(3), // 3 días para pagar
                'payment_status' => 'pending',
                'generated_by' => 1, // Sistema
                'metadata' => [
                    'match_id' => $match->id,
                    'auto_generated' => true,
                    'match_date' => $match->scheduled_date,
                    'match_result' => [
                        'home_score' => $match->home_team_score,
                        'away_score' => $match->away_team_score,
                    ],
                ],
            ]);

            // Notificar al coach del equipo
            $team->coach->user->notify(
                new \App\Notifications\MatchFeeGenerated($income, $match)
            );

            $incomes[] = $income;
        }

        // Programar job para verificar pagos tardíos
        GenerateLateFees::dispatch($match)->delay(now()->addDays(4));

        return $incomes;
    }

    /**
     * Generar multa por pago tardío
     */
    public function generateLateFee(Income $originalIncome): ?Income
    {
        // Verificar si ya está pagado
        if ($originalIncome->payment_status === 'confirmed') {
            return null;
        }

        $league = $originalIncome->league;
        $lateFeePercent = $league->settings['late_fee_percent'] ?? 10; // 10% de recargo
        $lateFeeAmount = ($originalIncome->amount * $lateFeePercent) / 100;

        $lateFee = Income::create([
            'league_id' => $originalIncome->league_id,
            'team_id' => $originalIncome->team_id,
            'parent_income_id' => $originalIncome->id,
            'income_type' => 'late_payment_fee',
            'amount' => $lateFeeAmount,
            'description' => "Recargo por pago tardío ({$lateFeePercent}%) - " . 
                           $originalIncome->description,
            'due_date' => now()->addDays(7),
            'payment_status' => 'pending',
            'generated_by' => 1, // Sistema
            'metadata' => [
                'original_income_id' => $originalIncome->id,
                'late_fee_percent' => $lateFeePercent,
                'auto_generated' => true,
                'days_overdue' => now()->diffInDays($originalIncome->due_date),
            ],
        ]);

        // Marcar el income original como overdue
        $originalIncome->update(['payment_status' => 'overdue']);

        // Notificar al equipo
        $originalIncome->team->coach->user->notify(
            new \App\Notifications\LateFeeGenerated($lateFee, $originalIncome)
        );

        return $lateFee;
    }

    /**
     * Procesar pago con gateway (Stripe/PayPal/etc.)
     */
    public function processOnlinePayment(Income $income, array $paymentData): array
    {
        $paymentService = app(PaymentServiceInterface::class);
        
        // Crear intención de pago
        $paymentIntent = $paymentService->createPaymentIntent([
            'amount' => $income->amount,
            'currency' => $income->league->currency ?? 'USD',
            'description' => $income->description,
            'metadata' => [
                'income_id' => $income->id,
                'league_id' => $income->league_id,
                'team_id' => $income->team_id,
                'payment_type' => 'income',
            ],
            'email' => $paymentData['email'] ?? null,
        ]);

        if ($paymentIntent['success']) {
            // Guardar información del pago
            $income->update([
                'payment_gateway_id' => $paymentIntent['payment_intent_id'],
                'payment_gateway' => 'stripe', // o el gateway configurado
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent['client_secret'],
                'payment_intent_id' => $paymentIntent['payment_intent_id'],
            ];
        }

        return [
            'success' => false,
            'error' => $paymentIntent['error'],
        ];
    }

    /**
     * Generar pago por partido amistoso
     */
    public function generateFriendlyMatchFee(\App\Models\FriendlyMatch $friendlyMatch): array
    {
        $incomes = [];
        $league = $friendlyMatch->league;
        $matchFeeAmount = $friendlyMatch->match_fee_amount; // Monto configurado específicamente

        foreach ([$friendlyMatch->home_team_id, $friendlyMatch->away_team_id] as $teamId) {
            $team = Team::find($teamId);
            
            $income = Income::create([
                'league_id' => $league->id,
                'team_id' => $teamId,
                'friendly_match_id' => $friendlyMatch->id,
                'income_type' => 'friendly_match_fee',
                'amount' => $matchFeeAmount,
                'description' => "Pago por partido amistoso - {$team->name} vs " . 
                               ($teamId === $friendlyMatch->home_team_id 
                                   ? $friendlyMatch->awayTeam->name 
                                   : $friendlyMatch->homeTeam->name),
                'due_date' => $friendlyMatch->match_date->subDays(1), // 1 día antes del partido
                'payment_status' => 'pending',
                'generated_by' => auth()->id() ?? 1, // Usuario actual o sistema
                'metadata' => [
                    'friendly_match_id' => $friendlyMatch->id,
                    'auto_generated' => true,
                    'match_date' => $friendlyMatch->match_date,
                    'venue' => $friendlyMatch->venue,
                    'is_friendly' => true,
                ],
            ]);

            // Notificar al coach del equipo
            $team->coach->user->notify(
                new \App\Notifications\FriendlyMatchFeeGenerated($income, $friendlyMatch)
            );

            $incomes[] = $income;
        }

        return $incomes;
    }

    /**
     * Confirmar pago manual (efectivo/transferencia)
     */
    public function confirmManualPayment(Income $income, array $data): bool
    {
        $validationService = new IncomeValidationService();
        
        try {
            // Paso 1: Equipo marca como pagado
            $validationService->stepOne($income, [
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
                'proof_document' => $data['proof_document'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error confirming manual payment: ' . $e->getMessage());
            return false;
        }
    }
}
```

### **💸 ExpenseService - Gestión de Egresos**

```php
<?php
// app/Services/ExpenseService.php
namespace App\Services;

use App\Models\Expense;
use App\Models\Match;
use App\Models\Referee;
use App\Models\League;
use App\Jobs\ProcessRefereePayments;
use App\Events\ExpenseGenerated;

class ExpenseService
{
    /**
     * Generar pagos a árbitros automáticamente
     */
    public function generateRefereePayments(Match $match): array
    {
        $expenses = [];
        $league = $match->league;
        
        // Configuración de pagos por rol
        $refereePayments = [
            'main_referee' => $league->settings['main_referee_payment'] ?? 100,
            'assistant_referee' => $league->settings['assistant_referee_payment'] ?? 60,
            'scorer' => $league->settings['scorer_payment'] ?? 40,
        ];

        foreach ($refereePayments as $role => $amount) {
            $refereeId = $match->{$role . '_id'};
            
            if (!$refereeId) continue;

            $referee = Referee::find($refereeId);
            
            $expense = Expense::create([
                'league_id' => $league->id,
                'match_id' => $match->id,
                'referee_id' => $refereeId,
                'expense_type' => 'referee_payment',
                'amount' => $amount,
                'description' => "Pago por arbitraje ({$role}) - Partido #{$match->id}",
                'payment_status' => 'pending',
                'generated_by' => 1, // Sistema
                'metadata' => [
                    'match_id' => $match->id,
                    'referee_role' => $role,
                    'auto_generated' => true,
                    'match_date' => $match->scheduled_date,
                    'teams' => [
                        'home' => $match->homeTeam->name,
                        'away' => $match->awayTeam->name,
                    ],
                ],
            ]);

            // Notificar al árbitro
            $referee->user->notify(
                new \App\Notifications\RefereePaymentGenerated($expense, $match)
            );

            $expenses[] = $expense;
        }

        // Programar procesamiento de pagos
        ProcessRefereePayments::dispatch($match)->delay(now()->addHours(2));

        broadcast(new ExpenseGenerated($expenses));

        return $expenses;
    }

    /**
     * Procesar pago manual a beneficiario
     */
    public function processManualPayment(Expense $expense, array $data): bool
    {
        $validationService = new ExpenseValidationService();
        
        try {
            // Paso 1: Admin marca pago como realizado
            $validationService->stepOne($expense, [
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
                'proof_document' => $data['proof_document'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error processing manual expense payment: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generar gasto por alquiler de cancha
     */
    public function generateVenueRental(League $league, array $data): Expense
    {
        $expense = Expense::create([
            'league_id' => $league->id,
            'expense_type' => 'venue_rental',
            'amount' => $data['amount'],
            'description' => $data['description'] ?? 'Alquiler de cancha',
            'payment_status' => 'pending',
            'generated_by' => auth()->id(),
            'metadata' => [
                'venue_name' => $data['venue_name'] ?? null,
                'rental_date' => $data['rental_date'] ?? null,
                'rental_hours' => $data['rental_hours'] ?? null,
                'manual_generation' => true,
            ],
        ]);

        // Notificar a admin/encargado
        $league->admin->user->notify(
            new \App\Notifications\NewExpenseGenerated($expense)
        );

        return $expense;
    }

    /**
     * Generar premios al final de temporada
     */
    public function generateSeasonPrizes(League $league): array
    {
        $expenses = [];
        $prizeConfig = $league->settings['season_prizes'] ?? [];

        // Obtener posiciones finales
        $finalStandings = $this->getFinalStandings($league);

        foreach ($prizeConfig as $position => $prizeData) {
            if (!isset($finalStandings[$position - 1])) continue;

            $team = $finalStandings[$position - 1];
            
            $expense = Expense::create([
                'league_id' => $league->id,
                'team_id' => $team->id,
                'expense_type' => 'prize_money',
                'amount' => $prizeData['amount'],
                'description' => "Premio por {$prizeData['title']} - {$team->name}",
                'payment_status' => 'pending',
                'generated_by' => auth()->id(),
                'metadata' => [
                    'final_position' => $position,
                    'prize_title' => $prizeData['title'],
                    'season_id' => $league->current_season_id,
                    'final_points' => $team->points,
                ],
            ]);

            // Notificar al equipo
            $team->coach->user->notify(
                new \App\Notifications\PrizeAwarded($expense, $position)
            );

            $expenses[] = $expense;
        }

        return $expenses;
    }

    /**
     * Generar pagos a árbitros para partido amistoso
     */
    public function generateFriendlyRefereePayments(\App\Models\FriendlyMatch $friendlyMatch): array
    {
        $expenses = [];
        
        // Obtener árbitros asignados al partido amistoso
        $refereeAssignments = $friendlyMatch->officials; // Relación con friendly_match_officials
        
        foreach ($refereeAssignments as $assignment) {
            $referee = $assignment->referee;
            
            $expense = Expense::create([
                'league_id' => $friendlyMatch->league_id,
                'friendly_match_id' => $friendlyMatch->id,
                'referee_id' => $referee->id,
                'expense_type' => 'friendly_referee_payment',
                'amount' => $assignment->payment_amount, // Monto específico por árbitro
                'description' => "Pago por arbitraje amistoso ({$assignment->role}) - Partido #{$friendlyMatch->id}",
                'payment_status' => 'pending',
                'generated_by' => 1, // Sistema
                'metadata' => [
                    'friendly_match_id' => $friendlyMatch->id,
                    'referee_role' => $assignment->role,
                    'auto_generated' => true,
                    'match_date' => $friendlyMatch->match_date,
                    'teams' => [
                        'home' => $friendlyMatch->homeTeam->name,
                        'away' => $friendlyMatch->awayTeam->name,
                    ],
                    'is_friendly' => true,
                ],
            ]);

            // Notificar al árbitro
            $referee->user->notify(
                new \App\Notifications\FriendlyRefereePaymentGenerated($expense, $friendlyMatch)
            );

            $expenses[] = $expense;
        }

        broadcast(new ExpenseGenerated($expenses));

        return $expenses;
    }

    private function getFinalStandings(League $league): array
    {
        // Obtener equipos ordenados por posición final
        return $league->teams()
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get()
            ->toArray();
    }
}
```

---

## 🤖 Automatización con Jobs

### **⚡ Job para Generar Pagos por Partido**

```php
<?php
// app/Jobs/GenerateMatchFees.php
namespace App\Jobs;

use App\Models\Match;
use App\Services\IncomeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMatchFees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Match $match
    ) {}

    public function handle(IncomeService $incomeService): void
    {
        // Solo generar si el partido terminó
        if ($this->match->status !== 'finished') {
            return;
        }

        // Verificar si ya se generaron los pagos
        $existingIncomes = $this->match->incomes()
            ->where('income_type', 'match_fee')
            ->count();

        if ($existingIncomes > 0) {
            \Log::info("Match fees already generated for match {$this->match->id}");
            return;
        }

        try {
            $incomes = $incomeService->generateMatchFee($this->match);
            
            \Log::info("Generated " . count($incomes) . " match fees for match {$this->match->id}");
            
        } catch (\Exception $e) {
            \Log::error("Failed to generate match fees for match {$this->match->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("GenerateMatchFees job failed for match {$this->match->id}: " . $exception->getMessage());
        
        // Notificar al admin de la liga
        $this->match->league->admin->user->notify(
            new \App\Notifications\JobFailedNotification('GenerateMatchFees', $exception->getMessage())
        );
    }
}
```

### **⏰ Job para Generar Recargos por Pago Tardío**

```php
<?php
// app/Jobs/GenerateLateFees.php
namespace App\Jobs;

use App\Models\Match;
use App\Models\Income;
use App\Services\IncomeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLateFees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Match $match
    ) {}

    public function handle(IncomeService $incomeService): void
    {
        // Buscar pagos por partido pendientes
        $overdueIncomes = Income::where('match_id', $this->match->id)
            ->where('income_type', 'match_fee')
            ->where('payment_status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueIncomes as $income) {
            // Verificar si ya tiene recargo
            $existingLateFee = Income::where('parent_income_id', $income->id)
                ->where('income_type', 'late_payment_fee')
                ->exists();

            if (!$existingLateFee) {
                $incomeService->generateLateFee($income);
            }
        }
    }
}
```

### **💰 Job para Procesar Pagos de Árbitros**

```php
<?php
// app/Jobs/ProcessRefereePayments.php
namespace App\Jobs;

use App\Models\Match;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRefereePayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Match $match
    ) {}

    public function handle(ExpenseService $expenseService): void
    {
        // Verificar que la liga tenga suficiente balance
        $totalRefereePayments = Expense::where('match_id', $this->match->id)
            ->where('expense_type', 'referee_payment')
            ->sum('amount');

        $leagueBalance = $this->calculateLeagueBalance();

        if ($leagueBalance < $totalRefereePayments) {
            \Log::warning("Insufficient balance for referee payments in league {$this->match->league_id}");
            
            // Notificar al admin
            $this->match->league->admin->user->notify(
                new \App\Notifications\InsufficientBalanceWarning($this->match, $totalRefereePayments, $leagueBalance)
            );
            
            return;
        }

        // Si hay balance suficiente, marcar como listos para pago
        Expense::where('match_id', $this->match->id)
            ->where('expense_type', 'referee_payment')
            ->where('payment_status', 'pending')
            ->update(['payment_status' => 'ready_for_payment']);

        // Notificar al admin que los pagos están listos
        $this->match->league->admin->user->notify(
            new \App\Notifications\RefereePaymentsReady($this->match)
        );
    }

    private function calculateLeagueBalance(): float
    {
        $totalIncome = Income::where('league_id', $this->match->league_id)
            ->where('payment_status', 'confirmed')
            ->sum('amount');

        $totalExpenses = Expense::where('league_id', $this->match->league_id)
            ->where('payment_status', 'confirmed')
            ->sum('amount');

        return $totalIncome - $totalExpenses;
    }
}

### **⚽ Job para Generar Pagos de Partidos Amistosos**

```php
<?php
// app/Jobs/GenerateFriendlyMatchFees.php
namespace App\Jobs;

use App\Models\FriendlyMatch;
use App\Services\IncomeService;
use App\Services\ExpenseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateFriendlyMatchFees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private FriendlyMatch $friendlyMatch
    ) {}

    public function handle(IncomeService $incomeService, ExpenseService $expenseService): void
    {
        // Verificar si ya se generaron los ingresos
        $existingIncomes = $this->friendlyMatch->incomes()
            ->where('income_type', 'friendly_match_fee')
            ->count();

        if ($existingIncomes === 0 && $this->friendlyMatch->match_fee_amount > 0) {
            try {
                $incomes = $incomeService->generateFriendlyMatchFee($this->friendlyMatch);
                \Log::info("Generated " . count($incomes) . " friendly match fees for match {$this->friendlyMatch->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to generate friendly match fees: " . $e->getMessage());
            }
        }

        // Verificar si ya se generaron los egresos para árbitros
        $existingExpenses = $this->friendlyMatch->expenses()
            ->where('expense_type', 'friendly_referee_payment')
            ->count();

        if ($existingExpenses === 0 && $this->friendlyMatch->referee_payment_amount > 0) {
            try {
                $expenses = $expenseService->generateFriendlyRefereePayments($this->friendlyMatch);
                \Log::info("Generated " . count($expenses) . " referee payments for friendly match {$this->friendlyMatch->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to generate friendly referee payments: " . $e->getMessage());
            }
        }

        // Si hay costo de cancha, generar egreso
        if ($this->friendlyMatch->venue_rental_cost > 0) {
            $this->generateVenueRentalExpense($expenseService);
        }
    }

    private function generateVenueRentalExpense(ExpenseService $expenseService): void
    {
        // Verificar si ya existe el egreso por alquiler
        $existingRental = $this->friendlyMatch->expenses()
            ->where('expense_type', 'venue_rental')
            ->exists();

        if (!$existingRental) {
            try {
                \App\Models\Expense::create([
                    'league_id' => $this->friendlyMatch->league_id,
                    'friendly_match_id' => $this->friendlyMatch->id,
                    'expense_type' => 'venue_rental',
                    'amount' => $this->friendlyMatch->venue_rental_cost,
                    'description' => "Alquiler de cancha para partido amistoso - {$this->friendlyMatch->homeTeam->name} vs {$this->friendlyMatch->awayTeam->name}",
                    'payment_status' => 'pending',
                    'generated_by' => 1, // Sistema
                    'metadata' => [
                        'friendly_match_id' => $this->friendlyMatch->id,
                        'venue' => $this->friendlyMatch->venue,
                        'match_date' => $this->friendlyMatch->match_date,
                        'auto_generated' => true,
                        'is_friendly' => true,
                    ],
                ]);

                \Log::info("Generated venue rental expense for friendly match {$this->friendlyMatch->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to generate venue rental expense: " . $e->getMessage());
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("GenerateFriendlyMatchFees job failed for match {$this->friendlyMatch->id}: " . $exception->getMessage());
        
        // Notificar al admin de la liga
        $this->friendlyMatch->league->admin->user->notify(
            new \App\Notifications\JobFailedNotification('GenerateFriendlyMatchFees', $exception->getMessage())
        );
    }
}
```
```

---

## 🔄 Webhooks y Integraciones

### **Webhook Controller para Stripe**

```php
<?php
// app/Http/Controllers/Api/WebhookController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Services\Payment\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentServiceInterface $paymentService
    ) {}

    /**
     * Manejar webhooks de Stripe
     */
    public function stripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        // Verificar signature del webhook
        if (!$this->paymentService->verifyWebhookSignature($payload, $signature)) {
            \Log::warning('Invalid Stripe webhook signature');
            return response('Invalid signature', 400);
        }

        $event = json_decode($payload, true);

        try {
            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event['data']['object']);
                    break;

                case 'payment_intent.requires_action':
                    $this->handlePaymentRequiresAction($event['data']['object']);
                    break;

                default:
                    \Log::info('Unhandled Stripe webhook event: ' . $event['type']);
            }

            return response('Webhook handled', 200);

        } catch (\Exception $e) {
            \Log::error('Stripe webhook error: ' . $e->getMessage());
            return response('Webhook processing failed', 500);
        }
    }

    private function handlePaymentSucceeded(array $paymentIntent): void
    {
        $incomeId = $paymentIntent['metadata']['income_id'] ?? null;
        
        if (!$incomeId) {
            \Log::warning('Payment succeeded but no income_id in metadata');
            return;
        }

        $income = Income::find($incomeId);
        
        if (!$income) {
            \Log::warning("Income not found for payment: {$paymentIntent['id']}");
            return;
        }
   
        // Confirmar pago automáticamente
        $income->update([
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'paid_at' => now(),
            'payment_gateway_reference' => $paymentIntent['id'],
        ]);

        // Trigger validación automática
        $validationService = new \App\Services\IncomeValidationService();
        
        // Como es pago online, saltamos al paso 2 (confirmación del admin)
        // El paso 1 ya se considera completado por el gateway
        try {
            $validationService->stepTwo($income, [
                'notes' => 'Pago confirmado automáticamente por Stripe',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in automatic validation: ' . $e->getMessage());
        }

        \Log::info("Payment succeeded and processed for income {$incomeId}");
    }

    private function handlePaymentFailed(array $paymentIntent): void
    {
        $incomeId = $paymentIntent['metadata']['income_id'] ?? null;
        
        if (!$incomeId) return;

        $income = Income::find($incomeId);
        
        if (!$income) return;

        // Marcar como fallido y resetear
        $income->update([
            'payment_status' => 'pending',
            'payment_gateway_reference' => $paymentIntent['id'],
            'payment_failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Payment failed',
        ]);

        // Notificar al equipo del fallo
        $income->team->coach->user->notify(
            new \App\Notifications\PaymentFailedNotification($income, $paymentIntent['last_payment_error']['message'] ?? 'Unknown error')
        );

        \Log::warning("Payment failed for income {$incomeId}: " . ($paymentIntent['last_payment_error']['message'] ?? 'Unknown error'));
    }

    private function handlePaymentRequiresAction(array $paymentIntent): void
    {
        // Manejar casos donde se requiere acción adicional (3D Secure, etc.)
        $incomeId = $paymentIntent['metadata']['income_id'] ?? null;
        
        if (!$incomeId) return;

        $income = Income::find($incomeId);
        
        if (!$income) return;

        // Notificar que se requiere acción
        $income->team->coach->user->notify(
            new \App\Notifications\PaymentRequiresActionNotification($income, $paymentIntent['client_secret'])
        );
    }
}
```

---

## 📊 Sistema de Conciliación

### **Conciliación Automática Diaria**

```php
<?php
// app/Jobs/DailyReconciliation.php
namespace App\Jobs;

use App\Models\League;
use App\Models\Income;
use App\Models\Expense;
use App\Models\ReconciliationReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DailyReconciliation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $leagues = League::where('status', 'active')->get();

        foreach ($leagues as $league) {
            $this->reconcileLeague($league);
        }
    }

    private function reconcileLeague(League $league): void
    {
        $yesterday = now()->subDay()->toDateString();

        // Calcular totales del día
        $dailyIncome = Income::where('league_id', $league->id)
            ->whereDate('confirmed_at', $yesterday)
            ->where('payment_status', 'confirmed')
            ->sum('amount');

        $dailyExpenses = Expense::where('league_id', $league->id)
            ->whereDate('confirmed_at', $yesterday)
            ->where('payment_status', 'confirmed')
            ->sum('amount');

        $netFlow = $dailyIncome - $dailyExpenses;

        // Verificar discrepancias
        $discrepancies = $this->findDiscrepancies($league, $yesterday);

        // Generar reporte
        ReconciliationReport::create([
            'league_id' => $league->id,
            'report_date' => $yesterday,
            'total_income' => $dailyIncome,
            'total_expenses' => $dailyExpenses,
            'net_flow' => $netFlow,
            'discrepancies_count' => count($discrepancies),
            'discrepancies_detail' => $discrepancies,
            'status' => count($discrepancies) > 0 ? 'requires_review' : 'balanced',
        ]);

        // Notificar si hay discrepancias
        if (count($discrepancies) > 0) {
            $league->admin->user->notify(
                new \App\Notifications\ReconciliationDiscrepancies($league, $discrepancies)
            );
        }
    }

    private function findDiscrepancies(League $league, string $date): array
    {
        $discrepancies = [];

        // Buscar pagos pendientes vencidos
        $overduePayments = Income::where('league_id', $league->id)
            ->where('payment_status', 'pending')
            ->where('due_date', '<', $date)
            ->get();

        foreach ($overduePayments as $payment) {
            $discrepancies[] = [
                'type' => 'overdue_payment',
                'income_id' => $payment->id,
                'team_id' => $payment->team_id,
                'amount' => $payment->amount,
                'days_overdue' => now()->diffInDays($payment->due_date),
            ];
        }

        // Buscar gastos no confirmados
        $unconfirmedExpenses = Expense::where('league_id', $league->id)
            ->where('payment_status', 'paid')
            ->whereDate('paid_at', '<=', $date)
            ->get();

        foreach ($unconfirmedExpenses as $expense) {
            $discrepancies[] = [
                'type' => 'unconfirmed_expense',
                'expense_id' => $expense->id,
                'amount' => $expense->amount,
                'days_pending' => now()->diffInDays($expense->paid_at),
            ];
        }

        return $discrepancies;
    }
}
```

---

## 🚀 Próximos Pasos - Parte 3

En la **Parte 3** cubriremos:

1. **📊 Sistema de Reportes Financieros**
2. **📈 Analytics y Métricas en Tiempo Real**
3. **📱 Dashboard Financiero Interactivo**
4. **📋 Exportación de Datos**
5. **🔍 Auditoría y Logs Detallados**

---

*¡El motor de transacciones está listo para procesar todos los flujos financieros automáticamente!* 💰⚡