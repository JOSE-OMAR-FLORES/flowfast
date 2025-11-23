<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Generar Calendario de Partidos</h2>
                    <p class="text-sm text-gray-600 mt-1">Generación automática con algoritmo Round Robin</p>
                </div>
                <a href="{{ route('fixtures.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('preview'))
            <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                {{ session('preview') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario de Configuración -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Configuración</h3>

                    <form wire:submit="generatePreview" class="space-y-4">
                        <!-- Liga (solo para admin) -->
                        @if(auth()->user()->user_type === 'admin')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Liga <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedLeague" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Seleccionar liga</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Temporada -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Temporada <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="season_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Seleccionar temporada</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}">{{ $season->name }}</option>
                                @endforeach
                            </select>
                            @error('season_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Cancha Principal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Cancha Principal
                            </label>
                            <select wire:model.live="venue_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Sin asignar</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Se puede cambiar individualmente después</p>
                            @error('venue_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Fecha de Inicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Fecha de Inicio 
                                <span class="text-xs text-gray-500">(desde la temporada)</span>
                            </label>
                            <input type="date" 
                                   wire:model="start_date"
                                   readonly
                                   class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500">Esta fecha se toma automáticamente de la temporada seleccionada</p>
                            @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tipo de Torneo -->
                        <div class="border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Tipo de Torneo
                            </label>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.live="use_round_robin" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <span class="ml-2 text-sm text-gray-700">
                                        Usar Round Robin
                                    </span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.live="double_round" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <span class="ml-2 text-sm text-gray-700">
                                        Doble Ronda (Ida y Vuelta)
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Botón Generar Preview -->
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span wire:loading.remove>Generar Vista Previa</span>
                                <span wire:loading>Generando...</span>
                            </button>
                        </div>

                        <!-- Botón Confirmar (solo si hay preview) -->
                        @if(!empty($preview))
                        <div>
                            <button type="button"
                                    wire:click="confirmGeneration"
                                    wire:confirm="¿Estás seguro? Esto creará {{ $totalMatches }} partidos en la base de datos."
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span wire:loading.remove wire:target="confirmGeneration">Confirmar y Crear Fixtures</span>
                                <span wire:loading wire:target="confirmGeneration">Creando...</span>
                            </button>
                        </div>
                        @endif
                    </form>

                    <!-- Información del Sistema -->
                    @if(!empty($preview))
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-sm text-blue-800 mb-2">Resumen del Calendario</h4>
                        <div class="space-y-1 text-xs text-blue-700">
                            <div class="flex justify-between">
                                <span>Total de Jornadas:</span>
                                <span class="font-bold">{{ $totalRounds }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total de Partidos:</span>
                                <span class="font-bold">{{ $totalMatches }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tipo:</span>
                                <span class="font-bold">{{ $double_round ? 'Ida y Vuelta' : 'Una Vuelta' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Vista Previa de Fixtures -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Vista Previa del Calendario</h3>

                    @if(empty($preview))
                        <div class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-4 text-lg">No hay vista previa disponible</p>
                            <p class="text-sm mt-2">Configura los parámetros y genera una vista previa</p>
                        </div>
                    @else
                        <div class="space-y-6 max-h-[600px] overflow-y-auto pr-2">
                            @foreach($preview as $roundIndex => $round)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                        <h4 class="font-semibold text-sm text-gray-800">
                                            Jornada {{ $round[0]['round'] ?? ($roundIndex + 1) }}
                                        </h4>
                                    </div>
                                    <div class="p-4 space-y-3">
                                        @foreach($round as $match)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex-1 flex items-center space-x-3">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-white border border-gray-300">
                                                            {{ $match['home_team']['name'] }}
                                                        </span>
                                                        <span class="text-gray-500 text-xs">vs</span>
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-white border border-gray-300">
                                                            {{ $match['away_team']['name'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3 text-xs text-gray-600">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($match['date'])->format('d/m/Y') }}
                                                    </div>
                                                    <span class="text-gray-400">•</span>
                                                    <span class="text-xs text-gray-500">Partido {{ $match['match'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-semibold mb-1">Nota Importante:</p>
                                    <p>Esta es solo una vista previa. Los fixtures NO se han creado aún. Haz clic en "Confirmar y Crear Fixtures" para guardarlos en la base de datos.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Explicación del Algoritmo Round Robin -->
        <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">¿Cómo funciona el Round Robin?</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex flex-col items-center text-center p-4 bg-blue-50 rounded-lg">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl mb-3">1</div>
                    <h4 class="font-semibold text-gray-800 mb-2">Emparejamiento Justo</h4>
                    <p class="text-sm text-gray-600">Cada equipo juega contra todos los demás equipos exactamente una vez (o dos en doble ronda)</p>
                </div>

                <div class="flex flex-col items-center text-center p-4 bg-green-50 rounded-lg">
                    <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-xl mb-3">2</div>
                    <h4 class="font-semibold text-gray-800 mb-2">Distribución Automática</h4>
                    <p class="text-sm text-gray-600">Los partidos se distribuyen en jornadas según los días de juego configurados en la temporada</p>
                </div>

                <div class="flex flex-col items-center text-center p-4 bg-purple-50 rounded-lg">
                    <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl mb-3">3</div>
                    <h4 class="font-semibold text-gray-800 mb-2">Horarios Alternados</h4>
                    <p class="text-sm text-gray-600">Los horarios de los partidos se alternan automáticamente entre los configurados</p>
                </div>
            </div>
        </div>
    </div>
</div>
