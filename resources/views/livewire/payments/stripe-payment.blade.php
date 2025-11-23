<div>
    <!-- Botón para abrir modal de pago -->
    <button wire:click="openPaymentModal"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3-3v8a3 3 0 003 3z"/>
        </svg>
        Pagar con Tarjeta
    </button>

    <!-- Modal de Pago con Stripe -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <!-- Modal Panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Pago con Tarjeta
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
                                <span class="text-sm font-medium text-gray-900">{{ $income->description }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">Equipo:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $income->team->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Monto a pagar:</span>
                                <span class="text-lg font-bold text-blue-600">${{ number_format($income->amount, 2) }} MXN</span>
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
                                        <h4 class="text-green-900 font-semibold">¡Pago exitoso!</h4>
                                        <p class="text-green-700 text-sm">Tu pago ha sido procesado correctamente.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($errorMessage)
                            <!-- Mensaje de error -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-red-900 font-semibold">Error en el pago</h4>
                                        <p class="text-red-700 text-sm">{{ $errorMessage }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Formulario de Stripe -->
                            <div id="payment-element" class="mb-4"></div>
                            
                            <div class="text-xs text-gray-500 mb-4">
                                <p class="mb-1">🔒 Pago seguro procesado por Stripe</p>
                                <p class="font-medium">Tarjetas de prueba:</p>
                                <ul class="list-disc list-inside ml-2">
                                    <li>4242 4242 4242 4242 - Pago exitoso</li>
                                    <li>4000 0000 0000 9995 - Fondos insuficientes</li>
                                    <li>4000 0000 0000 0002 - Tarjeta declinada</li>
                                </ul>
                            </div>

                            <div id="payment-message" class="hidden text-sm text-red-600 mb-4"></div>
                        @endif
                    </div>

                    <!-- Footer Buttons -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($paymentSuccess)
                            <button wire:click="closeModal"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3">
                                Cerrar
                            </button>
                        @else
                            <button type="button" id="submit-payment"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="button-text">Pagar ${{ number_format($income->amount, 2) }}</span>
                                <span id="spinner" class="hidden ml-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
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

        <!-- Stripe.js Script -->
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('stripe-modal-opened', () => {
                    initializeStripe();
                });

                Livewire.on('payment-successful', () => {
                    setTimeout(() => {
                        @this.call('closeModal');
                        window.location.reload();
                    }, 2000);
                });
            });

            function initializeStripe() {
                const stripe = Stripe('{{ $stripePublicKey }}');
                const clientSecret = '{{ $clientSecret }}';

                const appearance = {
                    theme: 'stripe',
                    variables: {
                        colorPrimary: '#2563eb',
                    }
                };

                const elements = stripe.elements({ clientSecret, appearance });
                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');

                const form = document.getElementById('submit-payment');
                
                form.addEventListener('click', async (e) => {
                    e.preventDefault();
                    setLoading(true);

                    const { error, paymentIntent } = await stripe.confirmPayment({
                        elements,
                        confirmParams: {
                            return_url: window.location.href,
                        },
                        redirect: 'if_required'
                    });

                    if (error) {
                        showMessage(error.message);
                        setLoading(false);
                    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                        @this.call('paymentCompleted', paymentIntent.id);
                        setLoading(false);
                    }
                });

                function showMessage(messageText) {
                    const messageContainer = document.querySelector('#payment-message');
                    messageContainer.classList.remove('hidden');
                    messageContainer.textContent = messageText;

                    setTimeout(() => {
                        messageContainer.classList.add('hidden');
                        messageContainer.textContent = '';
                    }, 4000);
                }

                function setLoading(isLoading) {
                    const submitButton = document.querySelector('#submit-payment');
                    const spinner = document.querySelector('#spinner');
                    const buttonText = document.querySelector('#button-text');

                    if (isLoading) {
                        submitButton.disabled = true;
                        spinner.classList.remove('hidden');
                        buttonText.classList.add('hidden');
                    } else {
                        submitButton.disabled = false;
                        spinner.classList.add('hidden');
                        buttonText.classList.remove('hidden');
                    }
                }
            }
        </script>
    @endif
</div>
