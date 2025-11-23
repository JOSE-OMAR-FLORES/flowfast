<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">💰 Dashboard Financiero</h1>
            <p class="text-gray-600 mt-1">{{ $league->name }}</p>
        </div>
        
        <div class="flex gap-3">
            {{-- Botones de Acceso Rápido --}}
            <a href="{{ route('financial.income.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ingresos
            </a>
            
            <a href="{{ route('financial.expense.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
                Gastos
            </a>
            
            {{-- Filtro de Temporada --}}
            <select wire:model.live="seasonId" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="">Todas las temporadas</option>
                @foreach($seasons as $season)
                    <option value="{{ $season->id }}">{{ $season->name }}</option>
                @endforeach
            </select>
            
            {{-- Filtro de Período --}}
            <select wire:model.live="period" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="today">Hoy</option>
                <option value="week">Esta Semana</option>
                <option value="month">Este Mes</option>
                <option value="year">Este Año</option>
                <option value="all">Todo el Tiempo</option>
            </select>
        </div>
    </div>

    {{-- Alertas --}}
    @if(!empty($metrics['alerts']))
        <div class="space-y-2">
            @foreach($metrics['alerts'] as $alert)
                <div class="bg-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-50 border border-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-200 rounded-lg p-4">
                    <p class="text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'blue') }}-800 font-medium">
                        {{ $alert['message'] }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Resumen Principal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Ingresos --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Ingresos</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($metrics['summary']['total_income'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-green-100 text-sm mt-4">
                Pendiente: ${{ number_format($metrics['summary']['pending_income'] ?? 0, 2) }}
            </p>
        </div>

        {{-- Total Egresos --}}
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Total Egresos</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($metrics['summary']['total_expenses'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-red-100 text-sm mt-4">
                Pendiente: ${{ number_format($metrics['summary']['pending_expenses'] ?? 0, 2) }}
            </p>
        </div>

        {{-- Utilidad Neta --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Utilidad Neta</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($metrics['summary']['net_profit'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-blue-100 text-sm mt-4">
                Margen: {{ number_format($metrics['summary']['profit_margin'] ?? 0, 1) }}%
            </p>
        </div>

        {{-- Balance Disponible --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Balance Disponible</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($metrics['summary']['available_balance'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Desglose de Ingresos y Egresos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Desglose de Ingresos --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">📈 Ingresos por Tipo</h3>
            <div class="space-y-3">
                @forelse($metrics['income_breakdown'] ?? [] as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item['label'] }}</p>
                            <p class="text-sm text-gray-600">{{ $item['count'] }} transacciones</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600">${{ number_format($item['total'], 2) }}</p>
                            <p class="text-xs text-gray-500">Prom: ${{ number_format($item['average'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hay ingresos en este período</p>
                @endforelse
            </div>
        </div>

        {{-- Desglose de Egresos --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">📉 Egresos por Tipo</h3>
            <div class="space-y-3">
                @forelse($metrics['expense_breakdown'] ?? [] as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item['label'] }}</p>
                            <p class="text-sm text-gray-600">{{ $item['count'] }} transacciones</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-red-600">${{ number_format($item['total'], 2) }}</p>
                            <p class="text-xs text-gray-500">Prom: ${{ number_format($item['average'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hay egresos en este período</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Items Pendientes --}}
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">⏳ Items Pendientes</h3>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-red-600">{{ $metrics['pending_items']['overdue_incomes'] ?? 0 }}</p>
                <p class="text-sm text-red-700 mt-1">Pagos Vencidos</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-yellow-600">{{ $metrics['pending_items']['pending_confirmations_income'] ?? 0 }}</p>
                <p class="text-sm text-yellow-700 mt-1">Esperando Confirmación</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $metrics['pending_items']['pending_confirmations_admin'] ?? 0 }}</p>
                <p class="text-sm text-blue-700 mt-1">Validación Admin</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-orange-600">{{ $metrics['pending_items']['pending_approval_expenses'] ?? 0 }}</p>
                <p class="text-sm text-orange-700 mt-1">Egresos por Aprobar</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-purple-600">{{ $metrics['pending_items']['ready_for_payment'] ?? 0 }}</p>
                <p class="text-sm text-purple-700 mt-1">Listos para Pagar</p>
            </div>
        </div>
    </div>

    {{-- Transacciones Recientes --}}
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">🕒 Transacciones Recientes</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($metrics['recent_transactions'] ?? [] as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transaction['type'] === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction['type'] === 'income' ? '📥 Ingreso' : '📤 Egreso' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $transaction['description'] }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold {{ $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    ${{ number_format($transaction['amount'], 2) }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $transaction['status_label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No hay transacciones recientes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
