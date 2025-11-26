<div class="min-h-screen bg-slate-950">
    <style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    /* Grid pattern */
    .bg-grid-white\/\[0\.02\] {
        background-image: linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                          linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
    }
    </style>

    {{-- Header de la Liga --}}
    <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white py-12 overflow-hidden">
        {{-- Decorative elements --}}
        <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:20px_20px]"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
        
        <div class="relative container mx-auto px-4">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-4xl backdrop-blur-sm border border-cyan-500/20">
                    {{ $league->sport->emoji ?? '⚽' }}
                </div>
                <div>
                    <h1 class="text-4xl font-bold">{{ $league->name }}</h1>
                    @if($activeSeason)
                        <p class="text-cyan-300 mt-1 flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            {{ $activeSeason->name }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Navegación --}}
    <div class="bg-slate-900/95 backdrop-blur-sm border-b border-slate-800 sticky top-0 z-10 shadow-lg">
        <div class="container mx-auto px-4">
            <nav class="flex gap-1 overflow-x-auto scrollbar-hide">
                <a href="{{ url('/league/' . $league->slug) }}" 
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
                    🏠 Inicio
                </a>
                <a href="{{ url('/league/' . $league->slug . '/fixtures') }}" 
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
                    📅 Calendario
                </a>
                <a href="{{ url('/league/' . $league->slug . '/standings') }}" 
                   class="py-4 px-6 bg-slate-800 text-cyan-400 border-b-2 border-cyan-400 font-semibold rounded-t-lg whitespace-nowrap">
                    📊 Posiciones
                </a>
                <a href="{{ url('/league/' . $league->slug . '/teams') }}" 
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
                    👥 Equipos
                </a>
            </nav>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="container mx-auto px-4 py-8">
        @if($standings->isEmpty())
            {{-- Estado Vacío --}}
            <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 p-16 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No hay tabla de posiciones</h3>
                <p class="text-slate-400">La clasificación aparecerá cuando comiencen los partidos</p>
            </div>
        @else
            {{-- Tabla Desktop --}}
            <div class="hidden md:block bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-800/50 border-b border-slate-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Pos</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Equipo</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Partidos Jugados">PJ</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Ganados">G</th>
                                @if($allowsDraws)
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Empatados">E</th>
                                @endif
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Perdidos">P</th>
                                
                                @if($sportSlug === 'basquetbol')
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Puntos a Favor">PF</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Puntos en Contra">PC</th>
                                @elseif($sportSlug === 'voleibol')
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Sets Ganados">SG</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Sets Perdidos">SP</th>
                                @elseif($sportSlug === 'beisbol')
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Carreras a Favor">CF</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Carreras en Contra">CC</th>
                                @else
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Goles a Favor">GF</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Goles en Contra">GC</th>
                                @endif
                                
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Diferencia">Dif</th>
                                
                                @if(in_array($sportSlug, ['basquetbol', 'beisbol']))
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Porcentaje de Victorias">%</th>
                                @endif
                                
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider" title="Puntos">Pts</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Forma</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($standings as $index => $standing)
                                <tr class="hover:bg-slate-800/30 transition-colors group {{ $index < 3 ? 'bg-slate-800/20' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold text-lg {{ $index < 3 ? 'text-cyan-400' : 'text-slate-300' }}">
                                                {{ $index + 1 }}
                                            </span>
                                            @if($index === 0)
                                                <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center shadow-lg shadow-yellow-500/25">
                                                    <span class="text-lg">🥇</span>
                                                </div>
                                            @elseif($index === 1)
                                                <div class="w-8 h-8 bg-gradient-to-br from-gray-300 to-gray-500 rounded-lg flex items-center justify-center shadow-lg shadow-gray-500/25">
                                                    <span class="text-lg">🥈</span>
                                                </div>
                                            @elseif($index === 2)
                                                <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center shadow-lg shadow-orange-500/25">
                                                    <span class="text-lg">🥉</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-slate-700 to-slate-800 rounded-lg flex items-center justify-center text-xs font-bold text-slate-300 border border-slate-600 group-hover:border-cyan-500/50 transition-colors">
                                                {{ strtoupper(substr($standing->team->name, 0, 3)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-white group-hover:text-cyan-400 transition-colors">
                                                    {{ $standing->team->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-300">{{ $standing->played }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-300">{{ $standing->won }}</td>
                                    @if($allowsDraws)
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-300">{{ $standing->drawn }}</td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-300">{{ $standing->lost }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-300">{{ $standing->goals_for }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-300">{{ $standing->goals_against }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold {{ $standing->goal_difference > 0 ? 'bg-green-500/20 text-green-400' : ($standing->goal_difference < 0 ? 'bg-red-500/20 text-red-400' : 'bg-slate-700 text-slate-400') }}">
                                            {{ $standing->goal_difference > 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                        </span>
                                    </td>
                                    
                                    @if(in_array($sportSlug, ['basquetbol', 'beisbol']))
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-300">
                                            @php
                                                $winPct = $standing->played > 0 ? round(($standing->won / $standing->played) * 100, 1) : 0;
                                            @endphp
                                            {{ number_format($winPct, 1) }}%
                                        </td>
                                    @endif
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 font-bold text-lg text-cyan-400 shadow-lg shadow-cyan-500/10">
                                            {{ $standing->points }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($standing->form)
                                            <div class="flex gap-1 justify-center">
                                                @foreach(str_split(substr($standing->form, -5)) as $result)
                                                    @if($result === 'W')
                                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                                                            <span class="text-white text-xs font-bold">V</span>
                                                        </div>
                                                    @elseif($result === 'D')
                                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center shadow-lg">
                                                            <span class="text-white text-xs font-bold">E</span>
                                                        </div>
                                                    @elseif($result === 'L')
                                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg">
                                                            <span class="text-white text-xs font-bold">D</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-slate-600 text-xs">-</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Cards Mobile --}}
            <div class="md:hidden space-y-4">
                @foreach($standings as $index => $standing)
                    <div class="bg-slate-900/50 backdrop-blur-sm rounded-xl border border-slate-800 p-5 hover:border-cyan-500/50 transition-all {{ $index < 3 ? 'border-cyan-500/30' : '' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl font-bold {{ $index < 3 ? 'text-cyan-400' : 'text-slate-400' }}">
                                    {{ $index + 1 }}
                                </span>
                                @if($index === 0)
                                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center shadow-lg">
                                        <span class="text-xl">🥇</span>
                                    </div>
                                @elseif($index === 1)
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-300 to-gray-500 rounded-lg flex items-center justify-center shadow-lg">
                                        <span class="text-xl">🥈</span>
                                    </div>
                                @elseif($index === 2)
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                                        <span class="text-xl">🥉</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30">
                                <span class="text-2xl font-bold text-cyan-400">{{ $standing->points }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-slate-700 to-slate-800 rounded-lg flex items-center justify-center text-xs font-bold text-slate-300 border border-slate-600">
                                {{ strtoupper(substr($standing->team->name, 0, 3)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-lg text-white">{{ $standing->team->name }}</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-3 mb-4">
                            <div class="text-center bg-slate-800/50 rounded-lg py-2">
                                <div class="text-xs text-slate-500 mb-1">PJ</div>
                                <div class="font-bold text-white">{{ $standing->played }}</div>
                            </div>
                            <div class="text-center bg-slate-800/50 rounded-lg py-2">
                                <div class="text-xs text-slate-500 mb-1">
                                    @if($allowsDraws)
                                        G-E-P
                                    @else
                                        G-P
                                    @endif
                                </div>
                                <div class="font-bold text-white text-sm">
                                    @if($allowsDraws)
                                        {{ $standing->won }}-{{ $standing->drawn }}-{{ $standing->lost }}
                                    @else
                                        {{ $standing->won }}-{{ $standing->lost }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-center bg-slate-800/50 rounded-lg py-2">
                                <div class="text-xs text-slate-500 mb-1">
                                    @if($sportSlug === 'basquetbol')
                                        Puntos
                                    @elseif($sportSlug === 'voleibol')
                                        Sets
                                    @elseif($sportSlug === 'beisbol')
                                        Carreras
                                    @else
                                        Goles
                                    @endif
                                </div>
                                <div class="font-bold text-white text-sm">{{ $standing->goals_for }}-{{ $standing->goals_against }}</div>
                            </div>
                            <div class="text-center bg-slate-800/50 rounded-lg py-2">
                                <div class="text-xs text-slate-500 mb-1">Dif</div>
                                <div class="font-bold {{ $standing->goal_difference > 0 ? 'text-green-400' : ($standing->goal_difference < 0 ? 'text-red-400' : 'text-slate-400') }}">
                                    {{ $standing->goal_difference > 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                </div>
                            </div>
                        </div>

                        @if(in_array($sportSlug, ['basquetbol', 'beisbol']))
                            <div class="text-center bg-slate-800/50 rounded-lg py-2 mb-4">
                                <div class="text-xs text-slate-500 mb-1">Porcentaje de Victorias</div>
                                @php
                                    $winPct = $standing->played > 0 ? round(($standing->won / $standing->played) * 100, 1) : 0;
                                @endphp
                                <div class="font-bold text-cyan-400">{{ number_format($winPct, 1) }}%</div>
                            </div>
                        @endif

                        @if($standing->form)
                            <div class="flex gap-1.5 justify-center pt-3 border-t border-slate-800">
                                @foreach(str_split(substr($standing->form, -5)) as $result)
                                    @if($result === 'W')
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                                            <span class="text-white text-xs font-bold">V</span>
                                        </div>
                                    @elseif($result === 'D')
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center shadow-lg">
                                            <span class="text-white text-xs font-bold">E</span>
                                        </div>
                                    @elseif($result === 'L')
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg">
                                            <span class="text-white text-xs font-bold">D</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Leyenda Dinámica --}}
            <div class="mt-8 bg-slate-900/50 backdrop-blur-sm rounded-xl border border-slate-800 p-6">
                <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Leyenda</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1">
                            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                                <span class="text-white text-xs font-bold">V</span>
                            </div>
                        </div>
                        <span class="text-sm text-slate-300">Victoria</span>
                    </div>
                    @if($allowsDraws)
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1">
                            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center shadow-lg">
                                <span class="text-white text-xs font-bold">E</span>
                            </div>
                        </div>
                        <span class="text-sm text-slate-300">Empate</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1">
                            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg">
                                <span class="text-white text-xs font-bold">D</span>
                            </div>
                        </div>
                        <span class="text-sm text-slate-300">Derrota</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-xs text-slate-500">
                            @if($sportSlug === 'basquetbol')
                                PJ: Partidos Jugados | PF: Puntos a Favor | PC: Puntos en Contra
                            @elseif($sportSlug === 'voleibol')
                                PJ: Partidos Jugados | SG: Sets Ganados | SP: Sets Perdidos
                            @elseif($sportSlug === 'beisbol')
                                PJ: Partidos Jugados | CF: Carreras a Favor | CC: Carreras en Contra
                            @else
                                PJ: Partidos Jugados | GF: Goles a Favor | GC: Goles en Contra
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div> 