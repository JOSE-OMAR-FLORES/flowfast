<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nuevo Jugador</h1>
                <p class="mt-1 text-sm text-gray-500">Agrega un nuevo jugador al equipo</p>
            </div>
            <a href="{{ route('players.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                ← Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Formulario --}}
        <div class="lg:col-span-2">
            <form wire:submit="create" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                {{-- Información Básica --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                            <input 
                                type="text" 
                                wire:model="first_name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Juan"
                            >
                            @error('first_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apellido *</label>
                            <input 
                                type="text" 
                                wire:model="last_name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Pérez"
                            >
                            @error('last_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input 
                                type="email" 
                                wire:model="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="jugador@ejemplo.com"
                            >
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <input 
                                type="text" 
                                wire:model="phone"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="+52 123 456 7890"
                            >
                            @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                            <input 
                                type="date" 
                                wire:model="birth_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('birth_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                            <input 
                                type="file" 
                                wire:model="photo"
                                accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            >
                            @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            @if ($photo)
                                <div class="mt-2">
                                    <img src="{{ $photo->temporaryUrl() }}" class="h-20 w-20 rounded-full object-cover">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Información Deportiva --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Deportiva</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Liga *</label>
                            <select wire:model.live="league_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Selecciona una liga</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                                @endforeach
                            </select>
                            @error('league_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo *</label>
                            <select wire:model="team_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Selecciona un equipo</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número de Camiseta</label>
                            <input 
                                type="number" 
                                wire:model="jersey_number"
                                min="0"
                                max="999"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="10"
                            >
                            @error('jersey_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Posición *</label>
                            <select wire:model="position" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Selecciona una posición</option>
                                @foreach($positions as $key => $label)
                                    <option value="{{ $key }}">
                                        @if($key === 'goalkeeper') 🧤
                                        @elseif($key === 'defender') 🛡️
                                        @elseif($key === 'midfielder') ⚙️
                                        @elseif($key === 'forward') ⚽
                                        @endif
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                            <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                            <textarea 
                                wire:model="notes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Notas adicionales sobre el jugador..."
                            ></textarea>
                            @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex gap-3 pt-6 border-t">
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors"
                    >
                        Crear Jugador
                    </button>
                    <a 
                        href="{{ route('players.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        {{-- Información Lateral --}}
        <div class="space-y-6">
            {{-- Info Card --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Información</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• Los campos con * son obligatorios</li>
                    <li>• El número de camiseta debe ser único por equipo</li>
                    <li>• La foto debe ser JPG, PNG o GIF (máx 2MB)</li>
                    <li>• Las estadísticas se actualizan automáticamente con los partidos</li>
                </ul>
            </div>

            {{-- Posiciones Info --}}
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Posiciones</h3>
                <div class="space-y-2 text-sm">
                    <div>🧤 <span class="font-medium">Portero:</span> Defiende la portería</div>
                    <div>🛡️ <span class="font-medium">Defensa:</span> Protege la zona defensiva</div>
                    <div>⚙️ <span class="font-medium">Mediocampista:</span> Conecta defensa y ataque</div>
                    <div>⚽ <span class="font-medium">Delantero:</span> Busca marcar goles</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @script
    <script>
        $wire.on('player-created', (playerName) => {
            alert('Jugador "' + playerName + '" creado exitosamente!');
            window.location.href = '{{ route("players.index") }}';
        });

        $wire.on('error', (message) => {
            alert('Error: ' + message);
        });
    </script>
    @endscript
</div>
