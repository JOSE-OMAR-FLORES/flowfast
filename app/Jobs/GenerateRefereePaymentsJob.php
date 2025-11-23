<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Fixture;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateRefereePaymentsJob implements ShouldQueue
{
    use Queueable;

    protected $fixture;

    /**
     * Create a new job instance.
     */
    public function __construct(Fixture $fixture)
    {
        $this->fixture = $fixture;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Verificar si el partido tiene árbitro asignado
            if (!$this->fixture->referee_id) {
                Log::info("No referee assigned to fixture {$this->fixture->id}");
                return;
            }

            // Verificar si ya existe el pago generado
            $existingPayment = Expense::where('fixture_id', $this->fixture->id)
                ->where('expense_type', 'referee_payment')
                ->where('beneficiary_id', $this->fixture->referee_id)
                ->exists();

            if ($existingPayment) {
                Log::info("Referee payment already exists for fixture {$this->fixture->id}");
                return;
            }

            // Obtener el árbitro
            $referee = User::find($this->fixture->referee_id);
            if (!$referee) {
                Log::warning("Referee not found for fixture {$this->fixture->id}");
                return;
            }

            // Obtener la configuración de pago por partido de la liga
            $league = $this->fixture->season->league;
            $refereePayment = $league->referee_payment ?? 30.00; // Valor por defecto

            // Crear el gasto de pago al árbitro
            Expense::create([
                'league_id' => $league->id,
                'season_id' => $this->fixture->season_id,
                'fixture_id' => $this->fixture->id,
                'beneficiary_id' => $referee->id,
                'expense_type' => 'referee_payment',
                'amount' => $refereePayment,
                'description' => "Pago a árbitro {$referee->name} - {$this->fixture->homeTeam->name} vs {$this->fixture->awayTeam->name}",
                'due_date' => Carbon::parse($this->fixture->match_date)->addDays(7), // Pagar en 7 días
                'approval_status' => 'pending',
                'payment_status' => 'pending',
                'created_by' => 1, // Sistema
                'notes' => 'Generado automáticamente después del partido'
            ]);

            Log::info("Referee payment generated successfully for fixture {$this->fixture->id}");

        } catch (\Exception $e) {
            Log::error("Error generating referee payment for fixture {$this->fixture->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
