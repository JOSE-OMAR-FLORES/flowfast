<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Crear Nuevo Equipo</h2>
                        <a href="{{ route('teams.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                    </div>

                    <!-- Mensajes de error general -->
                    @if (session()->has('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Error:</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Errores de validación -->
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Por favor corrige los siguientes errores:</strong>
                            <ul class="list-disc list-inside mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form wire:submit.prevent="save" class="space-y-6">
                        
                        <!-- Liga y Temporada -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="selectedLeague" class="block text-sm font-medium text-gray-700">Liga *</label>
                                <select wire:model.live="selectedLeague" id="selectedLeague" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Selecciona una liga</option>
                                    @foreach($leagues as $league)
                                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Selecciona primero la liga para filtrar las temporadas</p>
                            </div>

                            <div>
                                <label for="season_id" class="block text-sm font-medium text-gray-700">Temporada *</label>
                                <select wire:model.live="season_id" id="season_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Selecciona una temporada</option>
                                    @foreach($seasons as $season)
                                        <option value="{{ $season->id }}">{{ $season->name }} - {{ $season->status }}</option>
                                    @endforeach
                                </select>
                                @error('season_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Nombre del Equipo -->
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Equipo *</label>
                                <input type="text" wire:model.live="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="ej: Los Tigres FC">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Colores del Equipo -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700">Color Primario *</label>
                                <div class="mt-1 flex items-center gap-3">
                                    <input type="color" wire:model.live="primary_color" id="primary_color" class="h-10 w-20 rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <input type="text" wire:model.live="primary_color" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="#000000">
                                </div>
                                @error('primary_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700">Color Secundario *</label>
                                <div class="mt-1 flex items-center gap-3">
                                    <input type="color" wire:model.live="secondary_color" id="secondary_color" class="h-10 w-20 rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <input type="text" wire:model.live="secondary_color" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="#FFFFFF">
                                </div>
                                @error('secondary_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Vista previa de colores -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-700 mb-3">Vista previa de colores:</p>
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-lg flex items-center justify-center shadow-md" style="background-color: {{ $primary_color }};">
                                    <span class="text-2xl font-bold" style="color: {{ $secondary_color }};">{{ substr($name ?: 'EQ', 0, 2) }}</span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p><strong>Primario:</strong> {{ $primary_color }}</p>
                                    <p><strong>Secundario:</strong> {{ $secondary_color }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Entrenador -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="coach_id" class="block text-sm font-medium text-gray-700">Entrenador</label>
                                <select wire:model.live="coach_id" id="coach_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Sin asignar</option>
                                    @foreach($coaches as $coach)
                                        <option value="{{ $coach->id }}">{{ $coach->first_name }} {{ $coach->last_name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Puedes asignar un entrenador más tarde</p>
                                @error('coach_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="registration_paid" class="block text-sm font-medium text-gray-700">Estado de Registro</label>
                                <div class="mt-3 flex items-center">
                                    <input type="checkbox" wire:model.live="registration_paid" id="registration_paid" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="registration_paid" class="ml-2 block text-sm text-gray-900">
                                        Registro pagado
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 Si no marcas esto, se creará automáticamente un pago pendiente de inscripción
                                </p>
                                @error('registration_paid') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3 space-y-2">
                                    <p class="text-sm text-blue-700">
                                        Después de crear el equipo, podrás agregar jugadores y gestionar sus datos desde la página de edición.
                                    </p>
                                    <p class="text-sm text-blue-700 font-medium">
                                        💰 Se generará automáticamente un pago de inscripción según la cuota configurada en la liga (si no marcas "Registro pagado").
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row items-center justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('teams.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button 
                                type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <span wire:loading.remove>Crear Equipo</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Guardando...
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
