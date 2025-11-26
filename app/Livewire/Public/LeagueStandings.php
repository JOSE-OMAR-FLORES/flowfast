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
    public $sport;
    public $standingColumns;
    public $allowsDraws;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $this->league = League::where('slug', $slug)
            ->where('is_public', true)
            ->with(['sport'])
            ->firstOrFail();

        $this->activeSeason = $this->league->seasons()->where('status', 'active')->latest()->first();
        
        // Cargar configuración del deporte
        $this->sport = $this->league->sport;
        $this->standingColumns = $this->sport ? $this->sport->getStandingColumns() : [];
        $this->allowsDraws = $this->sport ? $this->sport->allowsDraws() : true;
    }

    public function render()
    {
        $standings = collect();
        
        if ($this->activeSeason) {
            // Ordenar según el deporte
            $query = Standing::with(['team'])
                ->where('season_id', $this->activeSeason->id);
            
            // Aplicar ordenamiento según el deporte
            $sportSlug = $this->sport->slug ?? 'futbol';
            
            switch ($sportSlug) {
                case 'basquetbol':
                case 'beisbol':
                    // Sin empates, ordenar por victorias, luego diferencia
                    $query->orderBy('points', 'desc')
                        ->orderBy('won', 'desc')
                        ->orderBy('goal_difference', 'desc');
                    break;
                case 'voleibol':
                    // Ordenar por puntos, sets ganados, diferencia de puntos
                    $query->orderBy('points', 'desc')
                        ->orderBy('goals_for', 'desc') // Sets ganados
                        ->orderBy('goal_difference', 'desc');
                    break;
                default:
                    // Fútbol: puntos, diferencia de goles, goles a favor
                    $query->orderBy('points', 'desc')
                        ->orderBy('goal_difference', 'desc')
                        ->orderBy('goals_for', 'desc');
            }
            
            $standings = $query->get();
        }

        return view('livewire.public.league-standings', [
            'standings' => $standings,
            'standingColumns' => $this->standingColumns,
            'allowsDraws' => $this->allowsDraws,
            'sportSlug' => $this->sport->slug ?? 'futbol',
        ])->layout('layouts.public', ['title' => 'Posiciones - ' . $this->league->name]);
    }
}
