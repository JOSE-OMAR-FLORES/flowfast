<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\GameMatch;
use App\Models\League;
use App\Models\Referee;
use App\Models\Season;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseService
{
    /**
     * Generar pago a árbitro automáticamente después de un partido
     */
    public function generateRefereePayment(GameMatch $match): ?Expense
    {
        if (!$match->referee_id) {
            Log::warning("No se puede generar pago a árbitro: Match #{$match->id} no tiene árbitro asignado");
            return null;
        }

        return DB::transaction(function () use ($match) {
            $league = $match->season->league;
            $referee = $match->referee;
            $refereePaymentAmount = $league->referee_payment ?? 50;

            // Verificar que no exista ya un egreso para este partido y árbitro
            $existing = Expense::where('match_id', $match->id)
                ->where('referee_id', $referee->id)
                ->where('expense_type', 'referee_payment')
                ->first();

            if ($existing) {
                Log::warning("Ya existe egreso por partido para match #{$match->id} y árbitro #{$referee->id}");
                return $existing;
            }

            $expense = Expense::create([
                'league_id' => $league->id,
                'match_id' => $match->id,
                'referee_id' => $referee->id,
                'season_id' => $match->season_id,
                'expense_type' => 'referee_payment',
                'amount' => $refereePaymentAmount,
                'description' => "Pago a árbitro - {$referee->user->name} - {$match->homeTeam->name} vs {$match->awayTeam->name}",
                'due_date' => now()->addDays(7),
                'payment_status' => 'pending',
                'requested_by' => 1, // Sistema
                'beneficiary_user_id' => $referee->user_id,
                'metadata' => [
                    'match_id' => $match->id,
                    'referee_id' => $referee->id,
                    'auto_generated' => true,
                    'match_date' => $match->match_date,
                ],
            ]);

            Log::info("Pago a árbitro generado: Expense #{$expense->id} para árbitro {$referee->user->name}");

            return $expense;
        });
    }

    /**
     * Crear un egreso manual
     */
    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expense = Expense::create([
                'league_id' => $data['league_id'],
                'match_id' => $data['match_id'] ?? null,
                'referee_id' => $data['referee_id'] ?? null,
                'season_id' => $data['season_id'] ?? null,
                'expense_type' => $data['expense_type'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'due_date' => $data['due_date'] ?? now()->addDays(7),
                'payment_status' => 'pending',
                'requested_by' => auth()->id(),
                'beneficiary_user_id' => $data['beneficiary_user_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            Log::info("Egreso creado manualmente: Expense #{$expense->id} - {$data['description']}");

            return $expense;
        });
    }

    /**
     * Aprobar un egreso (Paso 1)
     */
    public function approveExpense(Expense $expense, ?string $notes = null): Expense
    {
        return DB::transaction(function () use ($expense, $notes) {
            $expense->approve(auth()->id(), $notes);

            Log::info("Egreso aprobado: Expense #{$expense->id}");

            return $expense->fresh();
        });
    }

    /**
     * Marcar egreso como pagado (Paso 2)
     */
    public function markAsPaid(Expense $expense, array $data): Expense
    {
        return DB::transaction(function () use ($expense, $data) {
            $expense->markAsReadyForPayment(
                auth()->id(),
                $data['payment_method'] ?? null,
                $data['payment_reference'] ?? null
            );

            if (isset($data['payment_proof_url'])) {
                $expense->update(['payment_proof_url' => $data['payment_proof_url']]);
            }

            Log::info("Egreso marcado como pagado: Expense #{$expense->id}");

            return $expense->fresh();
        });
    }

    /**
     * Confirmación por beneficiario (Paso 3)
     */
    public function confirmByBeneficiary(Expense $expense, ?string $notes = null): Expense
    {
        return DB::transaction(function () use ($expense, $notes) {
            $expense->confirmByBeneficiary(auth()->id(), $notes);

            Log::info("Egreso confirmado por beneficiario: Expense #{$expense->id}");

            return $expense->fresh();
        });
    }

    /**
     * Cancelar un egreso
     */
    public function cancelExpense(Expense $expense, ?string $reason = null): Expense
    {
        return DB::transaction(function () use ($expense, $reason) {
            $expense->cancel($reason);

            Log::info("Egreso cancelado: Expense #{$expense->id} - Razón: {$reason}");

            return $expense->fresh();
        });
    }

    /**
     * Obtener resumen financiero de egresos para una liga
     */
    public function getLeagueExpenseSummary(League $league, ?Season $season = null): array
    {
        $query = Expense::where('league_id', $league->id);

        if ($season) {
            $query->where('season_id', $season->id);
        }

        $totalConfirmed = (clone $query)->where('payment_status', 'confirmed')->sum('amount');
        $totalPending = (clone $query)->where('payment_status', 'pending')->sum('amount');
        $totalApproved = (clone $query)->where('payment_status', 'approved')->sum('amount');
        $totalReadyForPayment = (clone $query)->where('payment_status', 'ready_for_payment')->sum('amount');

        $byType = (clone $query)
            ->where('payment_status', 'confirmed')
            ->selectRaw('expense_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('expense_type')
            ->get();

        return [
            'total_confirmed' => $totalConfirmed,
            'total_pending' => $totalPending,
            'total_approved' => $totalApproved,
            'total_ready_for_payment' => $totalReadyForPayment,
            'by_type' => $byType,
            'total_expected' => $totalConfirmed + $totalPending + $totalApproved + $totalReadyForPayment,
        ];
    }

    /**
     * Generar egresos automáticamente para todos los partidos finalizados sin egreso
     */
    public function generateMissingRefereePayments(League $league): array
    {
        $matches = GameMatch::whereHas('season', function ($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->where('status', 'finished')
            ->whereNotNull('referee_id')
            ->whereDoesntHave('expenses', function ($query) {
                $query->where('expense_type', 'referee_payment');
            })
            ->get();

        $expenses = [];
        foreach ($matches as $match) {
            $expense = $this->generateRefereePayment($match);
            if ($expense) {
                $expenses[] = $expense;
            }
        }

        Log::info("Generados " . count($expenses) . " pagos a árbitros faltantes para liga #{$league->id}");

        return $expenses;
    }
}
