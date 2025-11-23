<div>
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

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Crear Invitación</h1>
                <p class="mt-1 text-sm text-gray-500">Genera un enlace de invitación para nuevos usuarios</p>
            </div>
            <a href="{{ route('invitations.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                ← Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Formulario --}}
        <div class="lg:col-span-2">
            <form wire:submit="create" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                {{-- Tipo de Invitación --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Invitación *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'league_manager' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="league_manager" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">👔</span>
                                    <span class="font-semibold">Encargado de Liga</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gestión completa de la liga</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'coach' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="coach" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🎯</span>
                                    <span class="font-semibold">Entrenador</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gestión de un equipo</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'player' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="player" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">⚽</span>
                                    <span class="font-semibold">Jugador</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Jugador de un equipo</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'referee' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="referee" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🟨</span>
                                    <span class="font-semibold">Árbitro</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Arbitraje de partidos</p>
                            </div>
                        </label>
                    </div>
                    @error('tokenType') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Liga --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Liga *</label>
                    <select wire:model.live="leagueId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecciona una liga</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                    @error('leagueId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Equipo (solo para coach y player) --}}
                @if(in_array($tokenType, ['coach', 'player']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Equipo *</label>
                        <select wire:model="teamId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Selecciona un equipo</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('teamId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                {{-- Configuración --}}
                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Configuración del Token</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Máximo de Usos</label>
                            <input 
                                type="number" 
                                wire:model="maxUses" 
                                min="1" 
                                max="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('maxUses') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expira en (días)</label>
                            <input 
                                type="number" 
                                wire:model="expiresInDays" 
                                min="1" 
                                max="365"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('expiresInDays') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Enviar por Email --}}
                <div class="border-t pt-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input 
                            type="checkbox" 
                            wire:model.live="sendEmail" 
                            id="sendEmail"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="sendEmail" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Enviar invitación por email
                        </label>
                    </div>

                    @if($sendEmail)
                        <div class="space-y-4 pl-7">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email del destinatario *</label>
                                <input 
                                    type="email" 
                                    wire:model="recipientEmail"
                                    placeholder="usuario@ejemplo.com"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                @error('recipientEmail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del destinatario (opcional)</label>
                                <input 
                                    type="text" 
                                    wire:model="recipientName"
                                    placeholder="Juan Pérez"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Botón Crear --}}
                <div class="flex gap-3 pt-6 border-t">
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors"
                    >
                        Crear Invitación
                    </button>
                    <a 
                        href="{{ route('invitations.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        {{-- Información --}}
        <div class="space-y-6">
            {{-- Info Card --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Información</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• El token se genera automáticamente</li>
                    <li>• Puedes configurar usos y expiración</li>
                    <li>• El enlace se puede copiar o enviar por email</li>
                    <li>• Los tokens pueden ser revocados en cualquier momento</li>
                </ul>
            </div>

            {{-- Roles Info --}}
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Permisos por Rol</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="font-medium text-purple-700">👔 Encargado de Liga</div>
                        <p class="text-gray-600 text-xs mt-1">Gestión completa de la liga: equipos, temporadas, partidos, finanzas</p>
                    </div>
                    <div>
                        <div class="font-medium text-blue-700">🎯 Entrenador</div>
                        <p class="text-gray-600 text-xs mt-1">Gestión de su equipo: jugadores, alineaciones</p>
                    </div>
                    <div>
                        <div class="font-medium text-green-700">⚽ Jugador</div>
                        <p class="text-gray-600 text-xs mt-1">Ver partidos, estadísticas personales</p>
                    </div>
                    <div>
                        <div class="font-medium text-yellow-700">🟨 Árbitro</div>
                        <p class="text-gray-600 text-xs mt-1">Gestionar partidos asignados, registrar resultados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
