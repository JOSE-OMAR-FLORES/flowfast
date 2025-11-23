<?php

namespace App\Livewire\Fixtures;

use Livewire\Component; 
use App\Models\Season; 
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Generate extends Component
{
    public $season_id;
    public $venue_id;
    public $start_date;
    public $use_round_robin = true;
    public $double_round = false;
    public $selectedLeague;
    
    public $seasons = [];
    public $venues = [];
    public $preview = [];
    public $totalMatches = 0;
    public $totalRounds = 0;

    protected function rules()
    {
        return [
            'season_id' => 'required|exists:seasons,id',
            'venue_id' => 'nullable|exists:venues,id',
            'start_date' => 'required|date',
            'use_round_robin' => 'boolean',
            'double_round' => 'boolean',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        
        if ($user->user_type === 'league_manager') {
            $this->seasons = Season::whereHas('league', function($q) use ($user) {
                $q->where('league_manager_id', $user->id);
            })->get();
        } else {
            $this->seasons = Season::all();
        }

        if ($this->seasons->isNotEmpty()) {
            $this->season_id = $this->seasons->first()->id;
            $this->updatedSeasonId();
        }
    }

    public function updatedSelectedLeague()
    {
        if ($this->selectedLeague) {
            $this->seasons = Season::where('league_id', $this->selectedLeague)->get();
            $this->season_id = $this->seasons->first()->id ?? null;
            $this->updatedSeasonId();
        }
    }

    public function updatedSeasonId()
    {
        if ($this->season_id) {
            $season = Season::find($this->season_id);
            
            if ($season) {
                $this->venues = Venue::where('league_id', $season->league_id)
                    ->where('is_active', true)
                    ->get();
                
                if ($this->venues->isNotEmpty() && !$this->venue_id) {
                    $this->venue_id = $this->venues->first()->id;
                }

                // Siempre usar la fecha de inicio de la temporada
                $this->start_date = $season->start_date?->format('Y-m-d') ?? now()->format('Y-m-d');
            }
        }
    }

    public function generatePreview()
    {
        $this->validate();

        $season = Season::with('league')->findOrFail($this->season_id);
        $teams = Team::where('season_id', $this->season_id)->get();

        if ($teams->count() < 2) {
            session()->flash('error', 'Se necesitan al menos 2 equipos para generar fixtures.');
            return;
        }

        $this->preview = $this->generateRoundRobinFixtures($teams, $season);
        
        // Contar correctamente: totalRounds = número de días/jornadas, totalMatches = total de partidos
        $this->totalRounds = count($this->preview); // Número de jornadas (días de juego)
        $this->totalMatches = 0;
        foreach ($this->preview as $round) {
            $this->totalMatches += count($round); // Sumar partidos de cada jornada
        }
        
        if ($this->double_round) {
            $originalRounds = $this->totalRounds;
            $originalMatches = $this->totalMatches;
            $this->totalRounds = $originalRounds * 2;
            $this->totalMatches = $originalMatches * 2;
        }

        session()->flash('preview', 'Vista previa generada. Revisa los partidos antes de confirmar.');
    }

    private function generateRoundRobinFixtures($teams, $season)
    {
        $teamsList = $teams->toArray();
        $numTeams = count($teamsList);
        
        // Si hay número impar de equipos, agregar un "bye" (descanso)
        if ($numTeams % 2 !== 0) {
            $teamsList[] = ['id' => null, 'name' => 'BYE', 'slug' => 'bye'];
            $numTeams++;
        }

        $allMatches = [];
        $numRounds = $numTeams - 1;
        $matchesPerRound = $numTeams / 2;

        // Algoritmo Round Robin - Generar todos los partidos primero
        for ($round = 0; $round < $numRounds; $round++) {
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = ($round + $match) % ($numTeams - 1);
                $away = ($numTeams - 1 - $match + $round) % ($numTeams - 1);
                
                // El último equipo se queda fijo
                if ($match == 0) {
                    $away = $numTeams - 1;
                }
                
                $homeTeam = $teamsList[$home];
                $awayTeam = $teamsList[$away];
                
                // Saltar si algún equipo es "BYE"
                if ($homeTeam['id'] === null || $awayTeam['id'] === null) {
                    continue;
                }
                
                $allMatches[] = [
                    'home_team' => $homeTeam,
                    'away_team' => $awayTeam,
                ];
            }
        }
        
        // Ahora reorganizar los partidos en jornadas según daily_matches y game_days
        $dailyMatches = $season->daily_matches ?? 2;
        $gameDays = is_array($season->game_days) ? $season->game_days : [0];
        $gameDays = $this->convertGameDaysToNumbers($gameDays);
        $gameDays = array_map('intval', $gameDays);
         
        // Calcular cuántos partidos caben en una jornada completa
        $matchesPerRound = count($gameDays) * $dailyMatches; // días de juego × partidos por día
        
        $rounds = [];
        $matchIndex = 0;
        $dayIndexInRound = 0;
        $currentRoundNumber = 1;
        
        foreach ($allMatches as $match) {
            // Determinar en qué jornada estamos
            $roundNumber = floor($matchIndex / $matchesPerRound) + 1;
            
            // Si cambiamos de jornada, reiniciar el índice de días
            if ($roundNumber != $currentRoundNumber) {
                $currentRoundNumber = $roundNumber;
                $dayIndexInRound = 0;
            }
            
            // Determinar en qué día de la jornada estamos (dentro de los días configurados)
            $matchPositionInRound = $matchIndex % $matchesPerRound;
            $dayIndexInRound = floor($matchPositionInRound / $dailyMatches);
            
            // Número de partido dentro del día (1, 2, 3...)
            $matchNumberInDay = ($matchPositionInRound % $dailyMatches) + 1;
            
            // Calcular el índice global de día para calculateMatchDate
            $globalDayIndex = (($roundNumber - 1) * count($gameDays)) + $dayIndexInRound;
            
            if (!isset($rounds[$roundNumber - 1])) {
                $rounds[$roundNumber - 1] = [];
            }
            
            $rounds[$roundNumber - 1][] = [
                'round' => $roundNumber,
                'match' => $matchNumberInDay,
                'home_team' => $match['home_team'],
                'away_team' => $match['away_team'],
                'date' => $this->calculateMatchDate($globalDayIndex, $season),
            ];
            
            $matchIndex++;
        }

        // Si es doble ronda, agregar partidos de vuelta
        if ($this->double_round) {
            $secondRounds = [];
            foreach ($rounds as $roundIndex => $roundMatches) {
                $secondRoundMatches = [];
                $matchNumber = 1; // Reiniciar numeración para la segunda vuelta
                foreach ($roundMatches as $match) {
                    $secondRoundMatches[] = [
                        'round' => $roundIndex + $numRounds + 1,
                        'match' => $matchNumber,
                        'home_team' => $match['away_team'], // Invertir local/visitante
                        'away_team' => $match['home_team'],
                        'date' => $this->calculateMatchDate($roundIndex + $numRounds, $season),
                    ];
                    $matchNumber++;
                }
                $secondRounds[] = $secondRoundMatches;
            }
            $rounds = array_merge($rounds, $secondRounds);
        }

        return $rounds;
    }

    private function calculateMatchDate($dayIndex, $season)
    {
        $startDate = \Carbon\Carbon::parse($this->start_date);
        
        // game_days ya es un array gracias al cast en el modelo Season
        $gameDays = is_array($season->game_days) ? $season->game_days : [0]; // 0 = Domingo por defecto
        
        // Convertir días de palabras a números si es necesario
        $gameDays = $this->convertGameDaysToNumbers($gameDays);
        
        // Convertir todos los valores a enteros para evitar problemas con strings
        $gameDays = array_map('intval', $gameDays);
        
        // Obtener el día de la semana de la fecha de inicio (0=Domingo, 6=Sábado)
        $startDayOfWeek = $startDate->dayOfWeek;
        
        // Si la fecha de inicio ya es uno de los días configurados, empezar ahí
        if (in_array($startDayOfWeek, $gameDays)) {
            $firstGameDay = $startDate->copy();
        } else {
            // Si no, encontrar el próximo día de juego
            $firstGameDay = $startDate->copy();
            while (!in_array($firstGameDay->dayOfWeek, $gameDays)) {
                $firstGameDay->addDay();
            }
        }
        
        // Si dayIndex es 0, retornar el primer día de juego
        if ($dayIndex == 0) {
            return $firstGameDay->format('Y-m-d');
        }
        
        // Para dayIndex > 0, encontrar el siguiente día de juego
        $currentDate = $firstGameDay->copy();
        $daysFound = 0;
        
        while ($daysFound < $dayIndex) {
            $currentDate->addDay();
            if (in_array($currentDate->dayOfWeek, $gameDays)) {
                $daysFound++;
            }
        }
        
        return $currentDate->format('Y-m-d');
    }

    /**
     * Convierte nombres de días a números (0=Sunday, 1=Monday, ..., 6=Saturday)
     */
    private function convertGameDaysToNumbers($gameDays)
    {
        $dayMap = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
        ];

        return array_map(function($day) use ($dayMap) {
            // Si ya es un número, devolverlo
            if (is_numeric($day)) {
                return (int) $day;
            }
            // Si es una palabra, convertirla
            $dayLower = strtolower($day);
            return $dayMap[$dayLower] ?? 0; // Default Domingo si no se encuentra
        }, $gameDays);
    }

    public function confirmGeneration()
    {
        $this->validate();

        if (empty($this->preview)) {
            session()->flash('error', 'Primero genera una vista previa de los fixtures.');
            return;
        }

        try {
            DB::beginTransaction();

            $season = Season::findOrFail($this->season_id);
            
            // match_times ya es un array gracias al cast en el modelo Season
            $matchTimes = is_array($season->match_times) ? $season->match_times : ['14:00'];
            
            $lastMatchDate = null;
            
            foreach ($this->preview as $round) {
                foreach ($round as $matchIndex => $match) {
                    // Alternar horarios si hay múltiples horarios configurados
                    $timeIndex = $matchIndex % count($matchTimes);
                    $matchTime = $matchTimes[$timeIndex];

                    Fixture::create([
                        'season_id' => $this->season_id,
                        'home_team_id' => $match['home_team']['id'],
                        'away_team_id' => $match['away_team']['id'],
                        'venue_id' => $this->venue_id,
                        'round_number' => $match['round'],
                        'match_number' => $match['match'],
                        'match_date' => $match['date'],
                        'match_time' => $matchTime,
                        'status' => 'scheduled',
                    ]);
                    
                    // Guardar la fecha del último partido
                    if (!$lastMatchDate || $match['date'] > $lastMatchDate) {
                        $lastMatchDate = $match['date'];
                    }
                }
            }
            
            // Actualizar la fecha de fin de la temporada automáticamente
            if ($lastMatchDate && !$season->end_date) {
                $season->update(['end_date' => $lastMatchDate]);
            }

            DB::commit();

            session()->flash('success', "{$this->totalMatches} fixtures generados exitosamente. Fecha de fin actualizada: {$lastMatchDate}");
            
            return redirect()->route('fixtures.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al generar fixtures: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        $leagues = [];
        if ($user->user_type === 'league_manager') {
            $leagues = \App\Models\League::where('league_manager_id', $user->id)->get();
        } elseif ($user->user_type === 'admin') {
            $leagues = \App\Models\League::all();
        }

        return view('livewire.fixtures.generate', [
            'leagues' => $leagues,
        ])->layout('layouts.app');
    }
}
