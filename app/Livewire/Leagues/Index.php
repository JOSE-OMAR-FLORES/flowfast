<?php

namespace App\Livewire\Leagues;

use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sportFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sportFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSportFilter()
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

    public $confirmingDeletion = false;
    public $leagueToDelete = null;

    public function confirmDelete($id)
    {
        $this->leagueToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->leagueToDelete = null;
    }

    public function deleteLeague()
    {
        if (!$this->leagueToDelete) {
            return;
        }

        try {
            $league = League::findOrFail($this->leagueToDelete);
             
            // Verificar permisos (solo admin puede eliminar)
            if (auth()->user()->user_type !== 'admin') {
                session()->flash('error', 'No tienes permisos para eliminar ligas.');
                $this->cancelDelete();
                return;
            }

            // Eliminar seasons y todo su contenido (fixtures, teams, rounds, etc)
            foreach ($league->seasons as $season) {
                // Eliminar fixtures de la temporada
                foreach ($season->fixtures as $fixture) {
                    $fixture->incomes()->delete();
                    $fixture->expenses()->delete();
                    $fixture->fixtureEvents()->delete();
                    $fixture->officials()->delete();
                    $fixture->referees()->detach();
                    $fixture->delete();
                }
                // Eliminar equipos de la temporada
                foreach ($season->teams as $team) {
                    foreach ($team->players as $player) {
                        $player->fixtureEvents()->delete();
                        $player->delete();
                    }
                    $team->homeMatches()->delete();
                    $team->awayMatches()->delete();
                    $team->delete();
                }
                $season->rounds()->delete();
                $season->delete();
            }

            // Eliminar venues
            foreach ($league->venues as $venue) {
                $venue->fixtures()->delete();
                $venue->delete();
            }

            // Eliminar árbitros
            foreach ($league->referees as $referee) {
                if ($referee->user) {
                    $referee->user->delete();
                }
                $referee->delete();
            }

            // Eliminar métodos de pago
            foreach ($league->paymentMethods as $method) {
                $method->delete();
            }

            // Eliminar incomes y expenses generales
            $league->incomes()->delete();
            $league->expenses()->delete();

            // Eliminar jugadores sueltos
            foreach ($league->players as $player) {
                $player->fixtureEvents()->delete();
                $player->delete();
            }

            // Eliminar tokens de invitación
            foreach ($league->invitationTokens as $token) {
                $token->delete();
            }

            // Eliminar managers de la liga (opcional, si no están en uso en otras ligas)
            if ($league->manager) {
                $league->manager->delete();
            }

            $leagueName = $league->name;
            
            // Finalmente, eliminar la liga
            $league->delete();
            
            session()->flash('success', "Liga '{$leagueName}' y todo su contenido eliminados correctamente.");
            
            $this->cancelDelete();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la liga: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    public function render()
    {
        $leagues = League::query()
            ->with(['sport', 'admin', 'manager'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->sportFilter, function ($query) {
                $query->where('sport_id', $this->sportFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $sports = \App\Models\Sport::all();

        return view('livewire.leagues.index', [
            'leagues' => $leagues,
            'sports' => $sports,
        ])->layout('layouts.app');
    }
}
