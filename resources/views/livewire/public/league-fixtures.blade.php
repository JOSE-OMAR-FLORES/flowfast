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
                <a href="{{ url('/league/' . $league->slug . '/fixtures') }}" class="py-4 px-2 border-b-2 border-blue-500 text-blue-600 font-semibold whitespace-nowrap">
                    Calendario
                </a>
                <a href="{{ url('/league/' . $league->slug . '/standings') }}" class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
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
        @if($fixtures->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg">No hay partidos programados aún</p>
            </div>
        @else
            @foreach($fixtures as $date => $dateFixtures)
                <div class="mb-8">
                    {{-- Encabezado de Fecha --}}
                    <div class="bg-gray-100 px-4 py-2 rounded-lg mb-3">
                        <h3 class="font-semibold text-gray-700">
                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </h3>
                    </div>

                    {{-- Partidos del día --}}
                    <div class="space-y-3">
                        @foreach($dateFixtures as $fixture)
                            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-4">
                                <div class="flex items-center justify-between flex-wrap gap-4">
                                    {{-- Hora --}}
                                    <div class="text-sm text-gray-500 w-16">
                                        {{ \Carbon\Carbon::parse($fixture->date)->format('H:i') }}
                                    </div>

                                    {{-- Equipos --}}
                                    <div class="flex-1 min-w-[300px]">
                                        <div class="flex items-center justify-between">
                                            {{-- Equipo Local --}}
                                            <div class="flex items-center gap-2 flex-1">
                                                <span class="font-semibold text-right flex-1">{{ $fixture->homeTeam->name }}</span>
                                            </div>

                                            {{-- Resultado/Estado --}}
                                            <div class="px-4">
                                                @if($fixture->status === 'completed')
                                                    <div class="text-center">
                                                        <div class="font-bold text-lg">
                                                            {{ $fixture->home_score ?? 0 }} - {{ $fixture->away_score ?? 0 }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">Final</div>
                                                    </div>
                                                @elseif($fixture->status === 'in_progress')
                                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                        En vivo
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 font-semibold">vs</span>
                                                @endif
                                            </div>

                                            {{-- Equipo Visitante --}}
                                            <div class="flex items-center gap-2 flex-1">
                                                <span class="font-semibold flex-1">{{ $fixture->awayTeam->name }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sede --}}
                                    <div class="text-sm text-gray-500 text-right w-48">
                                        @if($fixture->venue)
                                            <div class="flex items-center gap-1 justify-end">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>{{ $fixture->venue->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">Por definir</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
