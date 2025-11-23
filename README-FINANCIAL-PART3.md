# 💰 FlowFast SaaS - Sistema Financiero (Parte 3)

## 📊 Reportes y Analytics Financieros

### **Enlaces de Navegación:**
- ← [Parte 1: Fundamentos](README-FINANCIAL-PART1.md)
- ← [Parte 2: Servicios](README-FINANCIAL-PART2.md)
- → [Parte 4: Membresías SaaS](README-FINANCIAL-PART4.md)

---

## 📈 Dashboard Financiero Interactivo

### **🎯 Métricas Principales**

```php
<?php
// app/Services/FinancialDashboardService.php
namespace App\Services;

use App\Models\League;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class FinancialDashboardService
{
    /**
     * Obtener métricas principales del dashboard
     */
    public function getDashboardMetrics(League $league, ?string $period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'summary' => $this->getSummaryMetrics($league, $dateRange),
            'income_breakdown' => $this->getIncomeBreakdown($league, $dateRange),
            'expense_breakdown' => $this->getExpenseBreakdown($league, $dateRange),
            'payment_status' => $this->getPaymentStatusMetrics($league),
            'trends' => $this->getTrendData($league, $period),
            'alerts' => $this->getFinancialAlerts($league),
        ];
    }

    private function getSummaryMetrics(League $league, array $dateRange): array
    {
        $totalIncome = Income::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange)
            ->sum('amount');

        $totalExpenses = Expense::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange)
            ->sum('amount');

        $pendingIncome = Income::where('league_id', $league->id)
            ->where('payment_status', 'pending')
            ->sum('amount');

        $pendingExpenses = Expense::where('league_id', $league->id)
            ->whereIn('payment_status', ['pending', 'ready_for_payment'])
            ->sum('amount');

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalIncome - $totalExpenses,
            'pending_income' => $pendingIncome,
            'pending_expenses' => $pendingExpenses,
            'available_balance' => $totalIncome - $totalExpenses - $pendingExpenses,
            'profit_margin' => $totalIncome > 0 ? (($totalIncome - $totalExpenses) / $totalIncome) * 100 : 0,
        ];
    }

    private function getIncomeBreakdown(League $league, array $dateRange): array
    {
        return Income::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange)
            ->selectRaw('income_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('income_type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->income_type,
                    'label' => $this->getIncomeTypeLabel($item->income_type),
                    'total' => $item->total,
                    'count' => $item->count,
                    'average' => $item->count > 0 ? $item->total / $item->count : 0,
                ];
            })
            ->toArray();
    }

    private function getExpenseBreakdown(League $league, array $dateRange): array
    {
        return Expense::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->whereBetween('confirmed_at', $dateRange)
            ->selectRaw('expense_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('expense_type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->expense_type,
                    'label' => $this->getExpenseTypeLabel($item->expense_type),
                    'total' => $item->total,
                    'count' => $item->count,
                    'average' => $item->count > 0 ? $item->total / $item->count : 0,
                ];
            })
            ->toArray();
    }

    private function getTrendData(League $league, string $period): array
    {
        $days = $period === 'year' ? 365 : ($period === 'month' ? 30 : 7);
        $groupBy = $period === 'year' ? 'MONTH' : 'DAY';
        
        $income_trend = Income::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->where('confirmed_at', '>=', now()->subDays($days))
            ->selectRaw("DATE({$groupBy}(confirmed_at)) as date, SUM(amount) as total")
            ->groupByRaw("DATE({$groupBy}(confirmed_at))")
            ->orderBy('date')
            ->get();

        $expense_trend = Expense::where('league_id', $league->id)
            ->where('payment_status', 'confirmed')
            ->where('confirmed_at', '>=', now()->subDays($days))
            ->selectRaw("DATE({$groupBy}(confirmed_at)) as date, SUM(amount) as total")
            ->groupByRaw("DATE({$groupBy}(confirmed_at))")
            ->orderBy('date')
            ->get();

        return [
            'income' => $income_trend,
            'expenses' => $expense_trend,
            'net_flow' => $this->calculateNetFlow($income_trend, $expense_trend),
        ];
    }

    private function getFinancialAlerts(League $league): array
    {
        $alerts = [];

        // Alertas de pagos vencidos
        $overdueCount = Income::where('league_id', $league->id)
            ->where('payment_status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Pagos Vencidos',
                'message' => "{$overdueCount} pagos están vencidos",
                'action_url' => '/dashboard/finances/overdue',
            ];
        }

        // Alerta de balance bajo
        $metrics = $this->getSummaryMetrics($league, $this->getDateRange('month'));
        if ($metrics['available_balance'] < 500) { // Umbral configurable
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Balance Bajo',
                'message' => 'El balance disponible está por debajo del umbral mínimo',
                'action_url' => '/dashboard/finances/balance',
            ];
        }

        return $alerts;
    }
}
```

