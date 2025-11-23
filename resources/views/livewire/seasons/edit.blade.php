<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Editar Temporada</h2>
                        <a href="{{ route('seasons.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                    </div>

                    <!-- Mensajes Flash -->
                    @if (session()->has('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">¡Éxito!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Form -->
                    <form wire:submit.prevent="update" class="space-y-6">
                        
                        <!-- Liga y Nombre -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="league_id" class="block text-sm font-medium text-gray-700">Liga *</label>
                                <select wire:model="league_id" id="league_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Selecciona una liga</option>
                                    @foreach($leagues as $league)
                                        <option value="{{ $league->id }}">{{ $league->name }} - {{ $league->sport->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                                @error('league_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Temporada *</label>
                                <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="ej: Temporada 2024">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Formato y Tipo Round Robin -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="format" class="block text-sm font-medium text-gray-700">Formato *</label>
                                <select wire:model="format" id="format" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Selecciona un formato</option>
                                    <option value="round_robin">Round Robin (Todos contra todos)</option>
                                    <option value="playoff">Playoff (Eliminación directa)</option>
                                    <option value="round_robin_playoff">Round Robin + Playoff (Fase de grupos + Finales)</option>
                                </select>
                                @error('format') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Round Robin: Fase de grupos donde todos juegan contra todos<br>
                                    Playoff: Solo eliminatorias directas<br>
                                    Round Robin + Playoff: Fase de grupos y luego los mejores pasan a eliminatorias
                                </p>
                            </div>

                            @if(in_array($format, ['round_robin', 'round_robin_playoff']))
                            <div>
                                <label for="round_robin_type" class="block text-sm font-medium text-gray-700">Tipo Round Robin *</label>
                                <select wire:model="round_robin_type" id="round_robin_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="single">Una vuelta (Single)</option>
                                    <option value="double">Doble vuelta (Double)</option>
                                </select>
                                @error('round_robin_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>

                        <!-- Fecha de Inicio -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha de Inicio *</label>
                            <input type="date" wire:model="start_date" id="start_date" class="mt-1 block w-full lg:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Esta fecha se usará automáticamente al generar fixtures. La fecha de fin se calculará según los partidos programados.</p>
                            @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Días de Juego -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Días de Juego *</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                                @foreach(['monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo'] as $day => $label)
                                    <label class="flex items-center space-x-2 p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                        <input type="checkbox" wire:model="game_days" value="{{ $day }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('game_days') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Partidos por Día -->
                        <div>
                            <label for="daily_matches" class="block text-sm font-medium text-gray-700">Partidos por Día *</label>
                            <input type="number" wire:model.live="daily_matches" id="daily_matches" min="1" max="10" class="mt-1 block w-full lg:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="ej: 3">
                            <p class="mt-1 text-xs text-gray-500">Número máximo de partidos que se pueden jugar por día</p>
                            <p class="mt-1 text-xs font-semibold text-indigo-600">⚠️ Debes definir exactamente {{ $daily_matches }} horarios abajo</p>
                            @error('daily_matches') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Horarios de Juego -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Horarios de Juego * 
                                <span class="text-xs font-normal text-gray-500">(Deben ser {{ $daily_matches }} horarios)</span>
                            </label>
                            <div class="space-y-3">
                                @foreach($match_times as $index => $time)
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                        <input type="time" wire:model.live="match_times.{{ $index }}" class="block w-full sm:w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <button type="button" wire:click="removeMatchTime({{ $index }})" class="px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition w-full sm:w-auto">
                                            Eliminar
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addMatchTime" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition w-full sm:w-auto">
                                    + Agregar Horario
                                </button>
                            </div>
                            @error('match_times') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado *</label>
                            <select wire:model="status" id="status" class="mt-1 block w-full lg:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="draft">Borrador</option>
                                <option value="upcoming">Próxima</option>
                                <option value="active">Activa</option>
                                <option value="completed">Completada</option>
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row items-center justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('seasons.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Actualizar Temporada
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
