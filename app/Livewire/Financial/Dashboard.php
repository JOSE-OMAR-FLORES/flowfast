<?php

namespace App\Livewire\Financial;

use App\Models\League;
use App\Models\Season;
use App\Services\FinancialDashboardService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Dashboard Financiero')]
class Dashboard extends Component
{
    public $leagueId;
    public $seasonId = null;
    public $period = 'month';
    public $metrics = [];
    
    public function mount($leagueId)
    {
        $this->leagueId = $leagueId;
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        $league = League::findOrFail($this->leagueId);
        $season = $this->seasonId ? Season::find($this->seasonId) : null;
        
        $service = new FinancialDashboardService();
        $this->metrics = $service->getDashboardMetrics($league, $season, $this->period);
    }

    public function updatedSeasonId()
    {
        $this->loadMetrics();
    }

    public function updatedPeriod()
    {
        $this->loadMetrics();
    }

    public function render()
    {
        $league = League::with('seasons')->findOrFail($this->leagueId);
        
        return view('livewire.financial.dashboard', [
            'league' => $league,
            'seasons' => $league->seasons,
        ]);
    }
}
