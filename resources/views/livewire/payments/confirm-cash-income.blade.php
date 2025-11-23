<div>
    {{-- Botón para abrir modal --}}
    <button wire:click="openModal" 
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Confirmar Efectivo</span>
    </button>

    {{-- Modal de Confirmación --}}
    @if($showModal && $income)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                     @click.away="$wire.closeModal()">
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Confirmar Pago en Efectivo
                            </h3>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <form wire:submit.prevent="confirmReceipt">
                        <div class="bg-white px-6 py-6 space-y-4">
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <p class="text-sm font-semibold text-blue-900">Detalles del Pago:</p>
                                <div class="mt-2 text-sm text-blue-800">
                                    <p><span class="font-medium">Equipo:</span> {{ $income->payer?->name ?? 'N/A' }}</p>
                                    <p><span class="font-medium">Monto:</span> ${{ number_format($income->amount, 2) }}</p>
                                    <p><span class="font-medium">Descripción:</span> {{ $income->description }}</p>
                                    @if($income->payment_notes)
                                        <p class="mt-2"><span class="font-medium">Notas del pagador:</span> {{ $income->payment_notes }}</p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Notas de confirmación (opcional)
                                </label>
                                <textarea wire:model="notes"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                          placeholder="Agregar notas sobre la confirmación del pago..."></textarea>
                            </div>

                            <div class="bg-green-50 border-l-4 border-green-400 p-3">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="ml-3 text-sm text-green-700">
                                        Al confirmar, este pago se marcará como completado.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button wire:click="closeModal"
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                Confirmar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
