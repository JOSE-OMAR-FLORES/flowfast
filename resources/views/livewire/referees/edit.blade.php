<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('referees.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">✏️ Editar Árbitro</h1>
            </div>
            <p class="text-sm text-gray-600 ml-10">Actualiza la información del árbitro</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form wire:submit.prevent="update">
                <div class="space-y-6">
                    
                    <!-- Nombre y Apellido -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="first_name"
                                wire:model="first_name"
                                class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white @error('first_name') border-red-500 @enderror"
                                placeholder="Nombre del árbitro"
                            >
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Apellido <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="last_name"
                                wire:model="last_name"
                                class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white @error('last_name') border-red-500 @enderror"
                                placeholder="Apellido del árbitro"
                            >
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="phone"
                            wire:model="phone"
                            class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white @error('phone') border-red-500 @enderror"
                            placeholder="Número de teléfono"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Árbitro -->
                    <div>
                        <label for="referee_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Árbitro <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="referee_type"
                            wire:model="referee_type"
                            class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white @error('referee_type') border-red-500 @enderror"
                        >
                            <option value="">Selecciona un tipo</option>
                            <option value="main">Principal</option>
                            <option value="assistant">Asistente</option>
                            <option value="scorer">Anotador</option>
                        </select>
                        @error('referee_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Liga -->
                    <div>
                        <label for="league_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Liga
                        </label>
                        <select 
                            id="league_id"
                            wire:model="league_id"
                            class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white @error('league_id') border-red-500 @enderror"
                        >
                            <option value="">Sin asignar</option>
                            @foreach($leagues as $league)
                                <option value="{{ $league->id }}">{{ $league->name }}</option>
                            @endforeach
                        </select>
                        @error('league_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tarifa de Pago -->
                    <div>
                        <label for="payment_rate" class="block text-sm font-medium text-gray-700 mb-1">
                            Tarifa de Pago <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input 
                                type="number" 
                                id="payment_rate"
                                wire:model="payment_rate"
                                step="0.01"
                                min="0"
                                class="w-full pl-8 rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white @error('payment_rate') border-red-500 @enderror"
                                placeholder="0.00"
                            >
                        </div>
                        @error('payment_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4 border-t border-gray-200">
                        <a 
                            href="{{ route('referees.index') }}" 
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </a>
                        <button 
                            type="submit" 
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

