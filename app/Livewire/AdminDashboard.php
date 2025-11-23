<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\GameMatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public $totalLeagues;
    public $activeLeagues;
    public $totalSeasons;
    public $totalTeams;
    public $totalMatches;
    public $completedMatches;
    public $totalUsers;
    public $recentActivity = [];
    public $leagueStats = [];
    public $monthlyMatches = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // Estadísticas generales
        $admin = auth()->user()->userable;
        
        $this->totalLeagues = $admin->leagues()->count();
        $this->activeLeagues = $admin->leagues()->where('status', 'active')->count();
        
        $this->totalSeasons = Season::whereHas('league', function($q) use ($admin) {
            $q->where('admin_id', $admin->id);
        })->count();
        
        $this->totalTeams = Team::whereHas('season.league', function($q) use ($admin) {
            $q->where('admin_id', $admin->id);
        })->count();
        
        $this->totalMatches = GameMatch::whereHas('season.league', function($q) use ($admin) {
            $q->where('admin_id', $admin->id);
        })->count();
        
        $this->completedMatches = GameMatch::whereHas('season.league', function($q) use ($admin) {
            $q->where('admin_id', $admin->id);
        })->where('status', 'completed')->count();
        
        $this->totalUsers = User::whereIn('user_type', ['league_manager', 'referee', 'coach', 'player'])
                                ->whereHas('invitationTokens', function($q) use ($admin) {
                                    $q->where('issued_by_user_id', $admin->user->id);
                                })->count();

        // Estadísticas por liga
        $this->leagueStats = $admin->leagues()->with(['seasons' => function($q) {
            $q->withCount(['teams']);
        }])->get()->map(function($league) {
            return [
                'name' => $league->name,
                'seasons' => $league->seasons->count(),
                'teams' => $league->seasons->sum('teams_count'),
                'sport' => $league->sport->name ?? 'N/A'
            ];
        });

        // Actividad reciente
        $this->recentActivity = $this->getRecentActivity($admin);
        
        // Partidos por mes
        $this->monthlyMatches = $this->getMonthlyMatches($admin);
    }

    private function getRecentActivity($admin)
    {
        $activity = [];
        
        // Últimas ligas creadas
        $recentLeagues = $admin->leagues()->latest()->limit(3)->get();
        foreach ($recentLeagues as $league) {
            $activity[] = [
                'type' => 'league_created',
                'message' => "Liga '{$league->name}' creada",
                'date' => $league->created_at->format('d/m/Y H:i'),
                'icon' => 'trophy'
            ];
        }
        
        // Últimos partidos completados
        $recentMatches = GameMatch::whereHas('season.league', function($q) use ($admin) {
            $q->where('admin_id', $admin->id);
        })->where('status', 'completed')->with(['homeTeam', 'awayTeam'])->latest()->limit(5)->get();
        
        foreach ($recentMatches as $match) {
            $activity[] = [
                'type' => 'match_completed',
                'message' => "Partido {$match->homeTeam->name} vs {$match->awayTeam->name} finalizado",
                'date' => $match->finished_at ? $match->finished_at->format('d/m/Y H:i') : 'N/A',
                'icon' => 'play'
            ];
        }
        
        // Ordenar por fecha
        usort($activity, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($activity, 0, 8);
    }

    private function getMonthlyMatches($admin)
    {
        $driver = config('database.default');
        
        if ($driver === 'pgsql') {
            // PostgreSQL usa EXTRACT
            return GameMatch::whereHas('season.league', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })
            ->selectRaw('EXTRACT(MONTH FROM scheduled_at) as month, COUNT(*) as count')
            ->whereRaw('EXTRACT(YEAR FROM scheduled_at) = ?', [now()->year])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        } else {
            // MySQL usa MONTH()
            return GameMatch::whereHas('season.league', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })
            ->selectRaw('MONTH(scheduled_at) as month, COUNT(*) as count')
            ->whereYear('scheduled_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        }
    }

    public function refreshStats()
    {
        $this->loadStats();
        $this->dispatch('stats-refreshed');
    }

    public function render()
    {
        return view('livewire.admin-dashboard')->layout('layouts.app');
    }
}
