<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;
use App\Models\Fixture;

class LeagueFixtures extends Component
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
        $fixtures = collect();
        
        if ($this->activeSeason) {
            $fixtures = Fixture::where('season_id', $this->activeSeason->id)
                ->with(['homeTeam', 'awayTeam', 'venue'])
                ->orderBy('match_date', 'desc')
                ->orderBy('match_time', 'desc')
                ->get()
                ->groupBy(function($fixture) {
                    return $fixture->match_date->format('Y-m-d');
                });
        }

        return view('livewire.public.league-fixtures', [
            'fixtures' => $fixtures
        ])->layout('layouts.public', ['title' => 'Calendario - ' . $this->league->name]);
    }
}
