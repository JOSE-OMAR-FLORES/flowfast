<?php

namespace App\Observers;

use App\Models\Fixture;
use App\Models\Income;
use App\Models\Expense;
use App\Services\StandingsService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FixtureObserver
{
    protected $standingsService;

    public function __construct(StandingsService $standingsService)
    {
        $this->standingsService = $standingsService;
    }

    /**
     * Handle the Fixture "created" event.
     */
    public function created(Fixture $fixture): void
    {
        //
    }

    /**
     * Handle the Fixture "updated" event.
     */
    public function updated(Fixture $fixture): void
    {
        // Detectar si el partido cambió a estado "completed"
        if ($fixture->isDirty('status') && $fixture->status === 'completed') {
            Log::info("Fixture {$fixture->id} completed, generating financial transactions and updating standings");
            
            // 1. Generar ingresos (cuotas por partido) - INMEDIATO
            $this->generateMatchFees($fixture);
            
            // 2. Generar egresos (pago al árbitro) - INMEDIATO
            if ($fixture->referee_id) {
                $this->generateRefereePayment($fixture);
            }
            
            // 3. Actualizar standings inmediatamente
            try {
                $this->standingsService->updateStandingsForFixture($fixture);
                Log::info("Standings updated successfully for fixture {$fixture->id}");
            } catch (\Exception $e) {
                Log::error("Failed to update standings for fixture {$fixture->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Generar cuotas por partido (ingresos)
     */
    protected function generateMatchFees(Fixture $fixture): void
    {
        try {
            // Verificar si ya existen cuotas generadas
            $existingFee = Income::where('fixture_id', $fixture->id)
                ->where('income_type', 'match_fee')
                ->exists();

            if ($existingFee) {
                Log::info("Match fees already exist for fixture {$fixture->id}");
                return;
            }

            // Obtener la liga y su configuración
            $league = $fixture->season->league;
            $matchFee = $league->match_fee ?? 50.00;

            // Generar cuotas para ambos equipos
            $teams = [
                ['team' => $fixture->homeTeam, 'type' => 'Local'],
                ['team' => $fixture->awayTeam, 'type' => 'Visitante']
            ];

            foreach ($teams as $teamData) {
                Income::create([
                    'league_id' => $league->id,
                    'season_id' => $fixture->season_id,
                    'team_id' => $teamData['team']->id,
                    'fixture_id' => $fixture->id,
                    'income_type' => 'match_fee',
                    'amount' => $matchFee,
                    'description' => "Cuota por partido - {$teamData['type']} - {$fixture->homeTeam->name} vs {$fixture->awayTeam->name}",
                    'due_date' => Carbon::parse($fixture->match_date)->addDays(3),
                    'payment_status' => 'pending',
                    'created_by' => auth()->id() ?? 1,
                    'notes' => 'Generado automáticamente al completar el partido'
                ]);
            }

            Log::info("Match fees generated successfully for fixture {$fixture->id}");

        } catch (\Exception $e) {
            Log::error("Error generating match fees for fixture {$fixture->id}: " . $e->getMessage());
        }
    }

    /**
     * Generar pago al árbitro (egreso)
     */
    protected function generateRefereePayment(Fixture $fixture): void
    {
        try {
            // Verificar si ya existe el pago generado
            $existingPayment = Expense::where('fixture_id', $fixture->id)
                ->where('expense_type', 'referee_payment')
                ->where('beneficiary_id', $fixture->referee_id)
                ->exists();

            if ($existingPayment) {
                Log::info("Referee payment already exists for fixture {$fixture->id}");
                return;
            }

            // Obtener el árbitro
            $referee = $fixture->referee;
            if (!$referee) {
                Log::warning("Referee not found for fixture {$fixture->id}");
                return;
            }

            // Obtener la liga y su configuración
            $league = $fixture->season->league;
            $refereePayment = $league->referee_payment ?? 30.00;

            // Crear el gasto de pago al árbitro
            Expense::create([
                'league_id' => $league->id,
                'season_id' => $fixture->season_id,
                'fixture_id' => $fixture->id,
                'beneficiary_id' => $referee->id,
                'expense_type' => 'referee_payment',
                'amount' => $refereePayment,
                'description' => "Pago a árbitro - {$fixture->homeTeam->name} vs {$fixture->awayTeam->name}",
                'due_date' => Carbon::parse($fixture->match_date)->addDays(7),
                'approval_status' => 'pending',
                'payment_status' => 'pending',
                'created_by' => auth()->id() ?? 1,
                'notes' => 'Generado automáticamente al completar el partido'
            ]);

            Log::info("Referee payment generated successfully for fixture {$fixture->id}");

        } catch (\Exception $e) {
            Log::error("Error generating referee payment for fixture {$fixture->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Fixture "deleted" event.
     */
    public function deleted(Fixture $fixture): void
    {
        //
    }

    /**
     * Handle the Fixture "restored" event.
     */
    public function restored(Fixture $fixture): void
    {
        //
    }

    /**
     * Handle the Fixture "force deleted" event.
     */
    public function forceDeleted(Fixture $fixture): void
    {
        //
    }
}
