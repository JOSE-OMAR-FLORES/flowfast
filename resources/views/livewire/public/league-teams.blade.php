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
                <a href="{{ url('/league/' . $league->slug . '/standings') }}" class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                    Posiciones
                </a>
                <a href="{{ url('/league/' . $league->slug . '/teams') }}" class="py-4 px-2 border-b-2 border-blue-500 text-blue-600 font-semibold whitespace-nowrap">
                    Equipos
                </a>
            </nav>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="container mx-auto px-4 py-8">
        @if($teams->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg">No hay equipos registrados aún</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($teams as $team)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                        {{-- Logo del Club (placeholder) --}}
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($team->club->name ?? $team->name, 0, 2) }}
                        </div>

                        {{-- Nombre del Equipo --}}
                        <h3 class="text-center font-bold text-lg text-gray-900 mb-1">
                            {{ $team->name }}
                        </h3>

                        {{-- Nombre del Club --}}
                        @if($team->club)
                            <p class="text-center text-sm text-gray-600 mb-3">
                                {{ $team->club->name }}
                            </p>
                        @endif

                        {{-- Información Adicional --}}
                        <div class="border-t pt-3 mt-3 space-y-2 text-sm">
                            @if($team->club && $team->club->city)
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>{{ $team->club->city }}</span>
                                </div>
                            @endif

                            @if($team->club && $team->club->email)
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="truncate">{{ $team->club->email }}</span>
                                </div>
                            @endif

                            @if($team->club && $team->club->phone)
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>{{ $team->club->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
