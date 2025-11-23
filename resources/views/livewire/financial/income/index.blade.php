<div class="min-h-screen bg-gray-50 py-6" wire:poll.10s>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">💰 Gestión de Ingresos</h2>
                <p class="text-sm text-gray-600 mt-1">Control de cuotas, inscripciones y pagos recibidos</p>
            </div>
            
            @if(auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'league_manager')
                <a href="{{ route('financial.income.create') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Registrar Ingreso
                </a>
            @endif
        </div>

        {{-- Info Box --}}
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <span class="font-semibold">💳 Pagos con tarjeta:</span> Se confirman automáticamente y no aparecen aquí. 
                        <span class="font-semibold">💵 Efectivo/🏦 Transferencia:</span> Aparecen aquí cuando el equipo registra el pago. Debes confirmarlos manualmente.
                    </p>
                </div>
            </div>
        </div>

        {{-- Mensajes --}}
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button wire:click="$refresh" class="text-green-800 hover:text-green-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Búsqueda --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Equipo, referencia..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                {{-- Filtro Liga --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Liga</label>
                    <select wire:model.live="leagueFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro Temporada --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Temporada</label>
                    <select wire:model.live="seasonFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}">{{ $season->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro Tipo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select wire:model.live="typeFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="registration_fee">Cuota Inscripción</option>
                        <option value="match_fee">Cuota Partido</option>
                        <option value="penalty_fee">Multa</option>
                        <option value="equipment_sale">Venta Equipamiento</option>
                        <option value="sponsorship">Patrocinio</option>
                        <option value="donation">Donación</option>
                        <option value="other">Otro</option>
                    </select>
                </div>

                {{-- Filtro Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="statusFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="pending">Pendiente</option>
                        <option value="pending_confirmation">Esperando Confirmación</option>
                        <option value="paid_by_team">Pagado por Equipo</option>
                        <option value="confirmed_by_admin">Confirmado Admin</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="overdue">Vencido</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Tabla de Ingresos --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($incomes as $income)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $income->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $income->getIncomeTypeLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $income->team->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate">{{ $income->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                    ${{ number_format($income->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $income->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $income->payment_status === 'pending_confirmation' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $income->payment_status === 'paid_by_team' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $income->payment_status === 'confirmed_by_admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $income->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $income->payment_status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $income->payment_status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $income->payment_status)) }}
                                    </span>
                                    @if($income->payment_method)
                                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                            {{ ucfirst($income->payment_method) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Botón Confirmar Pago (Solo para paid_by_team con efectivo o transferencia) --}}
                                        @if(in_array($income->payment_status, ['paid_by_team', 'confirmed_by_admin']) && in_array($income->payment_method, ['cash', 'transfer']) && (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'league_manager' || auth()->user()->user_type === 'referee'))
                                            <button wire:click="openConfirmModal({{ $income->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Confirmar Pago
                                            </button>
                                        @endif

                                        {{-- Botón Marcar Vencido --}}
                                        @if($income->payment_status === 'pending' && (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'league_manager'))
                                            <button wire:click="markAsOverdue({{ $income->id }})"
                                                    wire:confirm="¿Marcar este ingreso como vencido?"
                                                    class="text-orange-600 hover:text-orange-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Botón Cancelar --}}
                                        @if(!in_array($income->payment_status, ['confirmed', 'cancelled']) && auth()->user()->user_type === 'admin')
                                            <button wire:click="cancelIncome({{ $income->id }})"
                                                    wire:confirm="¿Estás seguro de cancelar este ingreso?"
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            {{-- Fila expandible con detalles del pago (solo para paid_by_team o confirmed_by_admin) --}}
                            @if(in_array($income->payment_status, ['paid_by_team', 'confirmed_by_admin']) && ($income->payment_reference || $income->notes || $income->paid_at))
                                <tr wire:key="details-row-{{ $income->id }}" class="bg-blue-50">
                                    <td colspan="7" class="px-6 py-4 border-l-4 border-blue-400">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-semibold text-blue-900 mb-2">Detalles del Pago</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    @if($income->payment_method)
                                                        <div>
                                                            <p class="text-xs text-blue-700 font-medium">Método de Pago:</p>
                                                            <p class="text-sm text-blue-900 font-semibold capitalize">{{ $income->payment_method }}</p>
                                                        </div>
                                                    @endif
                                                    @if($income->payment_reference)
                                                        <div>
                                                            <p class="text-xs text-blue-700 font-medium">Referencia:</p>
                                                            <p class="text-sm text-blue-900 font-semibold">{{ $income->payment_reference }}</p>
                                                        </div>
                                                    @endif
                                                    @if($income->paid_at)
                                                        <div>
                                                            <p class="text-xs text-blue-700 font-medium">Fecha de Pago:</p>
                                                            <p class="text-sm text-blue-900">{{ $income->paid_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($income->notes)
                                                    <div class="mt-3">
                                                        <p class="text-xs text-blue-700 font-medium">Notas:</p>
                                                        <p class="text-sm text-blue-900 italic">{{ $income->notes }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No hay ingresos registrados</p>
                                    <p class="text-sm mt-1">Los ingresos aparecerán aquí una vez que se registren</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $incomes->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Confirmación --}}
    @if($showConfirmModal && $selectedIncome)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeConfirmModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Confirmar Pago
                                </h3>
                                <div class="mt-4 space-y-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Equipo:</p>
                                        <p class="text-sm text-gray-900">{{ $selectedIncome->team->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Monto:</p>
                                        <p class="text-lg font-bold text-green-600">${{ number_format($selectedIncome->amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Estado actual:</p>
                                        <p class="text-sm text-gray-900">{{ $selectedIncome->getPaymentStatusLabel() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="confirmPayment" 
                                type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmar Pago
                        </button>
                        <button wire:click="closeConfirmModal" 
                                type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
