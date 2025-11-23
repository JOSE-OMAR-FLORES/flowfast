<div>
    <!-- Botón para abrir modal de pago -->
    <button wire:click="openPaymentModal"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        Pagar por Transferencia
    </button>

    <!-- Modal de Pago por Transferencia -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }" x-show="open">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <!-- Modal Panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Pago por Transferencia
                            </h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Información del Pago -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">Concepto:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $expense->description }}</span>
                            </div>
                            @if($expense->referee)
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">Árbitro:</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $expense->referee->first_name }} {{ $expense->referee->last_name }}
                                    </span>
                                </div>
                            @endif
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">Liga:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $expense->league->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Monto:</span>
                                <span class="text-lg font-bold text-indigo-600">${{ number_format($expense->amount, 2) }} MXN</span>
                            </div>
                        </div>

                        @if($paymentSuccess)
                            <!-- Mensaje de éxito -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-green-900 font-semibold">¡Pago registrado!</h4>
                                        <p class="text-green-700 text-sm">El pago por transferencia ha sido registrado correctamente.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Formulario -->
                            <form wire:submit.prevent="confirmPayment">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Monto transferido <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" wire:model="amount" step="0.01" min="0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               required>
                                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Referencia/Folio <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="transferReference"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Ej: REF123456" required>
                                        @error('transferReference') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Banco <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="bankName"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Ej: BBVA Bancomer" required>
                                        @error('bankName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Notas (opcional)
                                        </label>
                                        <textarea wire:model="notes" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Información adicional sobre la transferencia"></textarea>
                                        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>

                    <!-- Footer Buttons -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($paymentSuccess)
                            <button wire:click="closeModal"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3">
                                Cerrar
                            </button>
                        @else
                            <button wire:click="confirmPayment"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3">
                                Confirmar Pago
                            </button>
                            <button wire:click="closeModal" type="button"
                                    class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                Cancelar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('close-modal-after-delay', () => {
                    setTimeout(() => {
                        @this.call('closeModal');
                    }, 2000);
                });
            });
        </script>
    @endif
</div>
