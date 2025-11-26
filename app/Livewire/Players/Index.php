<?php

namespace App\Livewire\Players;

use App\Models\Player;
use App\Models\Team;
use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $teamFilter = '';
    public $leagueFilter = '';
    public $positionFilter = '';
    public $statusFilter = '';

    public $leagues;
    public $teams = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'teamFilter' => ['except' => ''],
        'leagueFilter' => ['except' => ''],
        'positionFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        
        if ($user->user_type === 'admin') {
            $this->leagues = League::orderBy('name')->get();
        } elseif ($user->user_type === 'league_manager') {
            $leagueManager = $user->userable;
            $this->leagues = League::where('id', $leagueManager->league_id)->get();
            $this->leagueFilter = $leagueManager->league_id;
        } elseif ($user->user_type === 'coach') {
            // Coach: obtener ligas de sus equipos
            $coach = $user->userable;
            $teamIds = Team::where('coach_id', $coach->id)->pluck('id');
            $seasonIds = Team::where('coach_id', $coach->id)->pluck('season_id');
            $leagueIds = \App\Models\Season::whereIn('id', $seasonIds)->pluck('league_id')->unique();
            $this->leagues = League::whereIn('id', $leagueIds)->orderBy('name')->get();
            
            // Cargar equipos del coach
            $this->teams = Team::where('coach_id', $coach->id)->orderBy('name')->get();
        } else {
            $this->leagues = collect();
        }

        $this->loadTeams();
    }

    public function updatedLeagueFilter()
    {
        $this->teamFilter = '';
        $this->loadTeams();
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTeamFilter()
    {
        $this->resetPage();
    }

    public function updatedPositionFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function loadTeams()
    {
        if ($this->leagueFilter) {
            $seasonIds = \App\Models\Season::where('league_id', $this->leagueFilter)->pluck('id');
            $this->teams = Team::whereIn('season_id', $seasonIds)
                ->orderBy('name')
                ->get();
        } else {
            $this->teams = collect();
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->teamFilter = '';
        $this->positionFilter = '';
        $this->statusFilter = '';
        
        $user = auth()->user();
        if ($user->user_type !== 'league_manager') {
            $this->leagueFilter = '';
        }
        
        $this->loadTeams();
        $this->resetPage();
    }

    public function deletePlayer($playerId)
    {
        $player = Player::find($playerId);
        
        if (!$player) {
            $this->dispatch('error', 'Jugador no encontrado');
            return;
        }

        // Verificar permisos
        $user = auth()->user();
        if ($user->user_type === 'league_manager') {
            $leagueManager = $user->userable;
            if ($player->league_id !== $leagueManager->league_id) {
                $this->dispatch('error', 'No tienes permiso para eliminar este jugador');
                return;
            }
        }

        $playerName = $player->full_name;
        $player->delete();
        
        $this->dispatch('player-deleted', $playerName);
    }

    public function render()
    {
        $user = auth()->user();
        
        $query = Player::with(['team', 'league'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('jersey_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->leagueFilter, fn($q) => $q->where('league_id', $this->leagueFilter))
            ->when($this->teamFilter, fn($q) => $q->where('team_id', $this->teamFilter))
            ->when($this->positionFilter, fn($q) => $q->where('position', $this->positionFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter));

        // Filtrar por rol del usuario
        if ($user->user_type === 'coach') {
            $coach = $user->userable;
            $teamIds = Team::where('coach_id', $coach->id)->pluck('id');
            $query->whereIn('team_id', $teamIds);
        } elseif ($user->user_type === 'league_manager') {
            $leagueManager = $user->userable;
            $query->where('league_id', $leagueManager->league_id);
        }

        $players = $query->orderBy('jersey_number')
            ->orderBy('last_name')
            ->paginate(15);

        return view('livewire.players.index', [
            'players' => $players,
            'positions' => Player::positions(),
            'statuses' => Player::statuses(),
            'statusColors' => Player::statusColors(),
        ])->layout('layouts.app');
    }
}
