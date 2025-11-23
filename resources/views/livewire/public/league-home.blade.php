<div>
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-5xl">
                    {{ $league->sport->emoji ?? '⚽' }}
                </div>
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold">{{ $league->name }}</h1>
                    <p class="text-xl text-blue-100 mt-2">{{ $league->sport->name }}</p>
                </div>
            </div>

            @if($league->description)
                <p class="text-lg text-blue-100 max-w-3xl">
                    {{ $league->description }}
                </p>
            @endif

            @if($activeSeason)
                <div class="mt-6 inline-flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    <span class="font-semibold">{{ $activeSeason->name }} - Temporada Activa</span>
                </div>
            @endif
        </div>
    </section>

    {{-- Navigation --}}
    <section class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-6 overflow-x-auto">
                <a href="{{ route('public.league', $league->slug) }}" 
                   class="py-4 px-2 border-b-2 border-blue-600 text-blue-600 font-medium whitespace-nowrap">
                    🏠 Inicio
                </a>
                <a href="{{ route('public.league.fixtures', $league->slug) }}" 
                   class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium whitespace-nowrap">
                    📅 Calendario
                </a>
                <a href="{{ route('public.league.standings', $league->slug) }}" 
                   class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium whitespace-nowrap">
                    📊 Posiciones
                </a>
                <a href="{{ route('public.league.teams', $league->slug) }}" 
                   class="py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium whitespace-nowrap">
                    👥 Equipos
                </a>
            </nav>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($activeSeason)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Quick Links --}}
                    <a href="{{ route('public.league.fixtures', $league->slug) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <div class="text-4xl mb-3">📅</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Ver Calendario</h3>
                        <p class="text-gray-600">Consulta todos los partidos programados</p>
                    </a>

                    <a href="{{ route('public.league.standings', $league->slug) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <div class="text-4xl mb-3">📊</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Tabla de Posiciones</h3>
                        <p class="text-gray-600">Mira cómo van los equipos</p>
                    </a>

                    <a href="{{ route('public.league.teams', $league->slug) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <div class="text-4xl mb-3">👥</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Ver Equipos</h3>
                        <p class="text-gray-600">Conoce todos los equipos participantes</p>
                    </a>
                </div>

                {{-- Welcome Message --}}
                <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        ¡Bienvenido a {{ $league->name }}!
                    </h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Sigue toda la acción de la temporada {{ $activeSeason->name }}. 
                        Consulta calendarios, resultados y la tabla de posiciones actualizada.
                    </p>
                </div>
            @else
                {{-- No Active Season --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <div class="text-6xl mb-4">⏳</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Próximamente</h3>
                    <p class="text-gray-600">
                        Esta liga aún no tiene una temporada activa. ¡Vuelve pronto para más información!
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>
