<div class="min-h-screen bg-gray-50 py-8" wire:poll.10s>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pagos del Equipo</h1>
            <p class="mt-2 text-sm text-gray-600">Gestiona los pagos y cuotas de tu equipo</p>
        </div>

        <!-- Filtros -->
        <div class="mb-6 flex flex-wrap gap-4">
            <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Todos los pagos</option>
                <option value="pending">Pendientes</option>
                <option value="paid_by_team">Esperando confirmación</option>
                <option value="confirmed">Confirmados</option>
                <option value="overdue">Vencidos</option>
            </select>
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
            @forelse($incomes as $income)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Información del Pago -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $income->description }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $income->team->name }} - {{ $income->league->name }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $income->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $income->payment_status === 'pending_confirmation' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $income->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $income->payment_status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $income->payment_status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <p class="text-xs text-gray-500">Monto</p>
                                    <p class="text-lg font-bold text-blue-600">${{ number_format($income->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Fecha límite</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $income->due_date?->format('d/m/Y') ?? 'N/A' }}</p>
                                </div>
                                @if($income->payment_method)
                                    <div>
                                        <p class="text-xs text-gray-500">Método de pago</p>
                                        <p class="text-sm font-medium text-gray-900 capitalize">{{ $income->payment_method }}</p>
                                    </div>
                                @endif
                                @if($income->paid_at)
                                    <div>
                                        <p class="text-xs text-gray-500">Pagado el</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $income->paid_at->format('d/m/Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        @if($income->payment_status === 'pending' || $income->payment_status === 'overdue')
                            <div class="flex items-center justify-end gap-2">
                                <div x-data="{ showPayments: false }" class="relative">
                                    <button @click="showPayments = !showPayments"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span x-text="showPayments ? 'Ocultar Métodos' : 'Pagar Ahora'">Pagar Ahora</span>
                                        <svg class="w-4 h-4 transition-transform duration-200" 
                                             :class="{ 'rotate-180': showPayments }"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    {{-- Panel desplegable --}}
                                    <div x-show="showPayments"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 transform scale-100"
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl border border-gray-200 z-50 p-4"
                                         @click.away="showPayments = false"
                                         style="display: none;">
                                        <div class="flex flex-col gap-3">
                                            <div class="flex items-center justify-between border-b pb-2">
                                                <h3 class="text-sm font-bold text-gray-900">Métodos de Pago</h3>
                                                <button @click="showPayments = false" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <livewire:payments.stripe-team-payment :incomeId="$income->id" :amount="$income->amount" :key="'stripe-team-'.$income->id" />
                                                <livewire:payments.cash-team-payment :incomeId="$income->id" :key="'cash-team-'.$income->id" />
                                                <livewire:payments.transfer-team-payment :incomeId="$income->id" :key="'transfer-team-'.$income->id" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($income->payment_status === 'paid_by_team' || $income->payment_status === 'confirmed_by_admin')
                            <div class="text-center min-w-[200px]">
                                <div class="inline-flex items-center px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Esperando confirmación
                                </div>
                                @if($income->payment_method)
                                    <p class="text-xs text-gray-500 mt-1">Método: {{ ucfirst($income->payment_method) }}</p>
                                @endif
                            </div>
                        @elseif($income->payment_status === 'confirmed')
                            <div class="text-center min-w-[200px]">
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
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay pagos</h3>
                    <p class="text-gray-500">No se encontraron pagos con los filtros seleccionados.</p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $incomes->links() }}
        </div>
    </div>
</div>
