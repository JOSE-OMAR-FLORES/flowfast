<div class="p-6">
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📊 Tabla de Posiciones</h1>
            <p class="text-sm text-gray-600 mt-1">Consulta las posiciones de los equipos en cada temporada</p>
        </div>
        
        @if(auth()->user()->hasRole('admin'))
        <button wire:click="recalculate" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
                wire:loading.attr="disabled">
            <svg wire:loading.remove wire:target="recalculate" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <svg wire:loading wire:target="recalculate" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Recalcular</span>
        </button>
        @endif
    </div>

    {{-- Mensajes de éxito/error --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Selector de Liga --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    🏆 Liga
                </label>
                <select wire:model.live="selectedLeagueId" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Selecciona una liga</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}">
                            {{ $league->name }} ({{ $league->sport->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Selector de Temporada --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Temporada
                </label>
                <select wire:model.live="selectedSeasonId" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        @if(empty($seasons) || count($seasons) === 0) disabled @endif>
                    <option value="">Selecciona una temporada</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}">
                            {{ $season->name }}
                            @if($season->status === 'active') ● @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tabla de Posiciones --}}
    @if($standings && count($standings) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pos</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Equipo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">PJ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">PG</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">PE</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">PP</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">GF</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">GC</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">DG</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Racha</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider font-bold">PTS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($standings as $standing)
                            @php
                                $isCoachTeam = in_array($standing->team_id, $coachTeamIds ?? []);
                            @endphp
                            <tr class="transition-colors
                                @if($isCoachTeam) bg-indigo-100 border-l-4 border-indigo-500 hover:bg-indigo-200
                                @elseif($standing->position === 1) bg-green-50 hover:bg-green-100
                                @elseif($standing->position === 2) bg-blue-50 hover:bg-blue-100
                                @elseif($standing->position === 3) bg-yellow-50 hover:bg-yellow-100
                                @else hover:bg-gray-50
                                @endif">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900">{{ $standing->position }}</span>
                                        @if($standing->position === 1)
                                            <span class="text-yellow-500">🥇</span>
                                        @elseif($standing->position === 2)
                                            <span class="text-gray-400">🥈</span>
                                        @elseif($standing->position === 3)
                                            <span class="text-orange-600">🥉</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($standing->team->club && $standing->team->club->logo)
                                            <img src="{{ Storage::url($standing->team->club->logo) }}" 
                                                 alt="{{ $standing->team->name }}" 
                                                 class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-xs font-bold text-gray-600">
                                                    {{ strtoupper(substr($standing->team->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-900 @if($isCoachTeam) font-bold text-indigo-700 @endif">
                                            {{ $standing->team->name }}
                                            @if($isCoachTeam)
                                                <span class="ml-1 text-xs bg-indigo-600 text-white px-2 py-0.5 rounded-full">Tu equipo</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">{{ $standing->played }}</td>
                                <td class="px-4 py-3 text-center text-sm text-green-600 font-semibold">{{ $standing->won }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $standing->drawn }}</td>
                                <td class="px-4 py-3 text-center text-sm text-red-600 font-semibold">{{ $standing->lost }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">{{ $standing->goals_for }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">{{ $standing->goals_against }}</td>
                                <td class="px-4 py-3 text-center text-sm font-semibold 
                                    @if($standing->goal_difference > 0) text-green-600
                                    @elseif($standing->goal_difference < 0) text-red-600
                                    @else text-gray-600
                                    @endif">
                                    {{ $standing->goal_difference > 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                    <div class="flex items-center justify-center gap-0.5">
                                        @if($standing->form)
                                            @foreach(str_split($standing->form) as $result)
                                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold text-white
                                                    @if($result === 'W') bg-green-500
                                                    @elseif($result === 'D') bg-gray-400
                                                    @elseif($result === 'L') bg-red-500
                                                    @endif">
                                                    {{ $result }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-lg text-blue-600">{{ $standing->points }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                @foreach($standings as $standing)
                    @php
                        $isCoachTeamMobile = in_array($standing->team_id, $coachTeamIds ?? []);
                    @endphp
                    <div class="p-4 
                        @if($isCoachTeamMobile) bg-indigo-100 border-l-4 border-indigo-500
                        @elseif($standing->position === 1) bg-green-50
                        @elseif($standing->position === 2) bg-blue-50
                        @elseif($standing->position === 3) bg-yellow-50
                        @endif">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="text-xl font-bold text-gray-900">{{ $standing->position }}</span>
                                @if($standing->position === 1)
                                    <span class="text-2xl">🥇</span>
                                @elseif($standing->position === 2)
                                    <span class="text-2xl">🥈</span>
                                @elseif($standing->position === 3)
                                    <span class="text-2xl">🥉</span>
                                @endif
                                @if($standing->team->club && $standing->team->club->logo)
                                    <img src="{{ Storage::url($standing->team->club->logo) }}" 
                                         alt="{{ $standing->team->name }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-sm font-bold text-gray-600">
                                            {{ strtoupper(substr($standing->team->name, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="font-semibold @if($isCoachTeamMobile) text-indigo-700 @else text-gray-900 @endif">{{ $standing->team->name }}</span>
                                    @if($isCoachTeamMobile)
                                        <span class="text-xs bg-indigo-600 text-white px-2 py-0.5 rounded-full w-fit mt-1">Tu equipo</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-2xl font-bold @if($isCoachTeamMobile) text-indigo-600 @else text-blue-600 @endif">{{ $standing->points }}</span>
                        </div>

                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div class="bg-white rounded p-2">
                                <div class="text-xs text-gray-600 mb-1">PJ</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $standing->played }}</div>
                            </div>
                            <div class="bg-white rounded p-2">
                                <div class="text-xs text-gray-600 mb-1">PG-PE-PP</div>
                                <div class="text-sm font-semibold">
                                    <span class="text-green-600">{{ $standing->won }}</span>-
                                    <span class="text-gray-600">{{ $standing->drawn }}</span>-
                                    <span class="text-red-600">{{ $standing->lost }}</span>
                                </div>
                            </div>
                            <div class="bg-white rounded p-2">
                                <div class="text-xs text-gray-600 mb-1">GF-GC</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $standing->goals_for }}-{{ $standing->goals_against }}</div>
                            </div>
                        </div>

                        {{-- Form --}}
                        @if($standing->form)
                            <div class="mt-3 flex items-center justify-center gap-1">
                                @foreach(str_split($standing->form) as $result)
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white
                                        @if($result === 'W') bg-green-500
                                        @elseif($result === 'D') bg-gray-400
                                        @elseif($result === 'L') bg-red-500
                                        @endif">
                                        {{ $result }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">📖 Leyenda</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-xs text-gray-600">
                <div><span class="font-semibold">PJ:</span> Partidos Jugados</div>
                <div><span class="font-semibold">PG:</span> Partidos Ganados</div>
                <div><span class="font-semibold">PE:</span> Partidos Empatados</div>
                <div><span class="font-semibold">PP:</span> Partidos Perdidos</div>
                <div><span class="font-semibold">GF:</span> Goles a Favor</div>
                <div><span class="font-semibold">GC:</span> Goles en Contra</div>
                <div><span class="font-semibold">DG:</span> Diferencia de Goles</div>
                <div><span class="font-semibold">PTS:</span> Puntos</div>
                <div><span class="font-semibold">W/D/L:</span> Victoria/Empate/Derrota</div>
            </div>
        </div>
    @elseif($selectedSeasonId)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-6xl mb-4">📊</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay datos de posiciones</h3>
            <p class="text-gray-600 mb-4">Esta temporada aún no tiene partidos completados.</p>
            @if(auth()->user()->hasRole('admin'))
                <button wire:click="recalculate" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Inicializar Tabla
                </button>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Selecciona una liga y temporada</h3>
            <p class="text-gray-600">Usa los filtros de arriba para ver la tabla de posiciones</p>
        </div>
    @endif
</div>
