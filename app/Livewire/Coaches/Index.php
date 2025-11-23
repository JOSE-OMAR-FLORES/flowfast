<?php

namespace App\Livewire\Coaches;

use App\Models\Coach;
use App\Models\League;
use App\Models\Sport;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterLeague = '';
    public $filterSport = '';
    public $filterLicense = '';
    public $sortField = 'first_name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterLeague' => ['except' => ''],
        'filterSport' => ['except' => ''],
        'filterLicense' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLeague()
    {
        $this->resetPage();
    }

    public function updatingFilterSport()
    {
        $this->resetPage();
    }

    public function updatingFilterLicense()
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

    public function editCoach($coachId)
    {
        return redirect()->route('coaches.edit', $coachId);
    }

    public function confirmDelete($coachId)
    {
        $coach = Coach::findOrFail($coachId);
        
        // Si tiene usuario asociado, eliminarlo también
        if ($coach->user) {
            $coach->user->delete();
        }
        
        $coach->delete();
        
        session()->flash('success', 'Entrenador eliminado exitosamente.');
        
        return redirect()->route('coaches.index');
    }

    public function render()
    {
        $user = auth()->user();
        $userType = $user->userable_type ? class_basename($user->userable_type) : null;

        // Query base
        $query = Coach::with(['team.season.league.sport', 'user']);

        // Filtrar por rol
        if ($userType === 'LeagueManager') {
            $leagueManager = $user->userable;
            $assignedLeagues = $leagueManager->assigned_leagues;
            
            if (is_string($assignedLeagues)) {
                $assignedLeagues = json_decode($assignedLeagues, true) ?? [];
            }
            
            $query->whereHas('team.season.league', function($q) use ($assignedLeagues) {
                $q->whereIn('id', $assignedLeagues);
            });
        }

        // Búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('license_number', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por liga
        if ($this->filterLeague) {
            $query->whereHas('team.season.league', function($q) {
                $q->where('id', $this->filterLeague);
            });
        }

        // Filtro por deporte
        if ($this->filterSport) {
            $query->whereHas('team.season.league.sport', function($q) {
                $q->where('id', $this->filterSport);
            });
        }

        // Filtro por licencia
        if ($this->filterLicense !== '') {
            if ($this->filterLicense === '1') {
                $query->whereNotNull('license_number');
            } else {
                $query->whereNull('license_number');
            }
        }

        // Ordenamiento
        if ($this->sortField === 'team') {
            $query->join('teams', 'coaches.team_id', '=', 'teams.id')
                  ->orderBy('teams.name', $this->sortDirection)
                  ->select('coaches.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $coaches = $query->paginate(15);

        // Obtener listas para filtros
        $sports = Sport::orderBy('name')->get();
        
        if ($userType === 'LeagueManager') {
            $leagueManager = $user->userable;
            $assignedLeagues = $leagueManager->assigned_leagues;
            
            if (is_string($assignedLeagues)) {
                $assignedLeagues = json_decode($assignedLeagues, true) ?? [];
            }
            
            $leagues = League::whereIn('id', $assignedLeagues)->orderBy('name')->get();
        } else {
            $leagues = League::orderBy('name')->get();
        }

        return view('livewire.coaches.index', [
            'coaches' => $coaches,
            'sports' => $sports,
            'leagues' => $leagues,
        ])->layout('layouts.app');
    }
}
