<?php

namespace App\Services;

use App\Models\League;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Season;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialDashboardService
{
    /**
     * Obtener todas las métricas del dashboard financiero
     */
    public function getDashboardMetrics(League $league, ?Season $season = null, string $period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'summary' => $this->getSummaryMetrics($league, $season, $dateRange),
            'income_breakdown' => $this->getIncomeBreakdown($league, $season, $dateRange),
            'expense_breakdown' => $this->getExpenseBreakdown($league, $season, $dateRange),
            'payment_status' => $this->getPaymentStatusMetrics($league, $season),
            'pending_items' => $this->getPendingItems($league, $season),
            'recent_transactions' => $this->getRecentTransactions($league, $season, 10),
            'alerts' => $this->getFinancialAlerts($league, $season),
        ];
    }

    /**
     * Resumen principal de métricas
     */
    protected function getSummaryMetrics(League $league, ?Season $season, array $dateRange): array
    {
        $incomeQuery = Income::where('league_id', $league->id);
        $expenseQuery = Expense::where('league_id', $league->id);

        if ($season) {
            $incomeQuery->where('season_id', $season->id);
            $expenseQuery->where('season_id', $season->id);
        }

        $totalIncome = (clone $incomeQuery)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange)
            ->sum('amount');

        $totalExpenses = (clone $expenseQuery)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange)
            ->sum('amount');

        $pendingIncome = (clone $incomeQuery)
            ->whereIn('payment_status', ['pending', 'paid_by_team', 'confirmed_by_admin'])
            ->sum('amount');

        $pendingExpenses = (clone $expenseQuery)
            ->whereIn('payment_status', ['pending', 'approved', 'ready_for_payment'])
            ->sum('amount');

        $netProfit = $totalIncome - $totalExpenses;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

        return [
            'total_income' => round($totalIncome, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($netProfit, 2),
            'profit_margin' => round($profitMargin, 2),
            'pending_income' => round($pendingIncome, 2),
            'pending_expenses' => round($pendingExpenses, 2),
            'available_balance' => round($totalIncome - $totalExpenses - $pendingExpenses, 2),
        ];
    }

    /**
     * Desglose de ingresos por tipo
     */
    protected function getIncomeBreakdown(League $league, ?Season $season, array $dateRange): array
    {
        $query = Income::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange);

        if ($season) {
            $query->where('season_id', $season->id);
        }

        return $query->selectRaw('income_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('income_type')
            ->get()
            ->map(function ($item) {
                $income = new Income(['income_type' => $item->income_type]);
                return [
                    'type' => $item->income_type,
                    'label' => $income->type_label,
                    'total' => round($item->total, 2),
                    'count' => $item->count,
                    'average' => round($item->count > 0 ? $item->total / $item->count : 0, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Desglose de egresos por tipo
     */
    protected function getExpenseBreakdown(League $league, ?Season $season, array $dateRange): array
    {
        $query = Expense::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange);

        if ($season) {
            $query->where('season_id', $season->id);
        }

        return $query->selectRaw('expense_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('expense_type')
            ->get()
            ->map(function ($item) {
                $expense = new Expense(['expense_type' => $item->expense_type]);
                return [
                    'type' => $item->expense_type,
                    'label' => $expense->type_label,
                    'total' => round($item->total, 2),
                    'count' => $item->count,
                    'average' => round($item->count > 0 ? $item->total / $item->count : 0, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Métricas de estado de pagos
     */
    protected function getPaymentStatusMetrics(League $league, ?Season $season): array
    {
        $incomeQuery = Income::where('league_id', $league->id);
        $expenseQuery = Expense::where('league_id', $league->id);

        if ($season) {
            $incomeQuery->where('season_id', $season->id);
            $expenseQuery->where('season_id', $season->id);
        }

        return [
            'income_by_status' => (clone $incomeQuery)
                ->selectRaw('payment_status, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_status')
                ->get()
                ->toArray(),
            'expense_by_status' => (clone $expenseQuery)
                ->selectRaw('payment_status, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_status')
                ->get()
                ->toArray(),
        ];
    }

    /**
     * Items pendientes que requieren atención
     */
    protected function getPendingItems(League $league, ?Season $season): array
    {
        $incomeQuery = Income::where('league_id', $league->id);
        $expenseQuery = Expense::where('league_id', $league->id);

        if ($season) {
            $incomeQuery->where('season_id', $season->id);
            $expenseQuery->where('season_id', $season->id);
        }

        return [
            'overdue_incomes' => (clone $incomeQuery)
                ->where('payment_status', 'overdue')
                ->orWhere(function ($q) {
                    $q->where('payment_status', 'pending')
                      ->where('due_date', '<', now());
                })
                ->count(),
            'pending_confirmations_income' => (clone $incomeQuery)
                ->where('payment_status', 'paid_by_team')
                ->count(),
            'pending_confirmations_admin' => (clone $incomeQuery)
                ->where('payment_status', 'confirmed_by_admin')
                ->count(),
            'pending_approval_expenses' => (clone $expenseQuery)
                ->where('payment_status', 'pending')
                ->count(),
            'ready_for_payment' => (clone $expenseQuery)
                ->where('payment_status', 'ready_for_payment')
                ->count(),
        ];
    }

    /**
     * Transacciones recientes
     */
    protected function getRecentTransactions(League $league, ?Season $season, int $limit = 10): array
    {
        $incomeQuery = Income::where('league_id', $league->id);
        $expenseQuery = Expense::where('league_id', $league->id);

        if ($season) {
            $incomeQuery->where('season_id', $season->id);
            $expenseQuery->where('season_id', $season->id);
        }

        $recentIncomes = (clone $incomeQuery)
            ->with(['team', 'match'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($income) {
                return [
                    'type' => 'income',
                    'id' => $income->id,
                    'description' => $income->description,
                    'amount' => $income->amount,
                    'status' => $income->payment_status,
                    'status_label' => $income->status_label,
                    'date' => $income->created_at,
                ];
            });

        $recentExpenses = (clone $expenseQuery)
            ->with(['referee', 'match'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($expense) {
                return [
                    'type' => 'expense',
                    'id' => $expense->id,
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'status' => $expense->payment_status,
                    'status_label' => $expense->status_label,
                    'date' => $expense->created_at,
                ];
            });

        return $recentIncomes->concat($recentExpenses)
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Alertas financieras
     */
    protected function getFinancialAlerts(League $league, ?Season $season): array
    {
        $alerts = [];

        $incomeQuery = Income::where('league_id', $league->id);
        $expenseQuery = Expense::where('league_id', $league->id);

        if ($season) {
            $incomeQuery->where('season_id', $season->id);
            $expenseQuery->where('season_id', $season->id);
        }

        // Alerta de pagos vencidos
        $overdueCount = (clone $incomeQuery)
            ->where('payment_status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Hay {$overdueCount} pagos vencidos que requieren atención",
                'action' => 'view_overdue',
            ];
        }

        // Alerta de confirmaciones pendientes
        $pendingConfirmations = (clone $incomeQuery)
            ->where('payment_status', 'paid_by_team')
            ->count();

        if ($pendingConfirmations > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Hay {$pendingConfirmations} pagos esperando tu confirmación",
                'action' => 'view_pending_confirmations',
            ];
        }

        // Alerta de egresos pendientes de aprobación
        $pendingExpenses = (clone $expenseQuery)
            ->where('payment_status', 'pending')
            ->count();

        if ($pendingExpenses > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "Hay {$pendingExpenses} egresos pendientes de aprobación",
                'action' => 'view_pending_expenses',
            ];
        }

        return $alerts;
    }

    /**
     * Obtener rango de fechas según el período
     */
    protected function getDateRange(string $period): array
    {
        return match($period) {
            'today' => [Carbon::today(), Carbon::tomorrow()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'all' => [Carbon::parse('2000-01-01'), Carbon::parse('2100-12-31')],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}
