<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte Financiero</title>
    <style>
        @page {
            margin: 15mm 10mm 20mm 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            background: #fff;
        }
        
        /* Header */
        .header {
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 2px solid #4338ca;
            margin-bottom: 15px;
        }
        
        .header .logo {
            font-size: 20px;
            font-weight: bold;
            color: #4338ca;
            margin-bottom: 3px;
        }
        
        .header h1 {
            font-size: 16px;
            color: #1f2937;
            margin-bottom: 2px;
            font-weight: normal;
        }
        
        .header .subtitle {
            font-size: 10px;
            color: #6b7280;
        }
        
        /* Meta Info */
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .meta-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .meta-info td {
            padding: 6px 10px;
            font-size: 8px;
            border: none;
            vertical-align: top;
        }
        
        .meta-info .label {
            color: #6b7280;
            font-weight: normal;
        }
        
        .meta-info .value {
            color: #1f2937;
            font-weight: bold;
        }
        
        /* Summary Cards */
        .summary-section {
            margin-bottom: 15px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            width: 25%;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #e5e7eb;
        }
        
        .summary-table .card-income {
            background: #ecfdf5;
        }
        
        .summary-table .card-expense {
            background: #fef2f2;
        }
        
        .summary-table .card-balance-pos {
            background: #eef2ff;
        }
        
        .summary-table .card-balance-neg {
            background: #fff7ed;
        }
        
        .summary-table .card-label {
            font-size: 7px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: block;
            margin-bottom: 3px;
        }
        
        .summary-table .card-value {
            font-size: 14px;
            font-weight: bold;
            display: block;
        }
        
        .summary-table .card-count {
            font-size: 7px;
            color: #9ca3af;
            display: block;
            margin-top: 2px;
        }
        
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-blue { color: #4338ca; }
        .text-orange { color: #ea580c; }
        
        /* Section Title */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
            padding: 6px 8px;
            margin: 12px 0 8px 0;
            border-left: 3px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .section-title.income {
            border-left-color: #10b981;
            background: #ecfdf5;
        }
        
        .section-title.expense {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        
        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8px;
        }
        
        .data-table th {
            background: #f3f4f6;
            padding: 5px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #d1d5db;
        }
        
        .data-table.income-table th {
            background: #d1fae5;
            color: #065f46;
        }
        
        .data-table.expense-table th {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .data-table td {
            padding: 4px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            word-wrap: break-word;
        }
        
        .data-table tr:nth-child(even) td {
            background: #fafafa;
        }
        
        .data-table .col-date { width: 12%; }
        .data-table .col-league { width: 14%; }
        .data-table .col-team { width: 14%; }
        .data-table .col-type { width: 14%; }
        .data-table .col-desc { width: 18%; }
        .data-table .col-status { width: 12%; }
        .data-table .col-amount { width: 14%; text-align: right; }
        
        .data-table .amount {
            font-weight: bold;
            text-align: right;
            white-space: nowrap;
        }
        
        /* Status Badge */
        .status {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-confirmed_by_admin { background: #dbeafe; color: #1e40af; }
        .status-paid_by_team { background: #e0e7ff; color: #3730a3; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-approved { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; }
        
        /* Total Row */
        .total-row td {
            background: #f3f4f6 !important;
            font-weight: bold;
            border-top: 2px solid #d1d5db;
            padding: 6px 4px;
        }
        
        /* Type Breakdown */
        .breakdown-section {
            margin-top: 10px;
            padding: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        
        .breakdown-title {
            font-size: 9px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .breakdown-table td {
            padding: 3px 0;
            font-size: 8px;
            border: none;
        }
        
        .breakdown-table .type-name { width: 55%; }
        .breakdown-table .type-count { width: 15%; text-align: center; color: #6b7280; }
        .breakdown-table .type-amount { width: 30%; text-align: right; font-weight: bold; }
        
        /* No Data */
        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
            background: #f9fafb;
            border: 1px dashed #e5e7eb;
            border-radius: 4px;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
        }
        
        /* Page Break */
        .page-break {
            page-break-before: always;
        }
        
        /* Truncate text */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">FlowFast</div>
        <h1>Reporte Financiero</h1>
        <div class="subtitle">Análisis de Ingresos y Egresos</div>
    </div>

    <!-- Meta Info -->
    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 50%;">
                    <span class="label">Período:</span> 
                    <span class="value">{{ \Carbon\Carbon::parse($filters['dateFrom'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['dateTo'])->format('d/m/Y') }}</span>
                </td>
                <td style="width: 50%; text-align: right;">
                    <span class="label">Generado:</span> 
                    <span class="value">{{ $generatedAt->format('d/m/Y H:i') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Liga:</span> 
                    <span class="value">{{ $filters['league'] }}</span>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <span class="label">Temporada:</span> 
                    <span class="value">{{ $filters['season'] }}</span>
                </td>
                <td style="text-align: right;">
                    <span class="label">Generado por:</span> 
                    <span class="value">{{ $generatedBy }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary Cards -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                @if($filters['reportType'] !== 'expense')
                <td class="card-income">
                    <span class="card-label">Total Ingresos</span>
                    <span class="card-value text-green">${{ number_format($summary['income']['total'], 2) }}</span>
                    <span class="card-count">{{ $summary['income']['count'] }} registros</span>
                </td>
                <td class="card-income">
                    <span class="card-label">Ingresos Confirmados</span>
                    <span class="card-value text-green">${{ number_format($summary['income']['confirmed'], 2) }}</span>
                </td>
                @endif
                
                @if($filters['reportType'] !== 'income')
                <td class="card-expense">
                    <span class="card-label">Total Egresos</span>
                    <span class="card-value text-red">${{ number_format($summary['expense']['total'], 2) }}</span>
                    <span class="card-count">{{ $summary['expense']['count'] }} registros</span>
                </td>
                @endif
                
                <td class="{{ $summary['balance']['total'] >= 0 ? 'card-balance-pos' : 'card-balance-neg' }}">
                    <span class="card-label">Balance</span>
                    <span class="card-value {{ $summary['balance']['total'] >= 0 ? 'text-blue' : 'text-orange' }}">${{ number_format($summary['balance']['total'], 2) }}</span>
                    <span class="card-count">Confirmado: ${{ number_format($summary['balance']['confirmed'], 2) }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Ingresos Section -->
    @if($filters['reportType'] !== 'expense')
        @if($incomes->count() > 0)
        <div class="section-title income">Detalle de Ingresos ({{ $incomes->count() }} registros)</div>
        <table class="data-table income-table">
            <thead>
                <tr>
                    <th class="col-date">Fecha</th>
                    <th class="col-league">Liga</th>
                    <th class="col-team">Equipo</th>
                    <th class="col-type">Tipo</th>
                    <th class="col-desc">Descripción</th>
                    <th class="col-status">Estado</th>
                    <th class="col-amount">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomes as $income)
                <tr>
                    <td>{{ $income->created_at->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($income->league?->name ?? 'N/A', 15) }}</td>
                    <td>{{ Str::limit($income->team?->name ?? 'N/A', 15) }}</td>
                    <td>{{ $income->type_label }}</td>
                    <td>{{ Str::limit($income->description ?? '-', 20) }}</td>
                    <td><span class="status status-{{ $income->payment_status }}">{{ Str::limit($income->status_label, 12) }}</span></td>
                    <td class="amount text-green">${{ number_format($income->amount, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" style="text-align: right;">TOTAL INGRESOS:</td>
                    <td class="amount text-green">${{ number_format($incomes->sum('amount'), 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        @if($summary['income']['by_type']->count() > 0)
        <div class="breakdown-section">
            <div class="breakdown-title">Resumen por Tipo de Ingreso</div>
            <table class="breakdown-table">
                @php
                    $typeLabels = [
                        'registration_fee' => 'Cuota de Inscripción',
                        'match_fee' => 'Pago por Partido',
                        'penalty_fee' => 'Multa',
                        'late_payment_fee' => 'Recargo',
                        'championship_fee' => 'Cuota de Liguilla',
                        'friendly_match_fee' => 'Pago por Amistoso',
                        'other' => 'Otros',
                    ];
                @endphp
                @foreach($summary['income']['by_type'] as $type => $data)
                <tr>
                    <td class="type-name">{{ $typeLabels[$type] ?? $type }}</td>
                    <td class="type-count">{{ $data['count'] }} reg.</td>
                    <td class="type-amount text-green">${{ number_format($data['total'], 2) }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
        @else
        <div class="no-data">No se encontraron ingresos en el período seleccionado.</div>
        @endif
    @endif

    <!-- Egresos Section -->
    @if($filters['reportType'] !== 'income')
        @if($expenses->count() > 0)
        <div class="section-title expense">Detalle de Egresos ({{ $expenses->count() }} registros)</div>
        <table class="data-table expense-table">
            <thead>
                <tr>
                    <th class="col-date">Fecha</th>
                    <th class="col-league">Liga</th>
                    <th class="col-type">Tipo</th>
                    <th class="col-desc">Descripción</th>
                    <th class="col-team">Beneficiario</th>
                    <th class="col-status">Estado</th>
                    <th class="col-amount">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->created_at->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($expense->league?->name ?? 'N/A', 15) }}</td>
                    <td>{{ $expense->type_label }}</td>
                    <td>{{ Str::limit($expense->description ?? '-', 20) }}</td>
                    <td>{{ Str::limit($expense->beneficiary?->name ?? ($expense->referee?->user?->name ?? 'N/A'), 15) }}</td>
                    <td><span class="status status-{{ $expense->payment_status }}">{{ Str::limit($expense->status_label, 12) }}</span></td>
                    <td class="amount text-red">${{ number_format($expense->amount, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" style="text-align: right;">TOTAL EGRESOS:</td>
                    <td class="amount text-red">${{ number_format($expenses->sum('amount'), 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        @if($summary['expense']['by_type']->count() > 0)
        <div class="breakdown-section">
            <div class="breakdown-title">Resumen por Tipo de Egreso</div>
            <table class="breakdown-table">
                @php
                    $expenseTypeLabels = [
                        'referee_payment' => 'Pago a Árbitro',
                        'venue_rental' => 'Alquiler de Cancha',
                        'equipment' => 'Equipo Deportivo',
                        'maintenance' => 'Mantenimiento',
                        'utilities' => 'Servicios',
                        'staff_salary' => 'Salario de Personal',
                        'marketing' => 'Marketing',
                        'insurance' => 'Seguros',
                        'other' => 'Otros',
                    ];
                @endphp
                @foreach($summary['expense']['by_type'] as $type => $data)
                <tr>
                    <td class="type-name">{{ $expenseTypeLabels[$type] ?? $type }}</td>
                    <td class="type-count">{{ $data['count'] }} reg.</td>
                    <td class="type-amount text-red">${{ number_format($data['total'], 2) }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
        @else
        <div class="no-data">No se encontraron egresos en el período seleccionado.</div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        FlowFast - Sistema de Gestión de Ligas Deportivas | {{ $generatedAt->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