### **📊 Componente Livewire del Dashboard**

```php
<?php
// app/Livewire/FinancialDashboard.php
namespace App\Livewire;

use Livewire\Component;
use App\Services\FinancialDashboardService;

class FinancialDashboard extends Component
{
    public $league;
    public $period = 'month';
    public $metrics = [];
    
    protected $listeners = [
        'financial-metrics-updated' => 'refreshMetrics',
        'period-changed' => 'updatePeriod',
    ];

    public function mount()
    {
        $this->refreshMetrics();
    }

    public function refreshMetrics()
    {
        $dashboardService = new FinancialDashboardService();
        $this->metrics = $dashboardService->getDashboardMetrics($this->league, $this->period);
    }

    public function updatePeriod($newPeriod)
    {
        $this->period = $newPeriod;
        $this->refreshMetrics();
    }

    public function render()
    {
        return view('livewire.financial-dashboard');
    }
}
```

### **🎨 Vista del Dashboard Financiero**

```blade
{{-- resources/views/livewire/financial-dashboard.blade.php --}}
<div class="space-y-6">
    {{-- Header con Filtros --}}
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Dashboard Financiero</h2>
        
        <div class="flex space-x-2">
            <select wire:model="period" wire:change="refreshMetrics" class="rounded-md border-gray-300">
                <option value="week">Esta Semana</option>
                <option value="month">Este Mes</option>
                <option value="year">Este Año</option>
            </select>
            
            <button wire:click="refreshMetrics" class="btn-secondary">
                <x-icon name="refresh" class="w-4 h-4" />
            </button>
        </div>
    </div>

    {{-- Alertas --}}
    @if(count($metrics['alerts'] ?? []) > 0)
        <div class="space-y-2">
            @foreach($metrics['alerts'] as $alert)
                <div class="alert alert-{{ $alert['type'] }} flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold">{{ $alert['title'] }}</h4>
                        <p class="text-sm">{{ $alert['message'] }}</p>
                    </div>
                    <a href="{{ $alert['action_url'] }}" class="btn-sm btn-primary">
                        Ver Detalles
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Métricas Principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Ingresos --}}
        <div class="stat-card bg-green-50 border-green-200">
            <div class="stat-icon bg-green-100 text-green-600">
                <x-icon name="currency-dollar" class="w-6 h-6" />
            </div>
            <div class="stat-content">
                <p class="stat-label">Ingresos Totales</p>
                <p class="stat-value text-green-600">
                    ${{ number_format($metrics['summary']['total_income'] ?? 0, 2) }}
                </p>
            </div>
        </div>

        {{-- Total Gastos --}}
        <div class="stat-card bg-red-50 border-red-200">
            <div class="stat-icon bg-red-100 text-red-600">
                <x-icon name="credit-card" class="w-6 h-6" />
            </div>
            <div class="stat-content">
                <p class="stat-label">Gastos Totales</p>
                <p class="stat-value text-red-600">
                    ${{ number_format($metrics['summary']['total_expenses'] ?? 0, 2) }}
                </p>
            </div>
        </div>

        {{-- Balance Disponible --}}
        <div class="stat-card bg-blue-50 border-blue-200">
            <div class="stat-icon bg-blue-100 text-blue-600">
                <x-icon name="banknotes" class="w-6 h-6" />
            </div>
            <div class="stat-content">
                <p class="stat-label">Balance Disponible</p>
                <p class="stat-value text-blue-600">
                    ${{ number_format($metrics['summary']['available_balance'] ?? 0, 2) }}
                </p>
            </div>
        </div>

        {{-- Ganancia Neta --}}
        <div class="stat-card {{ ($metrics['summary']['net_profit'] ?? 0) >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-orange-50 border-orange-200' }}">
            <div class="stat-icon {{ ($metrics['summary']['net_profit'] ?? 0) >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600' }}">
                <x-icon name="{{ ($metrics['summary']['net_profit'] ?? 0) >= 0 ? 'trending-up' : 'trending-down' }}" class="w-6 h-6" />
            </div>
            <div class="stat-content">
                <p class="stat-label">Ganancia Neta</p>
                <p class="stat-value {{ ($metrics['summary']['net_profit'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-orange-600' }}">
                    ${{ number_format($metrics['summary']['net_profit'] ?? 0, 2) }}
                </p>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Gráfico de Tendencias --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tendencias Financieras</h3>
            </div>
            <div class="card-body">
                <canvas id="trendsChart" wire:ignore></canvas>
            </div>
        </div>

        {{-- Distribución de Ingresos --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Distribución de Ingresos</h3>
            </div>
            <div class="card-body">
                <canvas id="incomeChart" wire:ignore></canvas>
            </div>
        </div>
    </div>

    {{-- Tablas de Detalles --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Breakdown de Ingresos --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Desglose de Ingresos</h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    @foreach($metrics['income_breakdown'] ?? [] as $income)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium">{{ $income['label'] }}</p>
                                <p class="text-sm text-gray-500">{{ $income['count'] }} transacciones</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600">${{ number_format($income['total'], 2) }}</p>
                                <p class="text-sm text-gray-500">Prom: ${{ number_format($income['average'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Breakdown de Gastos --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Desglose de Gastos</h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    @foreach($metrics['expense_breakdown'] ?? [] as $expense)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium">{{ $expense['label'] }}</p>
                                <p class="text-sm text-gray-500">{{ $expense['count'] }} transacciones</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-red-600">${{ number_format($expense['total'], 2) }}</p>
                                <p class="text-sm text-gray-500">Prom: ${{ number_format($expense['average'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('livewire:initialized', function() {
    let trendsChart;
    let incomeChart;

    function initCharts() {
        // Gráfico de Tendencias
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: @json(array_column($metrics['trends']['income'] ?? [], 'date')),
                datasets: [{
                    label: 'Ingresos',
                    data: @json(array_column($metrics['trends']['income'] ?? [], 'total')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Gastos',
                    data: @json(array_column($metrics['trends']['expenses'] ?? [], 'total')),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(0);
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Distribución de Ingresos
        const incomeCtx = document.getElementById('incomeChart').getContext('2d');
        incomeChart = new Chart(incomeCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_column($metrics['income_breakdown'] ?? [], 'label')),
                datasets: [{
                    data: @json(array_column($metrics['income_breakdown'] ?? [], 'total')),
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#06b6d4'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    initCharts();

    // Actualizar gráficos cuando cambien los datos
    Livewire.on('financial-metrics-updated', function() {
        if (trendsChart) trendsChart.destroy();
        if (incomeChart) incomeChart.destroy();
        setTimeout(initCharts, 100);
    });
});
</script>
@endpush
```

