<?php

namespace App\Livewire\Reports;

use App\Models\Income;
use App\Models\Expense;
use App\Models\League;
use App\Models\Season;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\FinancialReportExport;

class FinancialReport extends Component
{
    use WithPagination;

    // Filtros
    public $reportType = 'all'; // all, income, expense
    public $leagueFilter = '';
    public $seasonFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';
    public $incomeTypeFilter = '';
    public $expenseTypeFilter = '';

    // Vista
    public $viewMode = 'table'; // table, summary

    protected $queryString = [
        'reportType' => ['except' => 'all'],
        'leagueFilter' => ['except' => ''],
        'seasonFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function mount()
    {
        // Verificar permisos
        $user = Auth::user();
        if (!$user || !in_array($user->user_type, ['admin', 'league_manager'])) {
            abort(403, 'Acceso no autorizado');
        }

        // Fechas por defecto: último mes
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getLeaguesProperty()
    {
        $user = Auth::user();
        $query = League::query();

        if ($user->user_type === 'league_manager') {
            $query->where('league_manager_id', $user->userable_id);
        }

        return $query->orderBy('name')->get();
    }

    public function getSeasonsProperty()
    {
        $query = Season::query();

        if ($this->leagueFilter) {
            $query->where('league_id', $this->leagueFilter);
        }

        return $query->orderBy('name')->get();
    }

    public function getIncomesProperty()
    {
        if ($this->reportType === 'expense') {
            return collect();
        }

        $user = Auth::user();
        $query = Income::with(['league', 'team', 'season', 'generatedBy']);

        // Filtro por rol
        if ($user->user_type === 'league_manager') {
            $leagueIds = League::where('league_manager_id', $user->userable_id)->pluck('id');
            $query->whereIn('league_id', $leagueIds);
        }

        // Filtros
        if ($this->leagueFilter) {
            $query->where('league_id', $this->leagueFilter);
        }

        if ($this->seasonFilter) {
            $query->where('season_id', $this->seasonFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->statusFilter) {
            $query->where('payment_status', $this->statusFilter);
        }

        if ($this->incomeTypeFilter) {
            $query->where('income_type', $this->incomeTypeFilter);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getExpensesProperty()
    {
        if ($this->reportType === 'income') {
            return collect();
        }

        $user = Auth::user();
        $query = Expense::with(['league', 'referee', 'season', 'requestedBy', 'approvedBy']);

        // Filtro por rol
        if ($user->user_type === 'league_manager') {
            $leagueIds = League::where('league_manager_id', $user->userable_id)->pluck('id');
            $query->whereIn('league_id', $leagueIds);
        }

        // Filtros
        if ($this->leagueFilter) {
            $query->where('league_id', $this->leagueFilter);
        }

        if ($this->seasonFilter) {
            $query->where('season_id', $this->seasonFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->statusFilter) {
            $query->where('payment_status', $this->statusFilter);
        }

        if ($this->expenseTypeFilter) {
            $query->where('expense_type', $this->expenseTypeFilter);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getSummaryProperty()
    {
        $incomes = $this->incomes;
        $expenses = $this->expenses;

        // Ingresos por estado
        $incomePending = $incomes->whereIn('payment_status', ['pending', 'overdue'])->sum('amount');
        $incomePaidByTeam = $incomes->where('payment_status', 'paid_by_team')->sum('amount');
        $incomeConfirmed = $incomes->whereIn('payment_status', ['confirmed', 'confirmed_by_admin'])->sum('amount');
        $totalIncome = $incomes->sum('amount');

        // Egresos por estado
        $expensePending = $expenses->where('payment_status', 'pending')->sum('amount');
        $expenseApproved = $expenses->where('payment_status', 'approved')->sum('amount');
        $expenseConfirmed = $expenses->where('payment_status', 'confirmed')->sum('amount');
        $totalExpense = $expenses->sum('amount');

        // Por tipo de ingreso
        $incomeByType = $incomes->groupBy('income_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'confirmed' => $group->whereIn('payment_status', ['confirmed', 'confirmed_by_admin'])->sum('amount'),
            ];
        });

        // Por tipo de egreso
        $expenseByType = $expenses->groupBy('expense_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'confirmed' => $group->where('payment_status', 'confirmed')->sum('amount'),
            ];
        });

        return [
            'income' => [
                'total' => $totalIncome,
                'pending' => $incomePending,
                'paid_by_team' => $incomePaidByTeam,
                'confirmed' => $incomeConfirmed,
                'count' => $incomes->count(),
                'by_type' => $incomeByType,
            ],
            'expense' => [
                'total' => $totalExpense,
                'pending' => $expensePending,
                'approved' => $expenseApproved,
                'confirmed' => $expenseConfirmed,
                'count' => $expenses->count(),
                'by_type' => $expenseByType,
            ],
            'balance' => [
                'total' => $totalIncome - $totalExpense,
                'confirmed' => $incomeConfirmed - $expenseConfirmed,
            ],
        ];
    }

    public function resetFilters()
    {
        $this->reportType = 'all';
        $this->leagueFilter = '';
        $this->seasonFilter = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->statusFilter = '';
        $this->incomeTypeFilter = '';
        $this->expenseTypeFilter = '';
    }

    public function exportPdf()
    {
        $data = [
            'incomes' => $this->incomes,
            'expenses' => $this->expenses,
            'summary' => $this->summary,
            'filters' => [
                'reportType' => $this->reportType,
                'league' => $this->leagueFilter ? League::find($this->leagueFilter)?->name : 'Todas',
                'season' => $this->seasonFilter ? Season::find($this->seasonFilter)?->name : 'Todas',
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
            ],
            'generatedAt' => now(),
            'generatedBy' => Auth::user()->name,
        ];

        $pdf = Pdf::loadView('reports.financial-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'reporte-financiero-' . now()->format('Y-m-d-His') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function exportExcel()
    {
        $filename = 'reporte-financiero-' . now()->format('Y-m-d-His') . '.xlsx';
        
        $export = new FinancialReportExport(
            $this->incomes,
            $this->expenses,
            $this->summary,
            [
                'reportType' => $this->reportType,
                'league' => $this->leagueFilter ? League::find($this->leagueFilter)?->name : 'Todas',
                'season' => $this->seasonFilter ? Season::find($this->seasonFilter)?->name : 'Todas',
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
            ]
        );
        
        return $export->download($filename);
    }

    public function render()
    {
        return view('livewire.reports.financial-report', [
            'leagues' => $this->leagues,
            'seasons' => $this->seasons,
            'incomes' => $this->incomes,
            'expenses' => $this->expenses,
            'summary' => $this->summary,
        ])->layout('layouts.app', ['title' => 'Reportes Financieros']);
    }
}
