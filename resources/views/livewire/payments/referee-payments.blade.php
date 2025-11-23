<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pagos a Árbitros</h1>
            <p class="mt-2 text-sm text-gray-600">Gestiona los pagos pendientes a los árbitros</p>
        </div>

        <!-- Filtros -->
        <div class="mb-6 flex flex-wrap gap-4">
            <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Todos los estados</option>
                <option value="pending">Pendientes de aprobación</option>
                <option value="approved">Aprobados</option>
                <option value="ready_for_payment">Listos para pagar</option>
                <option value="confirmed">Confirmados/Pagados</option>
            </select>

            <select wire:model.live="expenseTypeFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Todos los tipos</option>
                <option value="referee_payment">Pago por arbitraje</option>
                <option value="referee_bonus">Bonos</option>
                <option value="referee_travel">Viáticos</option>
            </select>

            @if(auth()->user()->role === 'admin')
                <select wire:model.live="leagueId" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las ligas</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- Mensajes Flash -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-red-800">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Lista de Pagos -->
        <div class="space-y-4">
            @forelse($expenses as $expense)
                <div wire:key="expense-{{ $expense->id }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Información del Pago -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $expense->description }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        @if($expense->referee)
                                            Árbitro: {{ $expense->referee->first_name }} {{ $expense->referee->last_name }}
                                        @else
                                            Sin árbitro asignado
                                        @endif
                                         - {{ $expense->league->name }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $expense->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $expense->payment_status === 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $expense->payment_status === 'ready_for_payment' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $expense->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $expense->payment_status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <p class="text-xs text-gray-500">Monto</p>
                                    <p class="text-lg font-bold text-blue-600">${{ number_format($expense->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Fecha límite</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $expense->due_date?->format('d/m/Y') ?? 'N/A' }}</p>
                                </div>
                                @if($expense->payment_method)
                                    <div>
                                        <p class="text-xs text-gray-500">Método de pago</p>
                                        <p class="text-sm font-medium text-gray-900 capitalize">{{ $expense->payment_method }}</p>
                                    </div>
                                @endif
                                @if($expense->paid_at)
                                    <div>
                                        <p class="text-xs text-gray-500">Pagado el</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $expense->paid_at->format('d/m/Y') }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($expense->fixture)
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500">Partido</p>
                                    <p class="text-sm text-gray-700">
                                        {{ $expense->fixture->homeTeam->name ?? 'N/A' }} vs {{ $expense->fixture->awayTeam->name ?? 'N/A' }}
                                        - {{ $expense->fixture->match_date?->format('d/m/Y') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-col gap-2 min-w-[220px]">
                            @if($expense->payment_status === 'pending')
                                <!-- Aprobar -->
                                <button wire:click="markAsApproved({{ $expense->id }})"
                                        wire:confirm="¿Aprobar este pago?"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Aprobar Pago
                                </button>
                            @elseif($expense->payment_status === 'approved')
                                <!-- Marcar como listo para pagar -->
                                <button wire:click="markAsReadyForPayment({{ $expense->id }})"
                                        wire:confirm="¿Marcar como listo para pagar?"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Listo para Pagar
                                </button>
                            @elseif($expense->payment_status === 'ready_for_payment')
                                <!-- Opciones de Pago -->
                                <div class="flex flex-col gap-2">
                                    <livewire:payments.stripe-expense-payment :expenseId="$expense->id" :key="'stripe-expense-'.$expense->id" />
                                    <livewire:payments.cash-expense-payment :expenseId="$expense->id" :key="'cash-expense-'.$expense->id" />
                                    <livewire:payments.transfer-expense-payment :expenseId="$expense->id" :key="'transfer-expense-'.$expense->id" />
                                </div>
                            @elseif($expense->payment_status === 'confirmed')
                                <div class="text-center">
                                    <div class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 rounded-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Pago confirmado
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay pagos pendientes</h3>
                    <p class="text-gray-500">No se encontraron pagos a árbitros con los filtros seleccionados.</p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
