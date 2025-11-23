<?php

namespace App\Services;

use App\Models\Season;
use App\Models\Round;
use App\Models\GameMatch;
use App\Models\Team;
use Carbon\Carbon;

/**
 * Servicio para generar fixtures usando algoritmo Round Robin
 */
class RoundRobinService
{
    /**
     * Generar fixture completo para una temporada
     */
    public function generateFixture(Season $season): array
    {
        $teams = $season->teams()->get();
        $teamsCount = $teams->count();
        
        if ($teamsCount < 2) {
            throw new \Exception('Se necesitan al menos 2 equipos para generar un fixture');
        }

        // Si es número impar, agregar equipo "bye" (descanso)
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
     * Generar todas las rondas usando algoritmo Round Robin
     */
    private function generateRounds(array $teams, Season $season): array
    {
        $teamsCount = count($teams);
        $totalRounds = $teamsCount - 1;
        $rounds = [];

        // Convertir equipos a array indexado
        $teamList = array_values($teams);

        for ($roundNumber = 1; $roundNumber <= $totalRounds; $roundNumber++) {
            $roundMatches = [];

            // Generar parejas para esta ronda
            for ($i = 0; $i < $teamsCount / 2; $i++) {
                $team1Index = $i;
                $team2Index = $teamsCount - 1 - $i;

                $team1 = $teamList[$team1Index];
                $team2 = $teamList[$team2Index];

                // Solo crear partido si ninguno es BYE
                if ($team1['id'] !== null && $team2['id'] !== null) {
                    $roundMatches[] = [
                        'home_team_id' => $team1['id'],
                        'away_team_id' => $team2['id'],
                        'home_team_name' => $team1['name'],
                        'away_team_name' => $team2['name'],
                    ];
                }
            }

            $rounds[] = [
                'round_number' => $roundNumber,
                'matches' => $roundMatches,
                'matches_count' => count($roundMatches)
            ];

            // Rotar equipos (mantener el primer equipo fijo)
            $this->rotateTeams($teamList);
        }

        return $rounds;
    }

    /**
     * Rotar equipos para la siguiente ronda (algoritmo Round Robin estándar)
     */
    private function rotateTeams(array &$teams): void
    {
        $teamsCount = count($teams);
        
        // Mantener el primer equipo fijo, rotar los demás
        if ($teamsCount > 2) {
            $lastTeam = array_pop($teams); // Quitar último
            array_splice($teams, 1, 0, [$lastTeam]); // Insertar después del primero
        }
    }

    /**
     * Crear las rondas y partidos en la base de datos
     */
    public function createFixtureInDatabase(Season $season, array $fixtureData): void
    {
        $startDate = $season->start_date ? Carbon::parse($season->start_date) : now();
        $matchInterval = 7; // días entre jornadas

        foreach ($fixtureData['rounds'] as $roundIndex => $roundData) {
            // Crear ronda
            $round = Round::create([
                'season_id' => $season->id,
                'round_number' => $roundData['round_number'],
                'name' => "Jornada {$roundData['round_number']}",
                'start_date' => $startDate->copy()->addDays($roundIndex * $matchInterval),
                'end_date' => $startDate->copy()->addDays($roundIndex * $matchInterval + 6),
            ]);

            // Crear partidos de la ronda
            foreach ($roundData['matches'] as $matchIndex => $matchData) {
                GameMatch::create([
                    'season_id' => $season->id,
                    'round_id' => $round->id,
                    'home_team_id' => $matchData['home_team_id'],
                    'away_team_id' => $matchData['away_team_id'],
                    'scheduled_at' => $round->start_date->copy()->addDays($matchIndex)->setTime(10, 0),
                    'status' => 'scheduled',
                ]);
            }
        }
    }

    /**
     * Contar total de partidos
     */
    private function countMatches(array $rounds): int
    {
        return array_sum(array_column($rounds, 'matches_count'));
    }

    /**
     * Validar que se puede generar un fixture
     */
    public function validateFixtureGeneration(Season $season): array
    {
        $errors = [];

        // Verificar que la temporada existe
        if (!$season) {
            $errors[] = 'Temporada no encontrada';
            return $errors;
        }

        // Verificar que tiene equipos
        $teamsCount = $season->teams()->count();
        if ($teamsCount < 2) {
            $errors[] = 'Se necesitan al menos 2 equipos para generar un fixture';
        }

        // Verificar que no tenga fixture ya generado
        if ($season->rounds()->exists()) {
            $errors[] = 'Esta temporada ya tiene un fixture generado. Elimínelo primero si desea regenerarlo.';
        }

        // Verificar que la temporada no esté activa
        if ($season->status === 'active') {
            $errors[] = 'No se puede generar fixture para una temporada activa';
        }

        return $errors;
    }

    /**
     * Eliminar fixture existente
     */
    public function clearFixture(Season $season): void
    {
        // Eliminar partidos
        GameMatch::where('season_id', $season->id)->delete();
        
        // Eliminar rondas
        Round::where('season_id', $season->id)->delete();
    }
}