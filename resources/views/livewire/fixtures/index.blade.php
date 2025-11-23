<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">📅 Calendario de Partidos</h2>
                <p class="text-sm text-gray-600 mt-1">Gestión de fixtures organizados por liga y jornada</p>
            </div>
            
            @if(auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'league_manager')
                <a href="{{ route('fixtures.generate') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Generar Fixtures
                </a>
            @endif
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

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Búsqueda --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Equipo o cancha..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Filtro Liga --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Liga</label>
                    <select wire:model.live="leagueFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las ligas</option>
                        @foreach($allLeagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro Temporada --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Temporada</label>
                    <select wire:model.live="seasonFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las temporadas</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}">{{ $season->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="statusFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="scheduled">Programado</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completado</option>
                        <option value="postponed">Pospuesto</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Acordeones de Ligas --}}
        <div class="space-y-4">
            @forelse($leagues as $league)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Header de Liga (Clic para expandir) --}}
                    <button wire:click="toggleLeague({{ $league->id }})" 
                            class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                                {{ substr($league->name, 0, 1) }}
                            </div>
                            <div class="text-left">
                                <h3 class="text-lg font-bold text-gray-900">{{ $league->name }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $league->seasons->count() }} temporada(s) · 
                                    {{ $league->seasons->sum(function($s) { return $s->rounds->count(); }) }} jornada(s)
                                </p>
                            </div>
                        </div>
                        <svg class="w-6 h-6 text-gray-400 transform transition-transform {{ in_array($league->id, $expandedLeagues) ? 'rotate-180' : '' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    {{-- Contenido de la Liga (Temporadas) --}}
                    @if(in_array($league->id, $expandedLeagues))
                        <div class="border-t border-gray-200 bg-gray-50 p-4">
                            @foreach($league->seasons as $season)
                                <div class="mb-4 last:mb-0">
                                    <div class="flex items-center justify-between mb-3 px-2">
                                        <h4 class="text-md font-semibold text-gray-800">{{ $season->name }}</h4>
                                        
                                        @if(auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'league_manager')
                                            <button wire:click="deleteAllFixtures({{ $season->id }})" 
                                                    wire:confirm="¿Estás seguro de eliminar TODOS los fixtures de la temporada {{ $season->name }}? Esta acción no se puede deshacer."
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar Todos los Fixtures
                                            </button>
                                        @endif
                                    </div>
                                    
                                    {{-- Jornadas --}}
                                    <div class="space-y-2">
                                        @foreach($season->rounds as $round)
                                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                                {{-- Header de Jornada --}}
                                                <button wire:click="toggleRound('{{ $round->id }}')" 
                                                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold">
                                                            {{ $round->round_number }}
                                                        </div>
                                                        <div class="text-left">
                                                            <p class="text-sm font-semibold text-gray-900">Jornada {{ $round->round_number }}</p>
                                                            <p class="text-xs text-gray-600">{{ $round->fixtures->count() }} partido(s)</p>
                                                        </div>
                                                    </div>
                                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform {{ in_array($round->id, $expandedRounds) ? 'rotate-180' : '' }}" 
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>

                                                {{-- Partidos de la Jornada --}}
                                                @if(in_array($round->id, $expandedRounds))
                                                    <div class="border-t border-gray-200 p-4 bg-gray-50">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            @foreach($round->fixtures as $fixture)
                                                                <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
                                                                    {{-- Fecha y Hora --}}
                                                                    <div class="flex items-center gap-2 text-xs text-gray-600 mb-3">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                        </svg>
                                                                        {{ \Carbon\Carbon::parse($fixture->match_date)->format('d/m/Y') }} - 
                                                                        {{ \Carbon\Carbon::parse($fixture->match_time)->format('H:i') }}
                                                                    </div>

                                                                    {{-- Equipos --}}
                                                                    <div class="flex items-center justify-between mb-3">
                                                                        <div class="flex-1 text-right">
                                                                            <p class="font-semibold text-gray-900">{{ $fixture->homeTeam->name }}</p>
                                                                            <p class="text-xs text-gray-600">Local</p>
                                                                        </div>
                                                                        <div class="px-4 py-2 bg-gray-100 rounded-lg mx-3">
                                                                            <span class="text-sm font-bold text-gray-700">VS</span>
                                                                        </div>
                                                                        <div class="flex-1">
                                                                            <p class="font-semibold text-gray-900">{{ $fixture->awayTeam->name }}</p>
                                                                            <p class="text-xs text-gray-600">Visitante</p>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Cancha --}}
                                                                    @if($fixture->venue)
                                                                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                            </svg>
                                                                            {{ $fixture->venue->name ?? 'Por definir' }}
                                                                        </div>
                                                                    @endif

                                                                    {{-- Estado --}}
                                                                    <div class="flex items-center justify-between">
                                                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                                            {{ $fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                            {{ $fixture->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                            {{ $fixture->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                                            {{ $fixture->status === 'postponed' ? 'bg-orange-100 text-orange-800' : '' }}
                                                                            {{ $fixture->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                                            {{ $fixture->getStatusLabel() }}
                                                                        </span>

                                                                        <div class="flex items-center gap-2">
                                                                            {{-- Botón Gestionar en Vivo --}}
                                                                            @if(in_array(auth()->user()->user_type, ['admin', 'league_manager', 'referee']))
                                                                                <a href="{{ route('matches.live', ['matchId' => $fixture->id]) }}" 
                                                                                   class="px-3 py-1 {{ $fixture->status === 'in_progress' || $fixture->status === 'live' ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white text-xs font-medium rounded-lg transition-colors">
                                                                                    @if($fixture->status === 'in_progress' || $fixture->status === 'live')
                                                                                        🔴 En Vivo
                                                                                    @else
                                                                                        ⚽ Gestionar
                                                                                    @endif
                                                                                </a>
                                                                            @endif
                                                                            
                                                                            @if(auth()->user()->user_type === 'admin' && $fixture->status !== 'completed')
                                                                                <button wire:click="delete({{ $fixture->id }})" 
                                                                                        wire:confirm="¿Estás seguro de eliminar este partido?"
                                                                                        class="text-red-600 hover:text-red-800 text-sm">
                                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                                    </svg>
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay fixtures disponibles</h3>
                    <p class="text-gray-600 mb-4">Genera nuevos fixtures para comenzar</p>
                    @if(auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'league_manager')
                        <a href="{{ route('fixtures.generate') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Generar Fixtures
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</div>
