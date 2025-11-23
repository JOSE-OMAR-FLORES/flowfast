<?php

namespace App\Livewire\Seasons;

use App\Models\Season;
use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $leagueFilter = '';
    public $statusFilter = '';
    public $formatFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'leagueFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'formatFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLeagueFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingFormatFilter()
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
        $season = Season::findOrFail($id);
        
        // Verificar permisos
        if (auth()->user()->role !== 'admin') {
            session()->flash('error', 'No tienes permisos para eliminar temporadas.');
            return;
        }

        $season->delete();
        session()->flash('success', 'Temporada eliminada correctamente.');
    }

    public function render()
    {
        $user = auth()->user();
        
        $seasons = Season::query()
            ->with(['league.sport', 'teams'])
            ->when($user->role === 'league_manager' && $user->leagueManager, function($query) use ($user) {
                $query->whereHas('league', function($q) use ($user) {
                    $q->where('manager_id', $user->leagueManager->id);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->leagueFilter, function ($query) {
                $query->where('league_id', $this->leagueFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->formatFilter, function ($query) {
                $query->where('format', $this->formatFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Obtener ligas disponibles según el rol
        $leagues = ($user->role === 'admin' || !$user->leagueManager)
            ? League::all() 
            : League::where('manager_id', $user->leagueManager->id)->get();

        return view('livewire.seasons.index', [
            'seasons' => $seasons,
            'leagues' => $leagues,
        ])->layout('layouts.app');
    }
}
