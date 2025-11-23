<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🤝 Crear Partido Amistoso</h1>
                    <p class="mt-2 text-sm text-gray-600">Organiza un partido amistoso entre equipos del mismo deporte</p>
                </div>
                <a href="{{ route('friendly-matches.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                    ← Volver
                </a>
            </div>
        </div>

        {{-- Alertas --}}
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Formulario --}}
        <form wire:submit="create" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">
                
                {{-- Selección de Deporte --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Deporte</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selecciona el deporte *</label>
                        <select wire:model.live="sport_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('sport_id') border-red-500 @enderror">
                            <option value="">-- Selecciona un deporte --</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                        @error('sport_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if($sport_id && $teams->count() > 0)
                    {{-- Selección de Equipos --}}
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Equipos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Equipo Local --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Equipo Local *</label>
                                <select wire:model.live="home_team_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('home_team_id') border-red-500 @enderror">
                                    <option value="">-- Selecciona equipo local --</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}">
                                            {{ $team->name }} - {{ $team->season->league->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('home_team_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Equipo Visitante --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Equipo Visitante *</label>
                                <select wire:model.live="away_team_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('away_team_id') border-red-500 @enderror">
                                    <option value="">-- Selecciona equipo visitante --</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ $team->id == $home_team_id ? 'disabled' : '' }}>
                                            {{ $team->name }} - {{ $team->season->league->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('away_team_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Fecha, Hora y Lugar --}}
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Fecha y Lugar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Fecha --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha del Partido *</label>
                                <input type="date" wire:model="match_date" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('match_date') border-red-500 @enderror">
                                @error('match_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Hora --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hora *</label>
                                <input type="time" wire:model="match_time" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('match_time') border-red-500 @enderror">
                                @error('match_time') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Venue --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cancha (Opcional)</label>
                                <select wire:model="venue_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white">
                                    <option value="">Sin cancha asignada</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Árbitro --}}
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Árbitro</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Árbitro (Opcional)</label>
                            <select wire:model="referee_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white">
                                <option value="">Sin árbitro asignado</option>
                                @foreach($referees as $referee)
                                    <option value="{{ $referee->id }}">
                                        {{ $referee->full_name }} - {{ $referee->league->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                💡 Puedes seleccionar árbitros de cualquier liga del mismo deporte
                            </p>
                        </div>
                    </div>

                    {{-- Configuración Financiera --}}
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Configuración Financiera</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Cuota Equipo Local --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cuota Equipo Local *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" wire:model="home_team_fee" step="0.01" min="0" 
                                           class="w-full pl-8 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('home_team_fee') border-red-500 @enderror">
                                </div>
                                @error('home_team_fee') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Cuota Equipo Visitante --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cuota Equipo Visitante *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" wire:model="away_team_fee" step="0.01" min="0" 
                                           class="w-full pl-8 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('away_team_fee') border-red-500 @enderror">
                                </div>
                                @error('away_team_fee') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Pago a Árbitro --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pago a Árbitro *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" wire:model="referee_fee" step="0.01" min="0" 
                                           class="w-full pl-8 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white @error('referee_fee') border-red-500 @enderror">
                                </div>
                                @error('referee_fee') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-700">
                                ℹ️ Los pagos se generarán automáticamente y aparecerán en el sistema financiero (ingresos/egresos)
                            </p>
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notas</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notas del partido (Opcional)</label>
                            <textarea wire:model="friendly_notes" rows="3" 
                                      class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white resize-none"
                                      placeholder="Ej: Partido benéfico, torneo relámpago, etc."></textarea>
                            @error('friendly_notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @else
                    @if($sport_id)
                        <div class="border-t pt-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <p class="text-yellow-800">⚠️ No hay equipos disponibles para este deporte</p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Botones --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end gap-3">
                    <a href="{{ route('friendly-matches.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            @if(!$sport_id || $teams->count() == 0) disabled @endif>
                        Crear Partido Amistoso
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
