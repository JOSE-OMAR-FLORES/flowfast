<div>
    <!-- Botón para abrir modal de pago -->
    <button wire:click="openPaymentModal"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        Pagar con Tarjeta
    </button>

    <!-- Modal de Pago con Stripe -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" 
             id="stripe-expense-modal-{{ $expense->id }}"
             x-data="{ 
                clientSecret: @entangle('clientSecret'),
                stripePublicKey: @entangle('stripePublicKey'),
                mounted: false,
                async initStripe(cs, pk) {
                    console.log('🚀 initStripe llamada con:', { cs: cs?.substring(0, 20), pk: pk?.substring(0, 20) });
                    
                    if (!cs || !pk) {
                        console.error('❌ Faltan datos');
                        return;
                    }
                    
                    // Cargar Stripe.js si no está disponible
                    if (typeof Stripe === 'undefined') {
                        console.log('📥 Cargando Stripe.js...');
                        await new Promise((resolve, reject) => {
                            const script = document.createElement('script');
                            script.src = 'https://js.stripe.com/v3/';
                            script.onload = resolve;
                            script.onerror = reject;
                            document.head.appendChild(script);
                        });
                        console.log('✅ Stripe.js cargado');
                    }
                    
                    console.log('🎨 Montando Stripe Elements...');
                    const stripe = Stripe(pk);
                    const elements = stripe.elements({ 
                        clientSecret: cs,
                        appearance: { theme: 'stripe', variables: { colorPrimary: '#2563eb' } }
                    });
                    const paymentElement = elements.create('payment');
                    
                    const mountPoint = document.querySelector('#payment-element-expense-{{ $expense->id }}');
                    if (!mountPoint) {
                        console.error('❌ Mount point no encontrado');
                        return;
                    }
                    
                    paymentElement.mount('#payment-element-expense-{{ $expense->id }}');
                    console.log('✅ Elements montado');
                    
                    paymentElement.on('ready', () => console.log('✅ Payment element listo'));
                    
                    // Configurar botón
                    setTimeout(() => {
                        const btn = document.getElementById('submit-payment-expense');
                        if (!btn) return;
                        
                        const newBtn = btn.cloneNode(true);
                        btn.parentNode.replaceChild(newBtn, btn);
                        
                        newBtn.addEventListener('click', async (e) => {
                            e.preventDefault();
                            console.log('🔄 Procesando pago...');
                            
                            const spinner = document.querySelector('#spinner-expense');
                            const btnText = document.querySelector('#button-text-expense');
                            newBtn.disabled = true;
                            if (spinner) spinner.classList.remove('hidden');
                            if (btnText) btnText.classList.add('hidden');
                            
                            try {
                                const { error, paymentIntent } = await stripe.confirmPayment({
                                    elements,
                                    confirmParams: { return_url: window.location.href },
                                    redirect: 'if_required'
                                });
                                
                                if (error) {
                                    console.error('❌ Error:', error);
                                    alert(error.message);
                                } else if (paymentIntent?.status === 'succeeded') {
                                    console.log('✅ Pago exitoso:', paymentIntent.id);
                                    await @this.call('paymentCompleted', paymentIntent.id);
                                    
                                    // Cerrar modal después de 2 segundos
                                    setTimeout(() => {
                                        @this.call('closeModal');
                                    }, 2000);
                                }
                            } catch (err) {
                                console.error('❌ Error inesperado:', err);
                                alert('Error al procesar el pago');
                            } finally {
                                newBtn.disabled = false;
                                if (spinner) spinner.classList.add('hidden');
                                if (btnText) btnText.classList.remove('hidden');
                            }
                        });
                    }, 500);
                }
             }"
             x-init="
                console.log('🎯 Modal x-init disparado');
                console.log('clientSecret:', clientSecret?.substring(0, 20));
                console.log('stripePublicKey:', stripePublicKey?.substring(0, 20));
                
                $nextTick(() => {
                    console.log('� nextTick ejecutado');
                    if (clientSecret && stripePublicKey && !mounted) {
                        console.log('✅ Inicializando Stripe...');
                        mounted = true;
                        setTimeout(() => initStripe(clientSecret, stripePublicKey), 300);
                    }
                });
             ">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <!-- Modal Panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Pago a Árbitro con Tarjeta
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
                                <span class="text-sm text-gray-600">Monto a pagar:</span>
                                <span class="text-lg font-bold text-blue-600">${{ number_format($expense->amount, 2) }} MXN</span>
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
                                        <p class="text-green-700 text-sm">El pago al árbitro ha sido procesado correctamente.</p>
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
                            <div id="payment-element-expense-{{ $expense->id }}" wire:ignore class="mb-4"></div>
                            
                            <div class="text-xs text-gray-500 mb-4">
                                <p class="mb-1">🔒 Pago seguro procesado por Stripe</p>
                                <p class="font-medium">Tarjetas de prueba:</p>
                                <ul class="list-disc list-inside ml-2">
                                    <li>4242 4242 4242 4242 - Pago exitoso</li>
                                </ul>
                            </div>

                            <div id="payment-message-expense-{{ $expense->id }}" class="hidden text-sm text-red-600 mb-4"></div>
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
                            <button type="button" id="submit-payment-expense"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="button-text-expense">Pagar ${{ number_format($expense->amount, 2) }}</span>
                                <span id="spinner-expense" class="hidden ml-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 1 1-7.07 2.93" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
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

        <!-- Stripe.js Script - Dentro del modal pero cargado antes del DOM -->
        <script>
            // Función global para inicializar Stripe cuando Alpine.js lo llama
            window.initStripeForExpense_{{ $expense->id }} = function(clientSecret, publicKey) {
                console.log('🚀 initStripeForExpense_{{ $expense->id }} llamada');
                console.log('📋 clientSecret:', clientSecret ? clientSecret.substring(0, 20) + '...' : 'NO DISPONIBLE');
                console.log('🔑 publicKey:', publicKey ? publicKey.substring(0, 20) + '...' : 'NO DISPONIBLE');
                
                if (!clientSecret || !publicKey) {
                    console.error('❌ Faltan datos de pago');
                    const messageEl = document.querySelector('#payment-message-expense-{{ $expense->id }}');
                    if (messageEl) {
                        messageEl.classList.remove('hidden');
                        messageEl.textContent = 'Error: No se pudo inicializar el sistema de pago. Por favor, cierra el modal e intenta de nuevo.';
                    }
                    return;
                }

                // Cargar Stripe.js si no está cargado
                if (typeof Stripe === 'undefined') {
                    console.log('📥 Cargando Stripe.js...');
                    const script = document.createElement('script');
                    script.src = 'https://js.stripe.com/v3/';
                    script.onload = () => {
                        console.log('✅ Stripe.js cargado exitosamente');
                        mountStripeElements_{{ $expense->id }}(clientSecret, publicKey);
                    };
                    script.onerror = () => {
                        console.error('❌ Error al cargar Stripe.js desde CDN');
                    };
                    document.head.appendChild(script);
                } else {
                    console.log('✅ Stripe.js ya está disponible');
                    mountStripeElements_{{ $expense->id }}(clientSecret, publicKey);
                }
            };

            function mountStripeElements_{{ $expense->id }}(clientSecret, publicKey) {
                console.log('🎨 mountStripeElements_{{ $expense->id }} iniciada');
                
                try {
                    console.log('🔑 Creando instancia de Stripe...');
                    const stripe = Stripe(publicKey);
                    
                    console.log('🎭 Configurando appearance...');
                    const appearance = {
                        theme: 'stripe',
                        variables: {
                            colorPrimary: '#2563eb',
                        }
                    };

                    console.log('📦 Creando elements...');
                    const elements = stripe.elements({ clientSecret, appearance });
                    
                    console.log('💳 Creando payment element...');
                    const paymentElement = elements.create('payment');
                    
                    // Verificar que el elemento existe
                    const mountPoint = document.querySelector('#payment-element-expense-{{ $expense->id }}');
                    console.log('🔍 Buscando mount point: #payment-element-expense-{{ $expense->id }}');
                    console.log('📍 Mount point encontrado:', mountPoint);
                    
                    if (!mountPoint) {
                        console.error('❌ No se encuentra el elemento #payment-element-expense-{{ $expense->id }}');
                        return;
                    }

                    console.log('⚡ Montando payment element en el DOM...');
                    paymentElement.mount('#payment-element-expense-{{ $expense->id }}');
                    
                    console.log('✅ Payment element montado exitosamente');
                    
                    // Event listener para errores de montaje
                    paymentElement.on('ready', () => {
                        console.log('✅ Payment element está listo y renderizado');
                    });
                    
                    paymentElement.on('loaderror', (event) => {
                        console.error('❌ Error al cargar payment element:', event);
                    });

                    // Configurar el botón de pago
                    console.log('🔘 Configurando botón de submit...');
                    const submitButton = document.getElementById('submit-payment-expense');
                    if (!submitButton) {
                        console.error('❌ No se encuentra el botón #submit-payment-expense');
                        return;
                    }

                    console.log('✅ Botón encontrado, agregando event listener...');

                    // Remover listener previo si existe
                    const newButton = submitButton.cloneNode(true);
                    submitButton.parentNode.replaceChild(newButton, submitButton);
                    
                    newButton.addEventListener('click', async (e) => {
                        e.preventDefault();
                        console.log('🔄 Click en botón de pago detectado');
                        
                        setLoading_{{ $expense->id }}(true);

                        try {
                            console.log('💰 Confirmando pago con Stripe...');
                            const { error, paymentIntent } = await stripe.confirmPayment({
                                elements,
                                confirmParams: {
                                    return_url: window.location.href,
                                },
                                redirect: 'if_required'
                            });

                            if (error) {
                                console.error('❌ Error en el pago:', error);
                                showMessage_{{ $expense->id }}(error.message);
                                setLoading_{{ $expense->id }}(false);
                            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                                console.log('✅ Pago exitoso!', paymentIntent.id);
                                
                                // Llamar al método de Livewire
                                @this.call('paymentCompleted', paymentIntent.id);
                                setLoading_{{ $expense->id }}(false);
                            }
                        } catch (err) {
                            console.error('❌ Error inesperado:', err);
                            showMessage_{{ $expense->id }}('Error al procesar el pago');
                            setLoading_{{ $expense->id }}(false);
                        }
                    });

                    console.log('✅ Todo configurado correctamente para expense {{ $expense->id }}');
                    
                } catch (error) {
                    console.error('❌ Error fatal en mountStripeElements:', error);
                    const messageEl = document.querySelector('#payment-message-expense-{{ $expense->id }}');
                    if (messageEl) {
                        messageEl.classList.remove('hidden');
                        messageEl.textContent = 'Error al inicializar el sistema de pago: ' + error.message;
                    }
                }
            }

            function showMessage_{{ $expense->id }}(messageText) {
                const messageContainer = document.querySelector('#payment-message-expense-{{ $expense->id }}');
                if (messageContainer) {
                    messageContainer.classList.remove('hidden');
                    messageContainer.textContent = messageText;

                    setTimeout(() => {
                        messageContainer.classList.add('hidden');
                        messageContainer.textContent = '';
                    }, 4000);
                }
            }

            function setLoading_{{ $expense->id }}(isLoading) {
                const submitBtn = document.querySelector('#submit-payment-expense');
                const spinner = document.querySelector('#spinner-expense');
                const buttonText = document.querySelector('#button-text-expense');

                if (submitBtn && spinner && buttonText) {
                    if (isLoading) {
                        submitBtn.disabled = true;
                        spinner.classList.remove('hidden');
                        buttonText.classList.add('hidden');
                    } else {
                        submitBtn.disabled = false;
                        spinner.classList.add('hidden');
                        buttonText.classList.remove('hidden');
                    }
                }
            }
        </script>
    @endif
</div>
