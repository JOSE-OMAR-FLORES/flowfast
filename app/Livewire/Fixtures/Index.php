<?php

namespace App\Livewire\Fixtures;

use Livewire\Component;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\League;
use App\Models\Round;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $search = '';
    public $seasonFilter = '';
    public $leagueFilter = '';
    public $statusFilter = '';
    public $expandedLeagues = [];
    public $expandedRounds = [];

    public function toggleLeague($leagueId)
    {
        if (in_array($leagueId, $this->expandedLeagues)) {
            $this->expandedLeagues = array_diff($this->expandedLeagues, [$leagueId]);
        } else {
            $this->expandedLeagues[] = $leagueId;
        }
    }

    public function toggleRound($roundId)
    {
        if (in_array($roundId, $this->expandedRounds)) {
            $this->expandedRounds = array_diff($this->expandedRounds, [$roundId]);
        } else {
            $this->expandedRounds[] = $roundId;
        }
    }

    public function delete($id)
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'admin') {
            session()->flash('error', 'No tienes permiso para eliminar fixtures.');
            return;
        }

        $fixture = Fixture::findOrFail($id);
        
        if ($fixture->status === 'completed') {
            session()->flash('error', 'No se puede eliminar un partido que ya ha sido completado.');
            return;
        }

        $fixture->delete();
        session()->flash('success', 'Fixture eliminado exitosamente.');
    }

    public function deleteAllFixtures($seasonId)
    {
        $user = Auth::user();
        
        // Verificar permisos
        if ($user->user_type !== 'admin' && $user->user_type !== 'league_manager') {
            session()->flash('error', 'No tienes permiso para eliminar fixtures.');
            return;
        }

        $season = Season::findOrFail($seasonId);

        // Si es league_manager, verificar que sea de su liga
        if ($user->user_type === 'league_manager') {
            if ($season->league->league_manager_id !== $user->userable_id) {
                session()->flash('error', 'No tienes permiso para eliminar fixtures de esta liga.');
                return;
            }
        }

        // Contar fixtures
        $fixturesCount = $season->fixtures()->count();

        if ($fixturesCount === 0) {
            session()->flash('error', 'No hay fixtures para eliminar en esta temporada.');
            return;
        }

        // Eliminar todos los fixtures de la temporada
        $season->fixtures()->delete();

        session()->flash('success', "Se eliminaron exitosamente {$fixturesCount} fixture(s) de la temporada {$season->name}.");
    }

    public function render()
    {
        $user = Auth::user();
        
        // Obtener ligas que tienen fixtures (con sus temporadas y fixtures incluyendo venue)
        $leaguesQuery = League::with(['seasons.fixtures.homeTeam', 'seasons.fixtures.awayTeam', 'seasons.fixtures.venue'])
            ->whereHas('seasons.fixtures'); // Solo ligas con fixtures

        // Filtros de acceso según rol
        if ($user->user_type === 'league_manager') {
            $leaguesQuery->where('league_manager_id', $user->userable_id);
        }

        // Aplicar filtros
        if ($this->leagueFilter) {
            $leaguesQuery->where('id', $this->leagueFilter);
        }

        $leagues = $leaguesQuery->get();

        // Procesar ligas y agrupar fixtures por jornada
        $leagues = $leagues->map(function ($league) use ($user) {
            $league->seasons = $league->seasons->filter(function ($season) {
                if ($this->seasonFilter && $season->id != $this->seasonFilter) {
                    return false;
                }
                return true;
            })->map(function ($season) use ($user) {
                // Filtrar fixtures según los filtros aplicados
                $filteredFixtures = $season->fixtures->filter(function ($fixture) use ($user) {
                    // Si es referee, solo mostrar partidos asignados a él
                    if ($user->user_type === 'referee') {
                        // Verificar si está en la relación muchos-a-muchos (fixture_referees)
                        $isAssigned = DB::table('fixture_referees')
                            ->where('fixture_id', $fixture->id)
                            ->where('user_id', $user->id)
                            ->exists();
                        
                        // También verificar el campo legacy referee_id por compatibilidad
                        if (!$isAssigned && $fixture->referee_id !== $user->id) {
                            return false;
                        }
                    }

                    // Filtro de búsqueda
                    if ($this->search) {
                        $searchLower = strtolower($this->search);
                        $homeTeam = strtolower($fixture->homeTeam->name ?? '');
                        $awayTeam = strtolower($fixture->awayTeam->name ?? '');
                        $venue = strtolower($fixture->venue->name ?? '');
                        
                        if (!str_contains($homeTeam, $searchLower) && 
                            !str_contains($awayTeam, $searchLower) && 
                            !str_contains($venue, $searchLower)) {
                            return false;
                        }
                    }

                    // Filtro de estado
                    if ($this->statusFilter && $fixture->status !== $this->statusFilter) {
                        return false;
                    }

                    return true;
                });

                // Agrupar fixtures por round_number
                $season->rounds = $filteredFixtures->groupBy('round_number')->map(function ($fixtures, $roundNumber) {
                    return (object)[
                        'id' => 'round_' . $roundNumber,
                        'round_number' => $roundNumber,
                        'fixtures' => $fixtures,
                    ];
                })->sortBy('round_number')->values();

                return $season;
            });

            return $league;
        })->filter(function ($league) {
            // Solo mostrar ligas que tengan al menos una temporada con fixtures
            return $league->seasons->filter(function ($season) {
                return $season->rounds->count() > 0;
            })->count() > 0;
        });

        // Obtener todas las ligas que tienen fixtures para el filtro
        $allLeagues = League::whereHas('seasons.fixtures')->get();
        $allSeasons = Season::whereHas('fixtures')->get();

        return view('livewire.fixtures.index', [
            'leagues' => $leagues,
            'allLeagues' => $allLeagues,
            'seasons' => $allSeasons,
        ])->layout('layouts.app');
    }
}
