<?php

namespace App\Livewire\Standings;

use Livewire\Component;
use App\Models\Season;
use App\Models\League;
use App\Services\StandingsService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $selectedLeagueId = null;
    public $selectedSeasonId = null;
    public $leagues = [];
    public $seasons = [];
    public $standings = [];

    protected $standingsService;


    public function boot(StandingsService $standingsService)
    {
        $this->standingsService = $standingsService;
    }

    public function mount()
    {
        $this->loadLeagues();
        
        // Seleccionar primera liga por defecto
        if ($this->leagues->isNotEmpty()) {
            $this->selectedLeagueId = $this->leagues->first()->id;
            $this->loadSeasons();
            
            // Seleccionar temporada activa o primera disponible
            if ($this->seasons->isNotEmpty()) {
                $activeSeason = $this->seasons->firstWhere('status', 'active');
                $this->selectedSeasonId = $activeSeason ? $activeSeason->id : $this->seasons->first()->id;
                $this->loadStandings();
            }
        }
    }

    public function loadLeagues()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Admin ve todas las ligas
            $this->leagues = League::with('sport')->get();
        } elseif ($user->hasRole('league_manager')) {
            // Manager ve solo sus ligas
            $this->leagues = $user->leagues()->with('sport')->get();
        } else {
            // Otros roles ven ligas donde están involucrados
            $this->leagues = League::whereHas('seasons.teams', function ($query) use ($user) {
                $query->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })->with('sport')->get();
        }
    }

    public function loadSeasons()
    {
        if (!$this->selectedLeagueId) {
            $this->seasons = collect();
            return;
        }

        $this->seasons = Season::where('league_id', $this->selectedLeagueId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updatedSelectedLeagueId()
    {
        $this->selectedSeasonId = null;
        $this->standings = [];
        $this->loadSeasons();
        
        if ($this->seasons->isNotEmpty()) {
            $activeSeason = $this->seasons->firstWhere('status', 'active');
            $this->selectedSeasonId = $activeSeason ? $activeSeason->id : $this->seasons->first()->id;
            $this->loadStandings();
        }
    }

    public function updatedSelectedSeasonId()
    {
        $this->loadStandings();
    }

    public function loadStandings()
    {
        if (!$this->selectedSeasonId) {
            $this->standings = collect();
            return;
        }

        $season = Season::find($this->selectedSeasonId);
        
        if (!$season) {
            $this->standings = collect();
            return;
        }

        // Verificar si hay standings, si no, inicializar
        if (!$this->standingsService->hasStandings($season)) {
            $this->standingsService->initializeStandings($season);
        }

        $this->standings = $this->standingsService->getStandings($season);
    }

    public function recalculate()
    {
        if (!$this->selectedSeasonId) {
            session()->flash('error', 'Selecciona una temporada primero.');
            return;
        }

        $season = Season::find($this->selectedSeasonId);
        
        if (!$season) {
            session()->flash('error', 'Temporada no encontrada.');
            return;
        }

        try {
            $this->standingsService->recalculateStandings($season);
            $this->loadStandings();
            session()->flash('success', 'Tabla de posiciones recalculada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al recalcular: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.standings.index', [
            'leagues' => $this->leagues,
            'seasons' => $this->seasons,
            'standings' => $this->standings,
        ])->layout('layouts.app');
    }
}
