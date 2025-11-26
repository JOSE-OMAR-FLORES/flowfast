<?php

namespace App\Livewire\Referee;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Referee;
use App\Models\Fixture;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function getReferee()
    {
        $user = Auth::user();
        // La relación es polimórfica: users.userable_id = referees.id
        return Referee::find($user->userable_id);
    }

    public function getStats()
    {
        $user = Auth::user();
        $referee = $this->getReferee();
        
        if (!$referee) {
            return [
                'matches_refereed' => 0,
                'upcoming_matches' => 0,
                'month_earnings' => 0,
                'pending_payments' => 0,
                'ready_to_confirm' => 0,
            ];
        }

        // Partidos arbitrados: contar fixtures completados a través de los expenses del árbitro
        // Un partido se considera arbitrado cuando el expense está confirmado o el fixture está completado
        $matchesRefereed = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->where('expense_type', 'referee_payment')
            ->whereHas('fixture', function ($q) {
                $q->where('status', 'completed');
            })
            ->distinct('fixture_id')
            ->count('fixture_id');

        // Próximos partidos (a través de expenses con fixtures scheduled)
        $upcomingMatches = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->where('expense_type', 'referee_payment')
            ->whereHas('fixture', function ($q) {
                $q->whereIn('status', ['scheduled', 'in_progress'])
                  ->where('match_date', '>=', now());
            })
            ->distinct('fixture_id')
            ->count('fixture_id');

        // Ganancias del mes (confirmadas)
        $monthEarnings = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->where('payment_status', 'confirmed')
            ->whereMonth('confirmed_at', now()->month)
            ->whereYear('confirmed_at', now()->year)
            ->sum('amount');

        // Pagos pendientes (pending/approved)
        $pendingPayments = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->whereIn('payment_status', ['pending', 'approved'])
            ->count();

        // Listos para confirmar
        $readyToConfirm = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->whereIn('payment_status', ['ready_for_payment', 'paid'])
            ->count();

        return [
            'matches_refereed' => $matchesRefereed,
            'upcoming_matches' => $upcomingMatches,
            'month_earnings' => $monthEarnings,
            'pending_payments' => $pendingPayments,
            'ready_to_confirm' => $readyToConfirm,
        ];
    }

    public function getUpcomingMatches()
    {
        $user = Auth::user();
        $referee = $this->getReferee();
        
        if (!$referee) {
            return collect();
        }

        // Obtener los fixture_ids de los expenses del árbitro
        $fixtureIds = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->where('expense_type', 'referee_payment')
            ->pluck('fixture_id')
            ->unique();

        return Fixture::with(['homeTeam', 'awayTeam', 'season.league'])
            ->whereIn('id', $fixtureIds)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->where('match_date', '>=', now())
            ->orderBy('match_date', 'asc')
            ->limit(5)
            ->get();
    }

    public function getRecentPayments()
    {
        $user = Auth::user();
        $referee = $this->getReferee();
        
        if (!$referee) {
            return collect();
        }

        return Expense::with(['league', 'fixture'])
            ->where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getPaymentsToConfirm()
    {
        $user = Auth::user();
        $referee = $this->getReferee();
        
        if (!$referee) {
            return collect();
        }

        return Expense::with(['league', 'fixture'])
            ->where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->whereIn('payment_status', ['ready_for_payment', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        $referee = $this->getReferee();
        $stats = $this->getStats();
        $upcomingMatches = $this->getUpcomingMatches();
        $recentPayments = $this->getRecentPayments();
        $paymentsToConfirm = $this->getPaymentsToConfirm();

        return view('livewire.referee.dashboard', [
            'referee' => $referee,
            'stats' => $stats,
            'upcomingMatches' => $upcomingMatches,
            'recentPayments' => $recentPayments,
            'paymentsToConfirm' => $paymentsToConfirm,
        ])->layout('layouts.app');
    }
}
