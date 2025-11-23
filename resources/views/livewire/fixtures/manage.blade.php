<div class="min-h-screen bg-gray-50 py-4 sm:py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('fixtures.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">⚽ Gestión de Partido</h2>
            </div>
            <p class="text-sm text-gray-600 ml-9">{{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}</p>
        </div>

        {{-- Mensajes --}}
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Columna Principal --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Información del Partido --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Partido</h3>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-1">Local</p>
                                <p class="text-xl font-bold text-gray-900">{{ $fixture->homeTeam->name }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-1">Visitante</p>
                                <p class="text-xl font-bold text-gray-900">{{ $fixture->awayTeam->name }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-600">Fecha:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($fixture->match_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-600">Hora:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($fixture->match_time)->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-gray-600">Cancha:</span>
                                <span class="font-medium">{{ $fixture->venue->name ?? 'Por definir' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-gray-600">Árbitro:</span>
                                <span class="font-medium">{{ $fixture->referee->name ?? 'No asignado' }}</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($this->statusColor === 'blue') bg-blue-100 text-blue-800
                                @elseif($this->statusColor === 'green') bg-green-100 text-green-800
                                @elseif($this->statusColor === 'yellow') bg-yellow-100 text-yellow-800
                                @elseif($this->statusColor === 'red') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $this->statusLabel }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Marcador --}}
                @if($fixture->status === 'in_progress' || $fixture->status === 'completed')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Marcador</h3>
                        
                        <div class="grid grid-cols-3 gap-4 items-center">
                            {{-- Equipo Local --}}
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">{{ $fixture->homeTeam->name }}</p>
                                @if($fixture->status === 'in_progress' && ($canManage || $isReferee))
                                    <input type="number" 
                                           wire:model.live="home_score" 
                                           min="0" 
                                           max="99"
                                           class="w-20 mx-auto text-center text-4xl font-bold p-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @else
                                    <p class="text-5xl font-bold text-gray-900">{{ $home_score }}</p>
                                @endif
                                @error('home_score') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- VS --}}
                            <div class="text-center">
                                <span class="text-2xl font-bold text-gray-400">VS</span>
                            </div>

                            {{-- Equipo Visitante --}}
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">{{ $fixture->awayTeam->name }}</p>
                                @if($fixture->status === 'in_progress' && ($canManage || $isReferee))
                                    <input type="number" 
                                           wire:model.live="away_score" 
                                           min="0" 
                                           max="99"
                                           class="w-20 mx-auto text-center text-4xl font-bold p-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @else
                                    <p class="text-5xl font-bold text-gray-900">{{ $away_score }}</p>
                                @endif
                                @error('away_score') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if($fixture->status === 'in_progress' && ($canManage || $isReferee))
                            <div class="mt-4">
                                <button wire:click="updateScore" 
                                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                    Actualizar Marcador
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Notas --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notas del Partido</h3>
                        @if($canManage || $isReferee)
                            <textarea wire:model="notes" 
                                      rows="3"
                                      placeholder="Notas, incidencias, observaciones..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        @else
                            <p class="text-gray-600">{{ $notes ?: 'Sin notas' }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Columna Lateral --}}
            <div class="space-y-6">
                
                {{-- Asignación de Árbitro --}}
                @if($canManage && $fixture->status === 'scheduled')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Asignar Árbitro</h3>
                        <select wire:model="referee_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3">
                            <option value="">Sin árbitro</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="assignReferee" 
                                class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                            Asignar
                        </button>
                    </div>
                </div>
                @endif

                {{-- Acciones del Partido --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>
                        
                        <div class="space-y-3">
                            @if($fixture->status === 'scheduled' && ($canManage || $isReferee))
                                <button wire:click="startMatch" 
                                        wire:confirm="¿Estás seguro de iniciar el partido?"
                                        class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                    ▶️ Iniciar Partido
                                </button>
                            @endif

                            @if($fixture->status === 'in_progress' && ($canManage || $isReferee))
                                <button wire:click="finishMatch" 
                                        wire:confirm="¿Confirmar finalización del partido con marcador {{ $home_score }} - {{ $away_score }}?"
                                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                    🏁 Finalizar Partido
                                </button>
                            @endif

                            @if($fixture->status === 'scheduled' && $canManage)
                                <button wire:click="postponeMatch" 
                                        wire:confirm="¿Posponer este partido?"
                                        class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                                    ⏸️ Posponer
                                </button>
                            @endif

                            @if(in_array($fixture->status, ['scheduled', 'postponed']) && $canManage)
                                <button wire:click="cancelMatch" 
                                        wire:confirm="¿Cancelar definitivamente este partido?"
                                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                    ❌ Cancelar
                                </button>
                            @endif

                            @if($fixture->status === 'completed')
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <p class="text-sm text-gray-600">✅ Partido finalizado</p>
                                    <p class="text-xs text-gray-500 mt-1">Los ingresos y pagos se generaron automáticamente</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Info de Permisos --}}
                @if(!$canManage && !$isReferee)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        ℹ️ Solo puedes ver la información. No tienes permisos para modificar este partido.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
