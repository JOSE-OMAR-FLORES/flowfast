<?php

namespace App\Services;

use App\Models\Standing;
use App\Models\Season;
use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StandingsService
{
    /**
     * Recalcular standings completos para una temporada
     * Se usa cuando se inicia una temporada o cuando hay cambios mayores
     */
    public function recalculateStandings(Season $season): void
    {
        try {
            DB::beginTransaction();

            // 1. Limpiar standings existentes
            Standing::where('season_id', $season->id)->delete();

            // 2. Obtener todos los equipos de la temporada
            $teams = $season->teams;

            // 3. Crear un standing inicial para cada equipo
            foreach ($teams as $team) {
                Standing::create([
                    'season_id' => $season->id,
                    'team_id' => $team->id,
                    'played' => 0,
                    'won' => 0,
                    'drawn' => 0,
                    'lost' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_difference' => 0,
                    'points' => 0,
                    'form' => '',
                ]);
            }

            // 4. Procesar todos los partidos completados
            $completedFixtures = Fixture::where('season_id', $season->id)
                ->where('status', 'completed')
                ->get();

            foreach ($completedFixtures as $fixture) {
                $this->updateStandingsForFixture($fixture);
            }

            // 5. Actualizar posiciones
            $this->updatePositions($season);

            DB::commit();

            Log::info("Standings recalculated for season {$season->id}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error recalculating standings: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Actualizar standings cuando un partido se completa
     */
    public function updateStandingsForFixture(Fixture $fixture): void
    {
        if ($fixture->status !== 'completed') {
            Log::warning("Attempting to update standings for non-completed fixture {$fixture->id}");
            return;
        }

        if (is_null($fixture->home_score) || is_null($fixture->away_score)) {
            Log::warning("Fixture {$fixture->id} completed but scores are null");
            return;
        }

        try {
            DB::beginTransaction();

            // Obtener o crear standings para ambos equipos
            $homeStanding = Standing::firstOrCreate(
                [
                    'season_id' => $fixture->season_id,
                    'team_id' => $fixture->home_team_id,
                ],
                $this->getDefaultStandingData($fixture->season_id, $fixture->home_team_id)
            );

            $awayStanding = Standing::firstOrCreate(
                [
                    'season_id' => $fixture->season_id,
                    'team_id' => $fixture->away_team_id,
                ],
                $this->getDefaultStandingData($fixture->season_id, $fixture->away_team_id)
            );

            // Determinar resultado
            $homeScore = $fixture->home_score;
            $awayScore = $fixture->away_score;

            // Actualizar estadísticas del equipo local
            $homeStanding->played++;
            $homeStanding->goals_for += $homeScore;
            $homeStanding->goals_against += $awayScore;
            $homeStanding->goal_difference = $homeStanding->goals_for - $homeStanding->goals_against;

            // Actualizar estadísticas del equipo visitante
            $awayStanding->played++;
            $awayStanding->goals_for += $awayScore;
            $awayStanding->goals_against += $homeScore;
            $awayStanding->goal_difference = $awayStanding->goals_for - $awayStanding->goals_against;

            // Determinar ganador y actualizar W/D/L y puntos
            if ($homeScore > $awayScore) {
                // Victoria local
                $homeStanding->won++;
                $homeStanding->points += 3;
                $awayStanding->lost++;
                
                $this->updateForm($homeStanding, 'W');
                $this->updateForm($awayStanding, 'L');
            } elseif ($homeScore < $awayScore) {
                // Victoria visitante
                $awayStanding->won++;
                $awayStanding->points += 3;
                $homeStanding->lost++;
                
                $this->updateForm($homeStanding, 'L');
                $this->updateForm($awayStanding, 'W');
            } else {
                // Empate
                $homeStanding->drawn++;
                $homeStanding->points++;
                $awayStanding->drawn++;
                $awayStanding->points++;
                
                $this->updateForm($homeStanding, 'D');
                $this->updateForm($awayStanding, 'D');
            }

            $homeStanding->save();
            $awayStanding->save();

            // Actualizar posiciones de toda la temporada
            $this->updatePositions($fixture->season);

            DB::commit();

            Log::info("Standings updated for fixture {$fixture->id}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating standings for fixture {$fixture->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Actualizar posiciones de todos los equipos de una temporada
     */
    private function updatePositions(Season $season): void
    {
        $standings = Standing::where('season_id', $season->id)
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->orderBy('id', 'asc') // Para mantener consistencia
            ->get();

        $position = 1;
        foreach ($standings as $standing) {
            $standing->position = $position;
            $standing->save();
            $position++;
        }
    }

    /**
     * Actualizar racha de resultados (últimos 5 partidos)
     */
    private function updateForm(Standing $standing, string $result): void
    {
        $form = $standing->form ?? '';
        
        // Agregar nuevo resultado al final
        $form .= $result;
        
        // Mantener solo los últimos 5 resultados
        if (strlen($form) > 5) {
            $form = substr($form, -5);
        }
        
        $standing->form = $form;
    }

    /**
     * Obtener datos por defecto para un nuevo standing
     */
    private function getDefaultStandingData(int $seasonId, int $teamId): array
    {
        return [
            'season_id' => $seasonId,
            'team_id' => $teamId,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'form' => '',
        ];
    }

    /**
     * Obtener standings ordenados de una temporada
     */
    public function getStandings(Season $season)
    {
        // Cargar temporada y coach del equipo en standings
        return Standing::with(['team.season', 'team.coach'])
            ->where('season_id', $season->id)
            ->ordered()
            ->get();
    }

    /**
     * Verificar si hay standings creados para una temporada
     */
    public function hasStandings(Season $season): bool
    {
        return Standing::where('season_id', $season->id)->exists();
    }

    /**
     * Inicializar standings para una temporada nueva
     */
    public function initializeStandings(Season $season): void
    {
        if ($this->hasStandings($season)) {
            Log::info("Standings already initialized for season {$season->id}");
            return;
        }

        $this->recalculateStandings($season);
    }
}
