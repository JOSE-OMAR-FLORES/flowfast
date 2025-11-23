<div>
    {{-- Header de la Liga --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-4xl">{{ $league->sport->emoji ?? '⚽' }}</span>
                <h1 class="text-3xl font-bold">{{ $league->name }}</h1>
            </div>
            @if($activeSeason)
                <p class="text-blue-100">{{ $activeSeason->name }}</p>
            @endif
        </div>
    </div>

    {{-- Navegación --}}
    <div class="bg-white border-b sticky top-0 z-10 shadow-sm">
        <div class="container mx-auto px-4">
            <nav class="flex gap-8 overflow-x-auto">
                <a href="{{ url('/league/' . $league->slug) }}" class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                    Inicio
                </a>
                <a href="{{ url('/league/' . $league->slug . '/fixtures') }}" class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                    Calendario
                </a>
                <a href="{{ url('/league/' . $league->slug . '/standings') }}" class="py-4 px-2 border-b-2 border-blue-500 text-blue-600 font-semibold whitespace-nowrap">
                    Posiciones
                </a>
                <a href="{{ url('/league/' . $league->slug . '/teams') }}" class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                    Equipos
                </a>
            </nav>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="container mx-auto px-4 py-8">
        @if($standings->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg">No hay tabla de posiciones aún</p>
            </div>
        @else
            {{-- Tabla Desktop --}}
            <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PJ</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">G</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">E</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">P</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GF</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GC</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dif</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pts</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Forma</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($standings as $index => $standing)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900">{{ $index + 1 }}</span>
                                        @if($index === 0)
                                            <span class="text-yellow-400">🥇</span>
                                        @elseif($index === 1)
                                            <span class="text-gray-400">🥈</span>
                                        @elseif($index === 2)
                                            <span class="text-orange-400">🥉</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ $standing->team->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $standing->team->club->name ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $standing->played }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $standing->won }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $standing->drawn }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $standing->lost }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $standing->goals_for }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $standing->goals_against }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <span class="font-semibold {{ $standing->goal_difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $standing->goal_difference > 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <span class="font-bold text-blue-600">{{ $standing->points }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($standing->form)
                                        <div class="flex gap-1 justify-center">
                                            @foreach(str_split(substr($standing->form, -5)) as $result)
                                                @if($result === 'W')
                                                    <span class="w-6 h-6 rounded-full bg-green-500 text-white text-xs flex items-center justify-center font-bold">V</span>
                                                @elseif($result === 'D')
                                                    <span class="w-6 h-6 rounded-full bg-gray-400 text-white text-xs flex items-center justify-center font-bold">E</span>
                                                @elseif($result === 'L')
                                                    <span class="w-6 h-6 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold">D</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Cards Mobile --}}
            <div class="md:hidden space-y-4">
                @foreach($standings as $index => $standing)
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-gray-400">{{ $index + 1 }}</span>
                                @if($index === 0)
                                    <span class="text-2xl">🥇</span>
                                @elseif($index === 1)
                                    <span class="text-2xl">🥈</span>
                                @elseif($index === 2)
                                    <span class="text-2xl">🥉</span>
                                @endif
                            </div>
                            <span class="text-2xl font-bold text-blue-600">{{ $standing->points }} pts</span>
                        </div>
                        <div class="font-semibold text-lg mb-1">{{ $standing->team->name }}</div>
                        <div class="text-sm text-gray-500 mb-3">{{ $standing->team->club->name ?? '' }}</div>
                        
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-center">
                                <div class="text-gray-500">PJ</div>
                                <div class="font-semibold">{{ $standing->played }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-500">G-E-P</div>
                                <div class="font-semibold">{{ $standing->won }}-{{ $standing->drawn }}-{{ $standing->lost }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-500">Dif</div>
                                <div class="font-semibold {{ $standing->goal_difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $standing->goal_difference > 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                </div>
                            </div>
                        </div>

                        @if($standing->form)
                            <div class="mt-3 flex gap-1 justify-center">
                                @foreach(str_split(substr($standing->form, -5)) as $result)
                                    @if($result === 'W')
                                        <span class="w-6 h-6 rounded-full bg-green-500 text-white text-xs flex items-center justify-center font-bold">V</span>
                                    @elseif($result === 'D')
                                        <span class="w-6 h-6 rounded-full bg-gray-400 text-white text-xs flex items-center justify-center font-bold">E</span>
                                    @elseif($result === 'L')
                                        <span class="w-6 h-6 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold">D</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
