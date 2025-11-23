<?php

namespace App\Services;

use App\Models\Income;
use App\Models\Team;
use App\Models\GameMatch;
use App\Models\League;
use App\Models\Season;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeService
{
    /**
     * Generar cuota de inscripción para un equipo
     */
    public function generateRegistrationFee(Team $team, array $data): Income
    {
        return DB::transaction(function () use ($team, $data) {
            $league = $team->season->league;
            $amount = $data['amount'] ?? $league->registration_fee ?? 0;

            $income = Income::create([
                'league_id' => $league->id,
                'team_id' => $team->id,
                'season_id' => $team->season_id,
                'income_type' => 'registration_fee',
                'amount' => $amount,
                'description' => "Cuota de inscripción - {$team->name} - Temporada {$team->season->name}",
                'due_date' => $data['due_date'] ?? now()->addDays(7),
                'payment_status' => 'pending',
                'generated_by' => auth()->id(),
                'metadata' => [
                    'team_id' => $team->id,
                    'season_id' => $team->season_id,
                    'manual_generation' => true,
                    'notes' => $data['notes'] ?? null,
                ],
            ]);

            Log::info("Cuota de inscripción generada: Income #{$income->id} para equipo {$team->name}");

            return $income;
        });
    }

    /**
     * Generar pagos por partido (automático después de que termine el partido)
     */
    public function generateMatchFee(GameMatch $match): array
    {
        return DB::transaction(function () use ($match) {
            $incomes = [];
            $league = $match->season->league;
            $matchFeeAmount = $league->match_fee_per_team ?? 50;

            foreach ([$match->home_team_id, $match->away_team_id] as $teamId) {
                $team = Team::find($teamId);
                
                // Verificar que no exista ya un ingreso para este partido y equipo
                $existing = Income::where('match_id', $match->id)
                    ->where('team_id', $teamId)
                    ->where('income_type', 'match_fee')
                    ->first();

                if ($existing) {
                    Log::warning("Ya existe ingreso por partido para match #{$match->id} y equipo #{$teamId}");
                    continue;
                }

                $isHome = $teamId === $match->home_team_id;
                $opponent = $isHome ? $match->awayTeam : $match->homeTeam;

                $income = Income::create([
                    'league_id' => $league->id,
                    'team_id' => $teamId,
                    'match_id' => $match->id,
                    'season_id' => $match->season_id,
                    'income_type' => 'match_fee',
                    'amount' => $matchFeeAmount,
                    'description' => "Pago por partido - {$team->name} vs {$opponent->name}",
                    'due_date' => now()->addDays(3), // 3 días para pagar
                    'payment_status' => 'pending',
                    'generated_by' => 1, // Sistema
                    'metadata' => [
                        'match_id' => $match->id,
                        'auto_generated' => true,
                        'match_date' => $match->match_date,
                        'round_id' => $match->round_id,
                        'opponent_team_id' => $opponent->id,
                    ],
                ]);

                $incomes[] = $income;

                Log::info("Pago por partido generado: Income #{$income->id} para equipo {$team->name}");
            }

            return $incomes;
        });
    }

    /**
     * Generar multa para un equipo
     */
    public function generatePenaltyFee(Team $team, array $data): Income
    {
        return DB::transaction(function () use ($team, $data) {
            $league = $team->season->league;

            $income = Income::create([
                'league_id' => $league->id,
                'team_id' => $team->id,
                'season_id' => $team->season_id,
                'match_id' => $data['match_id'] ?? null,
                'income_type' => 'penalty_fee',
                'amount' => $data['amount'],
                'description' => $data['description'],
                'due_date' => $data['due_date'] ?? now()->addDays(5),
                'payment_status' => 'pending',
                'generated_by' => auth()->id(),
                'metadata' => [
                    'reason' => $data['reason'] ?? null,
                    'match_id' => $data['match_id'] ?? null,
                    'manual_generation' => true,
                ],
            ]);

            Log::info("Multa generada: Income #{$income->id} para equipo {$team->name} - {$data['description']}");

            return $income;
        });
    }

    /**
     * Generar recargo por pago tardío
     */
    public function generateLateFee(Income $originalIncome, float $percentage = 10): Income
    {
        return DB::transaction(function () use ($originalIncome, $percentage) {
            $lateFeeAmount = $originalIncome->amount * ($percentage / 100);

            $income = Income::create([
                'league_id' => $originalIncome->league_id,
                'team_id' => $originalIncome->team_id,
                'season_id' => $originalIncome->season_id,
                'income_type' => 'late_payment_fee',
                'amount' => $lateFeeAmount,
                'description' => "Recargo por pago tardío ({$percentage}%) - " . $originalIncome->description,
                'due_date' => now()->addDays(3),
                'payment_status' => 'pending',
                'generated_by' => 1, // Sistema
                'metadata' => [
                    'original_income_id' => $originalIncome->id,
                    'original_amount' => $originalIncome->amount,
                    'percentage' => $percentage,
                    'auto_generated' => true,
                ],
            ]);

            Log::info("Recargo por pago tardío generado: Income #{$income->id} sobre Income #{$originalIncome->id}");

            return $income;
        });
    }

    /**
     * Marcar ingresos vencidos como "overdue"
     */
    public function markOverdueIncomes(): int
    {
        $overdueCount = Income::where('payment_status', 'pending')
            ->where('due_date', '<', now())
            ->update(['payment_status' => 'overdue']);

        Log::info("Marcados {$overdueCount} ingresos como vencidos");

        return $overdueCount;
    }

    /**
     * Confirmar pago por parte del equipo (Paso 1)
     */
    public function confirmPaymentByTeam(Income $income, array $data): Income
    {
        return DB::transaction(function () use ($income, $data) {
            $income->markAsPaidByTeam(
                auth()->id(),
                $data['payment_method'] ?? null,
                $data['payment_reference'] ?? null,
                $data['payment_proof_url'] ?? null
            );

            Log::info("Pago confirmado por equipo: Income #{$income->id}");

            return $income->fresh();
        });
    }

    /**
     * Confirmar pago por parte del admin (Paso 2)
     */
    public function confirmPaymentByAdmin(Income $income, ?string $notes = null): Income
    {
        return DB::transaction(function () use ($income, $notes) {
            $income->confirmByAdmin(auth()->id(), $notes);

            Log::info("Pago confirmado por admin: Income #{$income->id}");

            return $income->fresh();
        });
    }

    /**
     * Confirmación final del sistema (Paso 3)
     */
    public function finalConfirmation(Income $income): Income
    {
        return DB::transaction(function () use ($income) {
            $income->finalConfirm(auth()->id());

            Log::info("Pago confirmado finalmente: Income #{$income->id}");

            return $income->fresh();
        });
    }

    /**
     * Cancelar un ingreso
     */
    public function cancelIncome(Income $income, ?string $reason = null): Income
    {
        return DB::transaction(function () use ($income, $reason) {
            $income->cancel($reason);

            Log::info("Ingreso cancelado: Income #{$income->id} - Razón: {$reason}");

            return $income->fresh();
        });
    }

    /**
     * Obtener resumen financiero de ingresos para una liga
     */
    public function getLeagueIncomeSummary(League $league, ?Season $season = null): array
    {
        $query = Income::where('league_id', $league->id);

        if ($season) {
            $query->where('season_id', $season->id);
        }

        $totalConfirmed = (clone $query)->where('payment_status', 'confirmed')->sum('amount');
        $totalPending = (clone $query)->where('payment_status', 'pending')->sum('amount');
        $totalOverdue = (clone $query)->where('payment_status', 'overdue')->sum('amount');

        $byType = (clone $query)
            ->where('payment_status', 'confirmed')
            ->selectRaw('income_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('income_type')
            ->get();

        return [
            'total_confirmed' => $totalConfirmed,
            'total_pending' => $totalPending,
            'total_overdue' => $totalOverdue,
            'by_type' => $byType,
            'total_expected' => $totalConfirmed + $totalPending + $totalOverdue,
        ];
    }
}
