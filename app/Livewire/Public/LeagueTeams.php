<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;
use App\Models\Team;

class LeagueTeams extends Component
{
    public $slug;
    public $league;
    public $activeSeason;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $this->league = League::where('slug', $slug)
            ->where('is_public', true)
            ->with(['sport'])
            ->firstOrFail();

        $this->activeSeason = $this->league->seasons()->where('status', 'active')->latest()->first();
    }

    public function render()
    {
        $teams = collect();
        
        if ($this->activeSeason) {
            $teams = Team::where('season_id', $this->activeSeason->id)
                ->with(['coach'])
                ->get();
        }

        return view('livewire.public.league-teams', [
            'teams' => $teams
        ])->layout('layouts.public', ['title' => 'Equipos - ' . $this->league->name]);
    }
}
