<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header responsive -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <a href="{{ route('leagues.index') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Ligas
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-4">Editar Liga</h1>
            <p class="mt-1 text-sm text-gray-600">Actualiza la información de la liga: <strong>{{ $league->name }}</strong></p>
        </div>

        <!-- Formulario responsive -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <form wire:submit="update">
                <div class="p-4 sm:p-6 lg:p-8 space-y-6">
                    
                    <!-- Información básica -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Nombre -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la Liga <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name"
                                       wire:model="name" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                       placeholder="Ej: Liga Municipal de Fútbol">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deporte -->
                            <div>
                                <label for="sport_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deporte <span class="text-red-500">*</span>
                                </label>
                                <select id="sport_id"
                                        wire:model="sport_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sport_id') border-red-500 @enderror">
                                    <option value="">Seleccionar deporte</option>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                    @endforeach
                                </select>
                                @error('sport_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Manager -->
                            <div>
                                <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Manager (Opcional)
                                </label>
                                <select id="manager_id"
                                        wire:model="manager_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Sin asignar</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}">{{ $manager->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Estado -->
                            <div class="sm:col-span-2">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select id="status"
                                        wire:model="status" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                    <option value="draft">Borrador</option>
                                    <option value="active">Activa</option>
                                    <option value="inactive">Inactiva</option>
                                    <option value="archived">Archivada</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción
                                </label>
                                <textarea id="description"
                                          wire:model="description" 
                                          rows="4"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                          placeholder="Describe la liga, sus características, requisitos, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración financiera -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Configuración Financiera</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Cuota de inscripción -->
                            <div>
                                <label for="registration_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cuota de Inscripción <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" 
                                           id="registration_fee"
                                           wire:model="registration_fee" 
                                           step="0.01"
                                           min="0"
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('registration_fee') border-red-500 @enderror"
                                           placeholder="0.00">
                                </div>
                                @error('registration_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cuota por partido (por equipo) -->
                            <div>
                                <label for="match_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cuota por Partido (por equipo) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" 
                                           id="match_fee"
                                           wire:model="match_fee" 
                                           step="0.01"
                                           min="0"
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('match_fee') border-red-500 @enderror"
                                           placeholder="0.00">
                                </div>
                                @error('match_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Multa por partido -->
                            <div>
                                <label for="penalty_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                    Multa por Penalización <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" 
                                           id="penalty_fee"
                                           wire:model="penalty_fee" 
                                           step="0.01"
                                           min="0"
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('penalty_fee') border-red-500 @enderror"
                                           placeholder="0.00">
                                </div>
                                @error('penalty_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pago a árbitros -->
                            <div>
                                <label for="referee_payment" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pago a Árbitros <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" 
                                           id="referee_payment"
                                           wire:model="referee_payment" 
                                           step="0.01"
                                           min="0"
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('referee_payment') border-red-500 @enderror"
                                           placeholder="0.00">
                                </div>
                                @error('referee_payment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones responsive -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 lg:px-8 border-t border-gray-200">
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('leagues.index') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Actualizar Liga
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
