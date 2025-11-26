<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;
use App\Models\Standing;

class LeagueStandings extends Component
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
        $standings = collect();
        
        if ($this->activeSeason) {
            $standings = Standing::with(['team'])
                ->where('season_id', $this->activeSeason->id)
                ->orderBy('points', 'desc')
                ->orderBy('goal_difference', 'desc')
                ->orderBy('goals_for', 'desc')
                ->get();
        }

        return view('livewire.public.league-standings', [
            'standings' => $standings
        ])->layout('layouts.public', ['title' => 'Posiciones - ' . $this->league->name]);
    }
}
