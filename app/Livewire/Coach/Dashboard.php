<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\Team;
use App\Models\Player;
use App\Models\Income;
use App\Models\Fixture;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function getCoach()
    {
        return Auth::user()->userable;
    }

    public function getTeams()
    {
        $coach = $this->getCoach();
        return Team::where('coach_id', $coach->id)
            ->with(['season.league', 'players'])
            ->get();
    }

    public function getTeamStats($teams)
    {
        $teamIds = $teams->pluck('id');
        $stats = [];

        foreach ($teams as $team) {
            $completedMatches = Fixture::where('status', 'completed')
                ->where(function ($q) use ($team) {
                    $q->where('home_team_id', $team->id)
                      ->orWhere('away_team_id', $team->id);
                })
                ->get();

            $wins = 0;
            $draws = 0;
            $losses = 0;
            $goalsFor = 0;
            $goalsAgainst = 0;

            foreach ($completedMatches as $match) {
                $isHome = $match->home_team_id === $team->id;
                $teamScore = $isHome ? $match->home_score : $match->away_score;
                $opponentScore = $isHome ? $match->away_score : $match->home_score;

                $goalsFor += $teamScore;
                $goalsAgainst += $opponentScore;

                if ($teamScore > $opponentScore) {
                    $wins++;
                } elseif ($teamScore < $opponentScore) {
                    $losses++;
                } else {
                    $draws++;
                }
            }

            // Puntos: 3 por victoria, 1 por empate
            $points = ($wins * 3) + $draws;

            $stats[$team->id] = [
                'played' => $completedMatches->count(),
                'wins' => $wins,
                'draws' => $draws,
                'losses' => $losses,
                'goals_for' => $goalsFor,
                'goals_against' => $goalsAgainst,
                'goal_difference' => $goalsFor - $goalsAgainst,
                'points' => $points,
            ];
        }

        return $stats;
    }

    public function getUpcomingMatches($teams)
    {
        $teamIds = $teams->pluck('id');

        return Fixture::with(['homeTeam', 'awayTeam', 'season.league', 'venue'])
            ->where(function ($q) use ($teamIds) {
                $q->whereIn('home_team_id', $teamIds)
                  ->orWhereIn('away_team_id', $teamIds);
            })
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->where('match_date', '>=', now()->startOfDay())
            ->orderBy('match_date', 'asc')
            ->limit(5)
            ->get();
    }

    public function getRecentResults($teams)
    {
        $teamIds = $teams->pluck('id');

        return Fixture::with(['homeTeam', 'awayTeam', 'season.league'])
            ->where(function ($q) use ($teamIds) {
                $q->whereIn('home_team_id', $teamIds)
                  ->orWhereIn('away_team_id', $teamIds);
            })
            ->where('status', 'completed')
            ->orderBy('match_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getPaymentsSummary($teams)
    {
        $teamIds = $teams->pluck('id');

        $pending = Income::whereIn('team_id', $teamIds)
            ->whereIn('payment_status', ['pending', 'approved'])
            ->get();

        $paid = Income::whereIn('team_id', $teamIds)
            ->where('payment_status', 'confirmed')
            ->get();

        $awaitingConfirmation = Income::whereIn('team_id', $teamIds)
            ->whereIn('payment_status', ['ready_for_payment', 'paid'])
            ->get();

        return [
            'pending_count' => $pending->count(),
            'pending_amount' => $pending->sum('amount'),
            'paid_count' => $paid->count(),
            'paid_amount' => $paid->sum('amount'),
            'awaiting_count' => $awaitingConfirmation->count(),
            'awaiting_amount' => $awaitingConfirmation->sum('amount'),
        ];
    }

    public function getPendingPayments($teams)
    {
        $teamIds = $teams->pluck('id');

        return Income::with(['team', 'league', 'season'])
            ->whereIn('team_id', $teamIds)
            ->whereIn('payment_status', ['pending', 'approved', 'ready_for_payment', 'paid'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $coach = $this->getCoach();
        $teams = $this->getTeams();
        $teamStats = $this->getTeamStats($teams);
        $upcomingMatches = $this->getUpcomingMatches($teams);
        $recentResults = $this->getRecentResults($teams);
        $paymentsSummary = $this->getPaymentsSummary($teams);
        $pendingPayments = $this->getPendingPayments($teams);
        $totalPlayers = Player::whereIn('team_id', $teams->pluck('id'))->count();

        return view('livewire.coach.dashboard', [
            'coach' => $coach,
            'teams' => $teams,
            'teamStats' => $teamStats,
            'totalPlayers' => $totalPlayers,
            'upcomingMatches' => $upcomingMatches,
            'recentResults' => $recentResults,
            'paymentsSummary' => $paymentsSummary,
            'pendingPayments' => $pendingPayments,
        ])->layout('layouts.app');
    }
}
