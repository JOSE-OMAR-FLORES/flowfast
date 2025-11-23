<?php

namespace App\Livewire\FriendlyMatches;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GameMatch;
use App\Models\Sport;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $sport_filter = '';
    public $status_filter = '';
    public $search = '';

    protected $queryString = ['sport_filter', 'status_filter', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSportFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function deleteMatch($matchId)
    {
        try {
            $match = GameMatch::findOrFail($matchId);
            
            // Verificar que sea un partido amistoso
            if (!$match->is_friendly) {
                session()->flash('error', 'Solo se pueden eliminar partidos amistosos');
                return;
            }

            DB::beginTransaction();

            // Eliminar ingresos y egresos relacionados
            $match->incomes()->delete();
            $match->expenses()->delete();

            // Eliminar el partido
            $match->delete();

            DB::commit();

            session()->flash('success', 'Partido amistoso eliminado exitosamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al eliminar el partido: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = GameMatch::with(['homeTeam.season.league', 'awayTeam.season.league', 'referee', 'venue'])
            ->where('is_friendly', true);

        // Filtro por deporte
        if ($this->sport_filter) {
            $query->whereHas('homeTeam.season.league', function($q) {
                $q->where('sport_id', $this->sport_filter);
            });
        }

        // Filtro por estado
        if ($this->status_filter) {
            switch ($this->status_filter) {
                case 'scheduled':
                    $query->where('status', 'scheduled');
                    break;
                case 'live':
                    $query->where('status', 'live');
                    break;
                case 'finished':
                    $query->where('status', 'finished');
                    break;
            }
        }

        // Búsqueda por equipos
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('homeTeam', function($team) {
                    $team->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('awayTeam', function($team) {
                    $team->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        $matches = $query->orderBy('match_date', 'desc')
            ->orderBy('match_time', 'desc')
            ->paginate(15);

        $sports = Sport::all();

        return view('livewire.friendly-matches.index', [
            'matches' => $matches,
            'sports' => $sports
        ])->layout('layouts.app', ['title' => 'Partidos Amistosos']);
    }
}
