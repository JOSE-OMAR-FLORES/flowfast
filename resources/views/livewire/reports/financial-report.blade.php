<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">📊 Reportes Financieros</h1>
                    <p class="mt-1 text-sm text-gray-600">Análisis detallado de ingresos y egresos</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="exportExcel" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel
                    </button>
                    <button wire:click="exportPdf" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">🔍 Filtros</h2>
                <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Limpiar filtros
                </button>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Tipo de Reporte -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                    <select wire:model.live="reportType" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">📊 Ingresos y Egresos</option>
                        <option value="income">💰 Solo Ingresos</option>
                        <option value="expense">💸 Solo Egresos</option>
                    </select>
                </div>

                <!-- Liga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Liga</label>
                    <select wire:model.live="leagueFilter" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas las ligas</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Temporada -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temporada</label>
                    <select wire:model.live="seasonFilter" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas las temporadas</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}">{{ $season->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select wire:model.live="statusFilter" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="confirmed">Confirmado</option>
                        @if($reportType !== 'expense')
                            <option value="paid_by_team">Pagado (sin confirmar)</option>
                            <option value="overdue">Vencido</option>
                        @endif
                        @if($reportType !== 'income')
                            <option value="approved">Aprobado</option>
                        @endif
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" wire:model.live="dateFrom" 
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" wire:model.live="dateTo" 
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Tipo de Ingreso -->
                @if($reportType !== 'expense')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ingreso</label>
                    <select wire:model.live="incomeTypeFilter" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="registration_fee">Cuota de Inscripción</option>
                        <option value="match_fee">Pago por Partido</option>
                        <option value="penalty_fee">Multa</option>
                        <option value="late_payment_fee">Recargo</option>
                        <option value="championship_fee">Cuota de Liguilla</option>
                        <option value="other">Otros</option>
                    </select>
                </div>
                @endif

                <!-- Tipo de Egreso -->
                @if($reportType !== 'income')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Egreso</label>
                    <select wire:model.live="expenseTypeFilter" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="referee_payment">Pago a Árbitro</option>
                        <option value="venue_rental">Alquiler de Cancha</option>
                        <option value="equipment">Equipo Deportivo</option>
                        <option value="maintenance">Mantenimiento</option>
                        <option value="utilities">Servicios</option>
                        <option value="staff_salary">Salario de Personal</option>
                        <option value="other">Otros</option>
                    </select>
                </div>
                @endif
            </div>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Ingresos -->
            @if($reportType !== 'expense')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ingresos Totales</p>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($summary['income']['total'], 2) }}</p>
                        <p class="text-xs text-gray-400">{{ $summary['income']['count'] }} registros</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Ingresos Confirmados -->
            @if($reportType !== 'expense')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-emerald-100 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ingresos Confirmados</p>
                        <p class="text-2xl font-bold text-emerald-600">${{ number_format($summary['income']['confirmed'], 2) }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Total Egresos -->
            @if($reportType !== 'income')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Egresos Totales</p>
                        <p class="text-2xl font-bold text-red-600">${{ number_format($summary['expense']['total'], 2) }}</p>
                        <p class="text-xs text-gray-400">{{ $summary['expense']['count'] }} registros</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Balance -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 {{ $summary['balance']['total'] >= 0 ? 'bg-indigo-100' : 'bg-orange-100' }} rounded-lg">
                        <svg class="w-6 h-6 {{ $summary['balance']['total'] >= 0 ? 'text-indigo-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Balance</p>
                        <p class="text-2xl font-bold {{ $summary['balance']['total'] >= 0 ? 'text-indigo-600' : 'text-orange-600' }}">
                            ${{ number_format($summary['balance']['total'], 2) }}
                        </p>
                        <p class="text-xs text-gray-400">Confirmado: ${{ number_format($summary['balance']['confirmed'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs de Vista -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button wire:click="$set('viewMode', 'table')" 
                            class="px-6 py-3 border-b-2 font-medium text-sm {{ $viewMode === 'table' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        📋 Detalle
                    </button>
                    <button wire:click="$set('viewMode', 'summary')" 
                            class="px-6 py-3 border-b-2 font-medium text-sm {{ $viewMode === 'summary' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        📊 Resumen por Tipo
                    </button>
                </nav>
            </div>
        </div>

        <!-- Vista Detalle -->
        @if($viewMode === 'table')
            <!-- Tabla de Ingresos -->
            @if($reportType !== 'expense' && $incomes->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                    <h3 class="text-lg font-semibold text-green-800">💰 Ingresos ({{ $incomes->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liga</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($incomes as $income)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $income->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $income->league?->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $income->team?->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $income->type_label }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">
                                    {{ $income->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                    ${{ number_format($income->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        @if($income->payment_status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($income->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($income->payment_status === 'paid_by_team') bg-blue-100 text-blue-800
                                        @elseif($income->payment_status === 'overdue') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $income->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700">Total Ingresos:</td>
                                <td class="px-4 py-3 text-right font-bold text-green-700">${{ number_format($incomes->sum('amount'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            <!-- Tabla de Egresos -->
            @if($reportType !== 'income' && $expenses->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-red-50 border-b border-red-100">
                    <h3 class="text-lg font-semibold text-red-800">💸 Egresos ({{ $expenses->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liga</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Beneficiario</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($expenses as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $expense->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $expense->league?->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $expense->type_label }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">
                                    {{ $expense->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $expense->beneficiary?->name ?? ($expense->referee?->user?->name ?? 'N/A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-red-600">
                                    ${{ number_format($expense->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        @if($expense->payment_status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($expense->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($expense->payment_status === 'approved') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $expense->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-red-50">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700">Total Egresos:</td>
                                <td class="px-4 py-3 text-right font-bold text-red-700">${{ number_format($expenses->sum('amount'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            @if(($reportType === 'income' && $incomes->count() === 0) || 
                ($reportType === 'expense' && $expenses->count() === 0) ||
                ($reportType === 'all' && $incomes->count() === 0 && $expenses->count() === 0))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay datos</h3>
                <p class="mt-1 text-sm text-gray-500">No se encontraron registros con los filtros seleccionados.</p>
            </div>
            @endif
        @endif

        <!-- Vista Resumen por Tipo -->
        @if($viewMode === 'summary')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Resumen de Ingresos por Tipo -->
                @if($reportType !== 'expense')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                        <h3 class="text-lg font-semibold text-green-800">💰 Ingresos por Tipo</h3>
                    </div>
                    <div class="p-6">
                        @if(count($summary['income']['by_type']) > 0)
                            <div class="space-y-4">
                                @foreach($summary['income']['by_type'] as $type => $data)
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
                                        $percentage = $summary['income']['total'] > 0 ? ($data['total'] / $summary['income']['total']) * 100 : 0;
                                    @endphp
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $typeLabels[$type] ?? $type }}</span>
                                            <span class="text-sm font-semibold text-green-600">${{ number_format($data['total'], 2) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <span>{{ $data['count'] }} registros</span>
                                            <span>{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No hay ingresos en este período</p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Resumen de Egresos por Tipo -->
                @if($reportType !== 'income')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-red-50 border-b border-red-100">
                        <h3 class="text-lg font-semibold text-red-800">💸 Egresos por Tipo</h3>
                    </div>
                    <div class="p-6">
                        @if(count($summary['expense']['by_type']) > 0)
                            <div class="space-y-4">
                                @foreach($summary['expense']['by_type'] as $type => $data)
                                    @php
                                        $typeLabels = [
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
                                        $percentage = $summary['expense']['total'] > 0 ? ($data['total'] / $summary['expense']['total']) * 100 : 0;
                                    @endphp
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $typeLabels[$type] ?? $type }}</span>
                                            <span class="text-sm font-semibold text-red-600">${{ number_format($data['total'], 2) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <span>{{ $data['count'] }} registros</span>
                                            <span>{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No hay egresos en este período</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        @endif

    </div>
</div>
