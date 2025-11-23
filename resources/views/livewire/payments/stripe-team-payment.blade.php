<div>
    {{-- Botón para abrir modal --}}
    <button wire:click="openModal" 
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
        </svg>
        <span>Pagar con Tarjeta</span>
    </button>

    {{-- Modal de Pago con Stripe --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
             x-data="{
                 clientSecret: @entangle('clientSecret'),
                 publicKey: '{{ $publicKey }}',
                 processing: @entangle('processing'),
                 stripe: null,
                 elements: null,
                 cardElement: null,
                 
                 initStripe() {
                     if (this.stripe || !this.clientSecret) return;
                     
                     setTimeout(() => {
                         if (!window.Stripe) {
                             console.error('Stripe no está cargado');
                             return;
                         }
                         
                         this.stripe = window.Stripe(this.publicKey);
                         const appearance = { theme: 'stripe' };
                         this.elements = this.stripe.elements({ clientSecret: this.clientSecret, appearance });
                         this.cardElement = this.elements.create('payment');
                         this.cardElement.mount('#card-element-team');
                     }, 100);
                 },
                 
                 async handleSubmit() {
                     if (this.processing || !this.stripe || !this.elements) return;
                     
                     this.processing = true;
                     
                     try {
                         const { error, paymentIntent } = await this.stripe.confirmPayment({
                             elements: this.elements,
                             redirect: 'if_required',
                         });
                         
                         if (error) {
                             alert('Error: ' + error.message);
                             this.processing = false;
                         } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                             @this.call('confirmPayment', paymentIntent.id);
                         }
                     } catch (e) {
                         alert('Error al procesar el pago: ' + e.message);
                         this.processing = false;
                     }
                 }
             }"
             x-init="$nextTick(() => initStripe())"
             x-show="true">
            
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                     @click.away="$wire.closeModal()">
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Pagar con Tarjeta
                            </h3>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="bg-white px-6 py-6">
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Monto a pagar:</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($amount ?? 0, 2) }}</p>
                        </div>

                        {{-- Stripe Card Element --}}
                        <div id="card-element-team" class="p-3 border border-gray-300 rounded-lg bg-gray-50"></div>

                        <p class="mt-3 text-xs text-gray-500">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Pago seguro procesado por Stripe. El pago se confirmará automáticamente.
                        </p>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button wire:click="closeModal"
                                type="button"
                                :disabled="processing"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50">
                            Cancelar
                        </button>
                        <button @click="handleSubmit"
                                type="button"
                                :disabled="processing"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            <svg x-show="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="processing ? 'Procesando...' : 'Pagar Ahora'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Cargar Stripe.js --}}
    <script src="https://js.stripe.com/v3/"></script>
</div>
