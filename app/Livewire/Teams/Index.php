<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Models\Season;
use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterLeague = '';
    public $filterSeason = '';
    public $filterStatus = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'filterLeague', 'filterSeason', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLeague()
    {
        $this->resetPage();
        $this->filterSeason = ''; // Reset season filter when league changes
    }

    public function updatingFilterSeason()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($id)
    {
        $team = Team::find($id);
        
        if (!$team) {
            session()->flash('error', 'Equipo no encontrado');
            return;
        }

        // Solo admin puede eliminar
        if (auth()->user()->user_type !== 'admin') {
            session()->flash('error', 'No tienes permisos para eliminar equipos');
            return;
        }

        $team->delete();
        session()->flash('success', 'Equipo eliminado exitosamente');
    }

    public function render()
    {
        $user = auth()->user();
        
        // Base query
        $query = Team::with(['season.league', 'coach', 'players']);

        // Filtro por rol
        if ($user->user_type === 'league_manager' && $user->userable) {
            $query->whereHas('season.league', function($q) use ($user) {
                $q->where('manager_id', $user->userable->id);
            });
        } elseif ($user->user_type === 'coach' && $user->userable) {
            // Filtrar solo los equipos del coach actual
            $coachId = $user->userable->id;
            $query->where('coach_id', $coachId);
            
            // Debug temporal - quitar después
            Log::info('Coach ID: ' . $coachId);
            Log::info('User Type: ' . $user->user_type);
            Log::info('Userable Type: ' . get_class($user->userable));
        }

        // Búsqueda
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Filtros
        if ($this->filterLeague) {
            $query->whereHas('season', function($q) {
                $q->where('league_id', $this->filterLeague);
            });
        }

        if ($this->filterSeason) {
            $query->where('season_id', $this->filterSeason);
        }

        if ($this->filterStatus) {
            // $query->where('status', $this->filterStatus); // La tabla teams no tiene columna status
        }

        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        $teams = $query->paginate(10);

        // Obtener ligas y temporadas para filtros
        $leagues = ($user->user_type === 'admin' || !$user->userable)
            ? League::all() 
            : ($user->user_type === 'league_manager' && $user->userable
                ? League::where('manager_id', $user->userable->id)->get()
                : collect());

        $seasons = $this->filterLeague 
            ? Season::where('league_id', $this->filterLeague)->get()
            : Season::all();

        return view('livewire.teams.index', [
            'teams' => $teams,
            'leagues' => $leagues,
            'seasons' => $seasons,
        ])->layout('layouts.app');
    }
}
