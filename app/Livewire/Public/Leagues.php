<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\League;
use App\Models\Sport;

class Leagues extends Component
{
    use WithPagination;

    public $search = '';
    public $sportFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSportFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $leagues = League::where('is_public', true)
            ->with(['sport', 'seasons' => function ($query) {
                $query->where('status', 'active')->latest();
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->sportFilter, function ($query) {
                $query->where('sport_id', $this->sportFilter);
            })
            ->latest()
            ->paginate(9);

        $sports = Sport::withCount('leagues')->get();

        return view('livewire.public.leagues', [
            'leagues' => $leagues,
            'sports' => $sports
        ])->layout('layouts.public', ['title' => 'Ligas - FlowFast']);
    }
}
