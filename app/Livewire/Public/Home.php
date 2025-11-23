<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;

class Home extends Component
{
    public function render()
    {
        $leagues = League::where('is_public', true)
            ->with(['sport', 'seasons' => function ($query) {
                $query->where('status', 'active')->latest();
            }])
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.public.home', [
            'leagues' => $leagues
        ])->layout('layouts.public', ['title' => 'FlowFast - Gestión de Ligas Deportivas']);
    }
}