---

## 📋 Sistema de Reportes Detallados

### **🎯 Servicio de Reportes**

```php
<?php
// app/Services/FinancialReportService.php
namespace App\Services;

use App\Models\League;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class FinancialReportService
{
    /**
     * Generar reporte financiero completo
     */
    public function generateFullReport(League $league, array $options = []): array
    {
        $startDate = Carbon::parse($options['start_date'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($options['end_date'] ?? now()->endOfMonth());
        
        return [
            'header' => $this->getReportHeader($league, $startDate, $endDate),
            'summary' => $this->getReportSummary($league, $startDate, $endDate),
            'income_detail' => $this->getIncomeDetail($league, $startDate, $endDate),
            'expense_detail' => $this->getExpenseDetail($league, $startDate, $endDate),
            'team_payments' => $this->getTeamPaymentStatus($league),
            'referee_payments' => $this->getRefereePaymentStatus($league),
            'reconciliation' => $this->getReconciliationData($league, $startDate, $endDate),
            'forecasting' => $this->getFinancialForecasting($league),
        ];
    }

    private function getReportHeader(League $league, Carbon $start, Carbon $end): array
    {
        return [
            'league_name' => $league->name,
            'report_type' => 'Reporte Financiero Completo',
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'days' => $start->diffInDays($end) + 1,
            ],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => auth()->user()->name ?? 'Sistema',
        ];
    }

    private function getReportSummary(League $league, Carbon $start, Carbon $end): array
    {
        $incomes = Income::where('league_id', $league->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $expenses = Expense::where('league_id', $league->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return [
            'income' => [
                'total' => $incomes->where('payment_status', 'confirmed')->sum('amount'),
                'pending' => $incomes->where('payment_status', 'pending')->sum('amount'),
                'overdue' => $incomes->where('payment_status', 'overdue')->sum('amount'),
                'count' => $incomes->count(),
            ],
            'expenses' => [
                'total' => $expenses->where('payment_status', 'confirmed')->sum('amount'),
                'pending' => $expenses->whereIn('payment_status', ['pending', 'ready_for_payment'])->sum('amount'),
                'count' => $expenses->count(),
            ],
            'balance' => [
                'opening' => $this->getOpeningBalance($league, $start),
                'closing' => $this->getClosingBalance($league, $end),
                'net_change' => $incomes->where('payment_status', 'confirmed')->sum('amount') - 
                              $expenses->where('payment_status', 'confirmed')->sum('amount'),
            ],
        ];
    }

    private function getTeamPaymentStatus(League $league): array
    {
        return $league->teams->map(function ($team) {
            $totalDue = $team->incomes()->where('payment_status', 'pending')->sum('amount');
            $totalPaid = $team->incomes()->where('payment_status', 'confirmed')->sum('amount');
            $overdueDue = $team->incomes()
                ->where('payment_status', 'pending')
                ->where('due_date', '<', now())
                ->sum('amount');

            return [
                'team_name' => $team->name,
                'total_paid' => $totalPaid,
                'total_pending' => $totalDue,
                'overdue_amount' => $overdueDue,
                'payment_ratio' => $totalPaid + $totalDue > 0 ? 
                                 ($totalPaid / ($totalPaid + $totalDue)) * 100 : 100,
                'status' => $overdueDue > 0 ? 'overdue' : ($totalDue > 0 ? 'pending' : 'up_to_date'),
            ];
        })->toArray();
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportToExcel(League $league, array $options = []): string
    {
        $report = $this->generateFullReport($league, $options);
        
        $filename = 'financial_report_' . $league->slug . '_' . now()->format('Y_m_d') . '.xlsx';
        $filepath = storage_path('app/reports/' . $filename);
        
        // Asegurar que el directorio existe
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Hoja de Resumen
        $this->createSummarySheet($spreadsheet, $report);
        
        // Hoja de Ingresos
        $this->createIncomeSheet($spreadsheet, $report);
        
        // Hoja de Gastos
        $this->createExpenseSheet($spreadsheet, $report);
        
        // Hoja de Estados por Equipo
        $this->createTeamStatusSheet($spreadsheet, $report);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filename;
    }

    private function createSummarySheet($spreadsheet, array $report): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
        
        // Headers del reporte
        $sheet->setCellValue('A1', $report['header']['league_name']);
        $sheet->setCellValue('A2', $report['header']['report_type']);
        $sheet->setCellValue('A3', 'Período: ' . $report['header']['period']['start'] . ' - ' . $report['header']['period']['end']);
        
        // Estilos para el header
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);
        
        // Resumen financiero
        $row = 5;
        $sheet->setCellValue('A' . $row, 'RESUMEN FINANCIERO');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        
        $row += 2;
        $summaryData = [
            ['Concepto', 'Confirmado', 'Pendiente', 'Total'],
            ['Ingresos', $report['summary']['income']['total'], $report['summary']['income']['pending'], $report['summary']['income']['total'] + $report['summary']['income']['pending']],
            ['Gastos', $report['summary']['expenses']['total'], $report['summary']['expenses']['pending'], $report['summary']['expenses']['total'] + $report['summary']['expenses']['pending']],
            ['Balance Neto', $report['summary']['balance']['net_change'], '', $report['summary']['balance']['net_change']],
        ];
        
        foreach ($summaryData as $rowData) {
            $col = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Aplicar formato de moneda a las columnas numéricas
        $sheet->getStyle('B' . ($row - 4) . ':D' . ($row - 1))
              ->getNumberFormat()
              ->setFormatCode('#,##0.00');
    }
}
```

