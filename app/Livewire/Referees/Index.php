<?php

namespace App\Livewire\Referees;

use App\Models\Referee;
use App\Models\League;
use App\Models\Sport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $refereeTypeFilter = '';
    public $leagueFilter = '';
    public $sportFilter = '';
    public $sortField = 'first_name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'refereeTypeFilter' => ['except' => ''],
        'leagueFilter' => ['except' => ''],
        'sportFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRefereeTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingLeagueFilter()
    {
        $this->resetPage();
    }

    public function updatingSportFilter()
    {
        $this->resetPage();
        $this->leagueFilter = ''; // Reset league filter when sport changes
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

    public function editReferee($refereeId)
    {
        return redirect()->route('referees.edit', $refereeId);
    }

    public function confirmDelete($refereeId)
    {
        $referee = Referee::findOrFail($refereeId);
        
        // Si tiene usuario asociado, eliminarlo también
        if ($referee->user) {
            $referee->user->delete();
        }
        
        $referee->delete();
        
        session()->flash('success', 'Árbitro eliminado exitosamente.');
        
        return redirect()->route('referees.index');
    }

    public function render()
    {
        $user = Auth::user();

        // Query base para árbitros
        $query = Referee::query()
            ->with(['league.sport', 'user']);

        // Filtrar según el rol del usuario
        if ($user->user_type === 'league_manager') {
            // El encargado solo ve árbitros de sus ligas asignadas
            $assignedLeagues = $user->userable->assigned_leagues ?? [];
            if (!empty($assignedLeagues)) {
                $leagueIds = is_array($assignedLeagues) ? $assignedLeagues : explode(',', $assignedLeagues);
                $query->whereIn('league_id', $leagueIds);
            } else {
                $query->whereNull('league_id'); // No show any if no leagues assigned
            }
        }

        // Búsqueda por nombre
        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por tipo de árbitro
        if ($this->refereeTypeFilter) {
            $query->where('referee_type', $this->refereeTypeFilter);
        }

        // Filtro por liga
        if ($this->leagueFilter) {
            $query->where('league_id', $this->leagueFilter);
        }

        // Filtro por deporte (a través de la liga)
        if ($this->sportFilter) {
            $query->whereHas('league', function($q) {
                $q->where('sport_id', $this->sportFilter);
            });
        }

        // Ordenamiento
        if ($this->sortField === 'league') {
            $query->leftJoin('leagues', 'referees.league_id', '=', 'leagues.id')
                  ->orderBy('leagues.name', $this->sortDirection)
                  ->select('referees.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $referees = $query->paginate(15);

        // Obtener deportes para el filtro
        $sports = Sport::all();

        // Obtener ligas para el filtro
        $leagues = $this->sportFilter 
            ? League::where('sport_id', $this->sportFilter)->get()
            : League::all();

        // Si es league manager, filtrar ligas
        if ($user->user_type === 'league_manager') {
            $assignedLeagues = $user->userable->assigned_leagues ?? [];
            if (!empty($assignedLeagues)) {
                $leagueIds = is_array($assignedLeagues) ? $assignedLeagues : explode(',', $assignedLeagues);
                $leagues = $leagues->whereIn('id', $leagueIds);
            }
        }

        return view('livewire.referees.index', [
            'referees' => $referees,
            'sports' => $sports,
            'leagues' => $leagues,
        ])->layout('layouts.app');
    }

    public function getRefereeTypeLabel($type)
    {
        return match($type) {
            'main' => 'Principal',
            'assistant' => 'Asistente',
            'scorer' => 'Anotador',
            default => $type,
        };
    }
}
