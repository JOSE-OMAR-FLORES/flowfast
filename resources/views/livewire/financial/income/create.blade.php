<div class="min-h-screen bg-gray-50 py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('financial.income.index') }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">💰 Registrar Ingreso</h2>
            </div>
            <p class="text-sm text-gray-600 ml-9">Complete el formulario para registrar un nuevo ingreso</p>
        </div>

        {{-- Mensajes --}}
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Formulario --}}
        <form wire:submit.prevent="save">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                
                {{-- Sección 1: Información Básica --}}
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Liga --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Liga <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="league_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Seleccione una liga</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                                @endforeach
                            </select>
                            @error('league_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Temporada --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Temporada</label>
                            <select wire:model.live="season_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    @if(!$league_id) disabled @endif>
                                <option value="">Seleccione temporada</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}">{{ $season->name }}</option>
                                @endforeach
                            </select>
                            @error('season_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Equipo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                            <select wire:model.live="team_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    @if(!$league_id) disabled @endif>
                                <option value="">Seleccione equipo</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Partido (opcional) --}}
                        @if(count($matches) > 0)
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Partido (Opcional)</label>
                                <select wire:model.live="match_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="">No asociar a partido</option>
                                    @foreach($matches as $match)
                                        <option value="{{ $match->id }}">
                                            {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }} - 
                                            {{ \Carbon\Carbon::parse($match->match_date)->format('d/m/Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('match_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sección 2: Detalles del Ingreso --}}
                <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Ingreso</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Tipo de Ingreso --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Ingreso <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="income_type" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Selecciona el tipo de ingreso</option>
                                <option value="registration_fee">Cuota de Inscripción</option>
                                <option value="match_fee">Cuota por Partido</option>
                                <option value="penalty_fee">Multa</option>
                                <option value="referee_payment">Pago a Árbitros</option>
                                <option value="equipment_sale">Venta de Equipamiento</option>
                                <option value="sponsorship">Patrocinio</option>
                                <option value="donation">Donación</option>
                                <option value="other">Otro</option>
                            </select>
                            @error('income_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Monto --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Monto <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500 font-medium">$</span>
                                <input type="number" 
                                       wire:model="amount" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       @if($amountDisabled) disabled style="background-color:#f3f4f6;cursor:not-allowed;" @endif>
                            </div>
                            @error('amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Fecha de Vencimiento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Vencimiento</label>
                            <input type="date" 
                                   wire:model="due_date" 
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('due_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Método de Pago --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                            <select wire:model="payment_method" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Seleccione método</option>
                                <option value="cash">Efectivo</option>
                                <option value="transfer">Transferencia</option>
                                <option value="credit_card">Tarjeta de Crédito</option>
                                <option value="debit_card">Tarjeta de Débito</option>
                                <option value="check">Cheque</option>
                                <option value="other">Otro</option>
                            </select>
                            @error('payment_method') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="description" 
                                      rows="3"
                                      placeholder="Ingrese una descripción del ingreso"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Referencia de Pago --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Referencia de Pago</label>
                            <input type="text" 
                                   wire:model="payment_reference" 
                                   placeholder="Número de referencia, folio, etc."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('payment_reference') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección 3: Comprobante y Notas --}}
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Comprobante y Notas</h3>
                    
                    <div class="space-y-4">
                        {{-- Comprobante de Pago --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comprobante de Pago</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Click para subir</span> o arrastre</p>
                                        <p class="text-xs text-gray-500">PNG, JPG (MAX. 2MB)</p>
                                    </div>
                                    <input type="file" wire:model="payment_proof" class="hidden" accept="image/*">
                                </label>
                            </div>
                            @if ($payment_proof)
                                <div class="mt-2 text-sm text-green-600">Archivo seleccionado: {{ $payment_proof->getClientOriginalName() }}</div>
                            @endif
                            @error('payment_proof') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Notas Adicionales --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notas Adicionales</label>
                            <textarea wire:model="notes" 
                                      rows="3"
                                      placeholder="Notas o comentarios adicionales..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                            @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <a href="{{ route('financial.income.index') }}" 
                       class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-sm disabled:opacity-50">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Registrar Ingreso</span>
                        <span wire:loading wire:target="save">Procesando...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
