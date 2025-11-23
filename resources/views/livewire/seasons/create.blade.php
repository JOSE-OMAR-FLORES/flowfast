<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header responsive -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <a href="{{ route('seasons.index') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Temporadas
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-4">Crear Nueva Temporada</h1>
            <p class="mt-1 text-sm text-gray-600">Configura los detalles de la temporada y el calendario de juegos</p>
        </div>

        <!-- Formulario responsive -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <form wire:submit="save">
                <div class="p-4 sm:p-6 lg:p-8 space-y-6">
                    
                    <!-- Información básica -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Liga -->
                            <div class="sm:col-span-2">
                                <label for="league_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Liga <span class="text-red-500">*</span>
                                </label>
                                <select id="league_id"
                                        wire:model="league_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('league_id') border-red-500 @enderror">
                                    <option value="">Seleccionar liga</option>
                                    @foreach($leagues as $league)
                                        <option value="{{ $league->id }}">{{ $league->name }} ({{ $league->sport->name }})</option>
                                    @endforeach
                                </select>
                                @error('league_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nombre -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la Temporada <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name"
                                       wire:model="name" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                       placeholder="Ej: Temporada Primavera 2025">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Formato -->
                            <div>
                                <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                                    Formato <span class="text-red-500">*</span>
                                </label>
                                <select id="format"
                                        wire:model.live="format" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('format') border-red-500 @enderror">
                                    <option value="round_robin">Round Robin</option>
                                    <option value="playoff">Playoff</option>
                                    <option value="round_robin_playoff">Round Robin + Playoff</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Round Robin: Todos contra todos | Playoff: Eliminatorias | Round Robin + Playoff: Grupos + Finales
                                </p>
                                @error('format')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo Round Robin (solo si format es round_robin o round_robin_playoff) -->
                            @if(in_array($format, ['round_robin', 'round_robin_playoff']))
                                <div>
                                    <label for="round_robin_type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo Round Robin <span class="text-red-500">*</span>
                                    </label>
                                    <select id="round_robin_type"
                                            wire:model="round_robin_type" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('round_robin_type') border-red-500 @enderror">
                                        <option value="single">Vuelta Simple</option>
                                        <option value="double">Ida y Vuelta</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Simple: Todos contra todos 1 vez | Doble: Todos contra todos 2 veces
                                    </p>
                                    @error('round_robin_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Estado -->
                            <div class="@if($format !== 'round_robin') sm:col-span-2 @endif">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select id="status"
                                        wire:model="status" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                    <option value="draft">Borrador</option>
                                    <option value="upcoming">Próxima</option>
                                    <option value="active">Activa</option>
                                    <option value="completed">Completada</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Fechas de la Temporada</h2>
                        <!-- Fecha inicio -->
                        <div class="w-full lg:w-1/2">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="start_date"
                                   wire:model="start_date" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Esta fecha se usará automáticamente al generar fixtures. La fecha de fin se calculará según los partidos programados.</p>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Configuración de Partidos -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Configuración de Partidos</h2>
                        
                        <!-- Días de juego -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Días de Juego <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                                @foreach(['monday' => 'Lun', 'tuesday' => 'Mar', 'wednesday' => 'Mié', 'thursday' => 'Jue', 'friday' => 'Vie', 'saturday' => 'Sáb', 'sunday' => 'Dom'] as $day => $label)
                                    <label class="flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer transition-all
                                        {{ in_array($day, $game_days ?? []) ? 'bg-blue-100 border-blue-500 text-blue-800' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                        <input type="checkbox" 
                                               wire:model.live="game_days" 
                                               value="{{ $day }}"
                                               class="sr-only">
                                        <span class="text-sm font-medium">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('game_days')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Partidos por día -->
                        <div class="mb-6">
                            <label for="daily_matches" class="block text-sm font-medium text-gray-700 mb-2">
                                Partidos por Día <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="daily_matches"
                                   wire:model.live="daily_matches" 
                                   min="1"
                                   max="10"
                                   class="w-full sm:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('daily_matches') border-red-500 @enderror">
                            <p class="mt-1 text-xs font-semibold text-indigo-600">⚠️ Debes definir exactamente {{ $daily_matches }} horarios abajo</p>
                            @error('daily_matches')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Horarios -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Horarios de Juego <span class="text-red-500">*</span>
                                <span class="text-xs font-normal text-gray-500">(Deben ser {{ $daily_matches }} horarios)</span>
                            </label>
                            <div class="space-y-2">
                                @foreach($match_times as $index => $time)
                                    <div class="flex gap-2">
                                        <input type="time" 
                                               wire:model.live="match_times.{{ $index }}" 
                                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('match_times.' . $index) border-red-500 @enderror">
                                        @if(count($match_times) > 1)
                                            <button type="button" 
                                                    wire:click="removeMatchTime({{ $index }})"
                                                    class="px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    @error('match_times.' . $index)
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endforeach
                            </div>
                            <button type="button" 
                                    wire:click="addMatchTime"
                                    class="mt-3 inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Agregar Horario
                            </button>
                            @error('match_times')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pagos de Inscripción -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pagos de Inscripción</h2>
                        
                        <!-- Toggle para generar pagos -->
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" 
                                       wire:model.live="generateRegistrationFees"
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                    Generar pagos de inscripción para los equipos
                                </span>
                            </label>
                            <p class="mt-1 ml-8 text-xs text-gray-500">
                                Crea automáticamente un registro de pago pendiente por cada equipo seleccionado
                            </p>
                        </div>

                        <!-- Selección de equipos (solo si está activado) -->
                        @if($generateRegistrationFees && !empty($teams))
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Seleccionar Equipos
                                    <span class="text-xs font-normal text-gray-500">({{ count($selectedTeams) }} seleccionados)</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                    @foreach($teams as $team)
                                        <label class="flex items-center px-3 py-2 bg-white border rounded-lg cursor-pointer transition-all
                                            {{ in_array($team->id, $selectedTeams) ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:bg-gray-50' }}">
                                            <input type="checkbox" 
                                                   wire:model="selectedTeams" 
                                                   value="{{ $team->id }}"
                                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $team->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @if(count($selectedTeams) === 0)
                                    <p class="mt-2 text-xs text-amber-600">⚠️ Debes seleccionar al menos un equipo</p>
                                @endif
                            </div>
                        @elseif($generateRegistrationFees && empty($teams))
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-sm text-yellow-800">
                                    ⚠️ Primero debes seleccionar una liga para ver los equipos disponibles
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones responsive -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 lg:px-8 border-t border-gray-200">
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('seasons.index') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Crear Temporada
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
