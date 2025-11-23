<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;

class LeagueHome extends Component
{
    public $slug;
    public $league;
    public $activeSeason;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $this->league = League::where('slug', $slug)
            ->where('is_public', true)
            ->with(['sport', 'seasons' => function ($query) {
                $query->where('status', 'active')->latest();
            }])
            ->firstOrFail();

        $this->activeSeason = $this->league->seasons->first();
    }

    public function render()
    {
        return view('livewire.public.league-home')
            ->layout('layouts.public', ['title' => $this->league->name . ' - FlowFast']);
    }
}
