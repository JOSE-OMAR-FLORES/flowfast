<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">💵 Mis Pagos</h1>
            <p class="mt-1 text-sm text-gray-600">Historial y confirmación de pagos recibidos como árbitro</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Pagos en Proceso --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En Proceso</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Listos para Confirmar --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Por Confirmar</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['ready'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Monto Pendiente --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Por Confirmar</p>
                        <p class="text-2xl font-bold text-purple-600">${{ number_format($stats['total_pending'], 2) }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Confirmado --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Confirmados</p>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($stats['total_confirmed'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="mb-6 flex flex-wrap gap-4">
            <select wire:model.live="statusFilter" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="all">Todos los estados</option>
                <option value="pending">En proceso (pendientes/aprobados)</option>
                <option value="ready">Listos para confirmar</option>
                <option value="confirmed">Confirmados</option>
            </select>
        </div>

        {{-- Mensajes Flash --}}
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

        {{-- Lista de Pagos --}}
        <div class="space-y-4">
            @forelse($expenses as $expense)
                <div wire:key="expense-{{ $expense->id }}" 
                     class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            {{-- Información del Pago --}}
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $expense->description }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $expense->league->name ?? 'Liga no asignada' }}
                                        </p>
                                    </div>
                                    {{-- Badge de Estado --}}
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($expense->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($expense->payment_status === 'approved') bg-blue-100 text-blue-800
                                        @elseif(in_array($expense->payment_status, ['ready_for_payment', 'paid'])) bg-purple-100 text-purple-800
                                        @elseif($expense->payment_status === 'confirmed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($expense->payment_status === 'pending')
                                            🟡 Pendiente de aprobación
                                        @elseif($expense->payment_status === 'approved')
                                            🔵 Aprobado
                                        @elseif(in_array($expense->payment_status, ['ready_for_payment', 'paid']))
                                            🟣 ¡Pago recibido - Confirmar!
                                        @elseif($expense->payment_status === 'confirmed')
                                            ✅ Confirmado
                                        @else
                                            {{ $expense->payment_status }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Detalles --}}
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Monto</p>
                                        <p class="font-bold text-xl text-green-600">${{ number_format($expense->amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Tipo</p>
                                        <p class="font-medium text-gray-900">{{ $expense->type_label }}</p>
                                    </div>
                                    @if($expense->payment_method)
                                        <div>
                                            <p class="text-gray-500">Método</p>
                                            <p class="font-medium text-gray-900 capitalize">{{ $expense->payment_method }}</p>
                                        </div>
                                    @endif
                                    @if($expense->paid_at)
                                        <div>
                                            <p class="text-gray-500">Fecha de Pago</p>
                                            <p class="font-medium text-gray-900">{{ $expense->paid_at->format('d/m/Y') }}</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info del partido si existe --}}
                                @if($expense->fixture)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs text-gray-500 mb-1">Partido</p>
                                        <p class="text-sm text-gray-700">
                                            ⚽ {{ $expense->fixture->homeTeam->name ?? 'N/A' }} vs {{ $expense->fixture->awayTeam->name ?? 'N/A' }}
                                            @if($expense->fixture->match_date)
                                                - {{ $expense->fixture->match_date->format('d/m/Y') }}
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                {{-- Confirmación info --}}
                                @if($expense->payment_status === 'confirmed' && $expense->confirmed_at)
                                    <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                        <p class="text-sm text-green-700">
                                            ✓ Confirmaste la recepción el {{ $expense->confirmed_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Botón de Acción --}}
                            <div class="flex flex-col gap-2 min-w-[200px]">
                                @if(in_array($expense->payment_status, ['ready_for_payment', 'paid']))
                                    <button wire:click="openConfirmModal({{ $expense->id }})"
                                            class="inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Confirmar Recepción
                                    </button>
                                    <p class="text-xs text-center text-gray-500">
                                        El administrador ha registrado este pago
                                    </p>
                                @elseif($expense->payment_status === 'confirmed')
                                    <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                                        <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm font-medium text-green-800">Pago Confirmado</p>
                                    </div>
                                @else
                                    <div class="text-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <svg class="w-8 h-8 text-yellow-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm font-medium text-yellow-800">En Proceso</p>
                                        <p class="text-xs text-yellow-600 mt-1">Esperando aprobación del admin</p>
                                    </div>
                                @endif
                            </div>
                        </div>
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

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $expenses->links() }}
        </div>
    </div>

    {{-- Modal de Confirmación --}}
    @if($showConfirmModal && $selectedExpense)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeConfirmModal"></div>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Confirmar Recepción de Pago
                                </h3>
                                <div class="mt-4">
                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm text-gray-600">Concepto:</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $selectedExpense->description }}</span>
                                        </div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm text-gray-600">Monto:</span>
                                            <span class="text-lg font-bold text-green-600">${{ number_format($selectedExpense->amount, 2) }}</span>
                                        </div>
                                        @if($selectedExpense->payment_method)
                                            <div class="flex justify-between mb-2">
                                                <span class="text-sm text-gray-600">Método de pago:</span>
                                                <span class="text-sm font-medium text-gray-900 capitalize">{{ $selectedExpense->payment_method }}</span>
                                            </div>
                                        @endif
                                        @if($selectedExpense->payment_reference)
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Referencia:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $selectedExpense->payment_reference }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <label for="confirmationNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notas (opcional)
                                        </label>
                                        <textarea wire:model="confirmationNotes"
                                                  id="confirmationNotes"
                                                  rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                  placeholder="Agrega algún comentario si lo deseas..."></textarea>
                                    </div>

                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                        <p class="text-sm text-yellow-700">
                                            ⚠️ Al confirmar, estás indicando que has recibido este pago correctamente.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button wire:click="confirmPaymentReceived"
                                type="button"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirmar Recepción
                        </button>
                        <button wire:click="closeConfirmModal"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
