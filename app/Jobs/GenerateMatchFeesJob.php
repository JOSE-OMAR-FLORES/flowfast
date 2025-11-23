<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Fixture;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateMatchFeesJob implements ShouldQueue
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
            // Verificar si el partido ya tiene cuota generada
            $existingFee = Income::where('fixture_id', $this->fixture->id)
                ->where('income_type', 'match_fee')
                ->exists();

            if ($existingFee) {
                Log::info("Match fee already exists for fixture {$this->fixture->id}");
                return;
            }

            // Obtener la configuración de cuota por partido de la liga
            $league = $this->fixture->season->league;
            $matchFee = $league->match_fee ?? 50.00; // Valor por defecto si no está configurado

            // Generar cuotas para ambos equipos
            $teams = [
                ['team' => $this->fixture->homeTeam, 'type' => 'Local'],
                ['team' => $this->fixture->awayTeam, 'type' => 'Visitante']
            ];

            foreach ($teams as $teamData) {
                Income::create([
                    'league_id' => $league->id,
                    'season_id' => $this->fixture->season_id,
                    'team_id' => $teamData['team']->id,
                    'fixture_id' => $this->fixture->id,
                    'income_type' => 'match_fee',
                    'amount' => $matchFee,
                    'description' => "Cuota por partido - {$teamData['type']} - {$this->fixture->homeTeam->name} vs {$this->fixture->awayTeam->name}",
                    'due_date' => Carbon::parse($this->fixture->match_date)->addDays(3), // 3 días después del partido
                    'payment_status' => 'pending',
                    'created_by' => 1, // Sistema
                    'notes' => 'Generado automáticamente después del partido'
                ]);
            }

            Log::info("Match fees generated successfully for fixture {$this->fixture->id}");

        } catch (\Exception $e) {
            Log::error("Error generating match fees for fixture {$this->fixture->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