---

## 📊 Componente de Reportes

### **📈 Livewire Component para Reportes**

```php
<?php
// app/Livewire/FinancialReports.php
namespace App\Livewire;

use Livewire\Component;
use App\Services\FinancialReportService;
use Carbon\Carbon;

class FinancialReports extends Component
{
    public $league;
    public $reportType = 'summary';
    public $startDate;
    public $endDate;
    public $isGenerating = false;
    
    protected $rules = [
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
    ];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function generateReport()
    {
        $this->validate();
        
        $this->isGenerating = true;
        
        try {
            $reportService = new FinancialReportService();
            
            if ($this->reportType === 'excel') {
                $filename = $reportService->exportToExcel($this->league, [
                    'start_date' => $this->startDate,
                    'end_date' => $this->endDate,
                ]);
                
                $this->dispatch('download-file', [
                    'url' => route('reports.download', ['filename' => $filename]),
                    'filename' => $filename
                ]);
                
                session()->flash('message', 'Reporte generado exitosamente');
            } else {
                $report = $reportService->generateFullReport($this->league, [
                    'start_date' => $this->startDate,
                    'end_date' => $this->endDate,
                ]);
                
                $this->dispatch('report-generated', $report);
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error generando el reporte: ' . $e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function setQuickPeriod($period)
    {
        switch ($period) {
            case 'this_week':
                $this->startDate = now()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = now()->startOfYear()->format('Y-m-d');
                $this->endDate = now()->endOfYear()->format('Y-m-d');
                break;
        }
    }

    public function render()
    {
        return view('livewire.financial-reports');
    }
}
```

