<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\Team;
use App\Models\Player;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $coach = $user->userable;
        
        // Obtener equipos del entrenador
        $teams = Team::where('coach_id', $coach->id)
            ->with(['season.league', 'players'])
            ->get();
        
        // Obtener jugadores
        $totalPlayers = Player::whereIn('team_id', $teams->pluck('id'))->count();
        
        // Obtener pagos pendientes
        $pendingPayments = Income::whereIn('team_id', $teams->pluck('id'))
            ->where('payment_status', 'pending')
            ->sum('amount');
        
        // Obtener próximos partidos
        $upcomingMatches = [];
        foreach ($teams as $team) {
            $matches = $team->homeMatches()
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at', 'asc')
                ->limit(3)
                ->get();
            
            $upcomingMatches = array_merge($upcomingMatches, $matches->toArray());
            
            $awayMatches = $team->awayMatches()
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at', 'asc')
                ->limit(3)
                ->get();
            
            $upcomingMatches = array_merge($upcomingMatches, $awayMatches->toArray());
        }
        
        // Ordenar por fecha
        usort($upcomingMatches, function($a, $b) {
            return strtotime($a['scheduled_at']) - strtotime($b['scheduled_at']);
        });
        
        $upcomingMatches = array_slice($upcomingMatches, 0, 5);
        
        return view('livewire.coach.dashboard', [
            'teams' => $teams,
            'totalPlayers' => $totalPlayers,
            'pendingPayments' => $pendingPayments,
            'upcomingMatches' => $upcomingMatches,
        ])->layout('layouts.app');
    }
}
