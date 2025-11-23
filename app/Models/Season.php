<?php

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
        'game_days' => 'array', // ['monday', 'wednesday', 'friday']
        'match_times' => 'array', // ['18:00', '20:00']
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

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    // Métodos de negocio
    public function getTeamCount(): int
    {
        return $this->teams()->count();
    }

    public function generateRounds(): void
    {
        // Validar que hay suficientes equipos
        $teams = $this->teams()->get();
        $teamCount = $teams->count();
        
        if ($teamCount < 2) {
            throw new \Exception('Se necesitan al menos 2 equipos para generar rondas.');
        }

        // Limpiar rondas existentes si las hay
        $this->rounds()->delete();

        // Generar el fixture usando algoritmo Round Robin
        $fixtures = $this->generateRoundRobinFixtures($teams->toArray());
        
        // Crear las rondas en la base de datos
        foreach ($fixtures as $roundNumber => $matches) {
            $round = $this->rounds()->create([
                'round_number' => $roundNumber,
                'start_date' => $this->calculateRoundStartDate($roundNumber),
                'end_date' => $this->calculateRoundEndDate($roundNumber),
            ]);

            // Crear los partidos para esta ronda
            foreach ($matches as $match) {
                if ($match['home'] && $match['away']) { // Skip bye matches
                    $round->matches()->create([
                        'home_team_id' => $match['home']['id'],
                        'away_team_id' => $match['away']['id'],
                        'scheduled_at' => $this->calculateMatchDateTime($roundNumber, $match['match_order'] ?? 0),
                        'status' => 'scheduled',
                    ]);
                }
            }
        }
    }

    private function generateRoundRobinFixtures(array $teams): array
    {
        $teamCount = count($teams);
        
        // Si es número impar, agregar un "bye" (descanso)
        if ($teamCount % 2 !== 0) {
            $teams[] = null; // Bye team
            $teamCount++;
        }

        $fixtures = [];
        $rounds = $teamCount - 1;

        // Algoritmo Round Robin estándar
        for ($round = 1; $round <= $rounds; $round++) {
            $roundMatches = [];
            
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $home = $teams[$i];
                $away = $teams[$teamCount - 1 - $i];
                
                // Solo agregar si no hay bye
                if ($home !== null && $away !== null) {
                    $roundMatches[] = [
                        'home' => $home,
                        'away' => $away,
                        'match_order' => $i
                    ];
                }
            }
            
            $fixtures[$round] = $roundMatches;
            
            // Rotar equipos (excepto el primero que se mantiene fijo)
            $this->rotateTeams($teams);
        }

        // Si es doble vuelta, agregar la vuelta
        if ($this->round_robin_type === 'double') {
            $secondLeg = [];
            foreach ($fixtures as $roundNum => $matches) {
                $secondLegMatches = [];
                foreach ($matches as $match) {
                    $secondLegMatches[] = [
                        'home' => $match['away'], // Intercambiar local y visitante
                        'away' => $match['home'],
                        'match_order' => $match['match_order']
                    ];
                }
                $secondLeg[$roundNum + $rounds] = $secondLegMatches;
            }
            $fixtures = array_merge($fixtures, $secondLeg);
        }

        return $fixtures;
    }

    private function rotateTeams(array &$teams): void
    {
        // Mantener el primer equipo fijo, rotar los demás
        $first = array_shift($teams);
        $last = array_pop($teams);
        array_unshift($teams, $first, $last);
    }

    private function calculateRoundStartDate(int $roundNumber): \DateTime
    {
        $startDate = clone $this->start_date;
        $weeksToAdd = ($roundNumber - 1);
        
        return $startDate->modify("+{$weeksToAdd} week");
    }

    private function calculateRoundEndDate(int $roundNumber): \DateTime
    {
        $startDate = $this->calculateRoundStartDate($roundNumber);
        
        // La ronda termina una semana después de comenzar
        return (clone $startDate)->modify('+6 days');
    }

    private function calculateMatchDateTime(int $roundNumber, int $matchOrder): \DateTime
    {
        $roundStartDate = $this->calculateRoundStartDate($roundNumber);
        
        // Obtener días de juego configurados
        $gameDays = $this->game_days ?? ['saturday'];
        $matchTimes = $this->match_times ?? ['15:00'];
        
        // Calcular el día de la semana para este partido
        $dayIndex = $matchOrder % count($gameDays);
        $timeIndex = intval($matchOrder / count($gameDays)) % count($matchTimes);
        
        $gameDay = $gameDays[$dayIndex];
        $matchTime = $matchTimes[$timeIndex];
        
        // Encontrar el próximo día de juego
        $daysOfWeek = [
            'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4,
            'friday' => 5, 'saturday' => 6, 'sunday' => 0
        ];
        
        $targetDay = $daysOfWeek[$gameDay];
        $currentDay = $roundStartDate->format('w');
        
        $daysToAdd = ($targetDay - $currentDay + 7) % 7;
        
        $matchDate = (clone $roundStartDate)->modify("+{$daysToAdd} days");
        $matchDateTime = \DateTime::createFromFormat('Y-m-d H:i', $matchDate->format('Y-m-d') . ' ' . $matchTime);
        
        return $matchDateTime;
    }

    public function updateStandings(): void
    {
        // TODO: Implementar actualización de tabla de posiciones
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