### **🎨 Vista de Reportes**

```blade
{{-- resources/views/livewire/financial-reports.blade.php --}}
<div class="space-y-6">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Generador de Reportes Financieros</h2>
        </div>
        
        <div class="card-body space-y-4">
            {{-- Selección de Período --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Fecha de Inicio</label>
                    <input type="date" wire:model="startDate" class="form-input">
                    @error('startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="form-label">Fecha de Fin</label>
                    <input type="date" wire:model="endDate" class="form-input">
                    @error('endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="form-label">Tipo de Reporte</label>
                    <select wire:model="reportType" class="form-select">
                        <option value="summary">Resumen</option>
                        <option value="detailed">Detallado</option>
                        <option value="excel">Exportar Excel</option>
                    </select>
                </div>
            </div>

            {{-- Períodos Predefinidos --}}
            <div class="flex flex-wrap gap-2">
                <button wire:click="setQuickPeriod('this_week')" class="btn-outline btn-sm">
                    Esta Semana
                </button>
                <button wire:click="setQuickPeriod('this_month')" class="btn-outline btn-sm">
                    Este Mes
                </button>
                <button wire:click="setQuickPeriod('last_month')" class="btn-outline btn-sm">
                    Mes Pasado
                </button>
                <button wire:click="setQuickPeriod('this_year')" class="btn-outline btn-sm">
                    Este Año
                </button>
            </div>

            {{-- Botón Generar --}}
            <div class="flex justify-end">
                <button 
                    wire:click="generateReport" 
                    wire:loading.attr="disabled"
                    class="btn-primary"
                >
                    <div wire:loading wire:target="generateReport" class="animate-spin mr-2">
                        <x-icon name="refresh" class="w-4 h-4" />
                    </div>
                    {{ $isGenerating ? 'Generando...' : 'Generar Reporte' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Área de Visualización del Reporte --}}
    <div id="report-container" class="hidden">
        <!-- El reporte se carga aquí dinámicamente -->
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', function() {
    Livewire.on('report-generated', function(report) {
        // Mostrar el reporte generado
        const container = document.getElementById('report-container');
        container.innerHTML = formatReport(report);
        container.classList.remove('hidden');
    });

    Livewire.on('download-file', function(data) {
        // Descargar archivo
        const link = document.createElement('a');
        link.href = data.url;
        link.download = data.filename;
        link.click();
    });

    function formatReport(report) {
        return `
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">${report.header.league_name} - ${report.header.report_type}</h3>
                    <p class="text-sm text-gray-500">Período: ${report.header.period.start} - ${report.header.period.end}</p>
                </div>
                
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="stat-card bg-green-50 border-green-200">
                            <h4 class="font-semibold text-green-800">Ingresos Totales</h4>
                            <p class="text-2xl font-bold text-green-600">$${report.summary.income.total.toFixed(2)}</p>
                            <p class="text-sm text-green-600">Pendiente: $${report.summary.income.pending.toFixed(2)}</p>
                        </div>
                        
                        <div class="stat-card bg-red-50 border-red-200">
                            <h4 class="font-semibold text-red-800">Gastos Totales</h4>
                            <p class="text-2xl font-bold text-red-600">$${report.summary.expenses.total.toFixed(2)}</p>
                            <p class="text-sm text-red-600">Pendiente: $${report.summary.expenses.pending.toFixed(2)}</p>
                        </div>
                        
                        <div class="stat-card bg-blue-50 border-blue-200">
                            <h4 class="font-semibold text-blue-800">Balance Neto</h4>
                            <p class="text-2xl font-bold ${report.summary.balance.net_change >= 0 ? 'text-blue-600' : 'text-orange-600'}">
                                $${report.summary.balance.net_change.toFixed(2)}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Aquí se agregarían más secciones del reporte -->
                </div>
            </div>
        `;
    }
});
</script>
@endpush
```

---

## 🚀 Próximos Pasos - Parte 4

En la **Parte 4 (Final)** cubriremos:

1. **💳 Sistema de Membresías SaaS**
2. **🏢 Gestión Multi-Liga**
3. **💰 Revenue Sharing y Comisiones**
4. **📊 Analytics de Negocio**
5. **🔧 Configuración y Personalización**

---

*¡El sistema de reportes te dará visibilidad completa de las finanzas de tu liga!* 📊✨