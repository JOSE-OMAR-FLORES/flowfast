<div>
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    ⚡ Gestión Deportiva Simplificada
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Organiza ligas, gestiona equipos, programa partidos y mantén a tus aficionados informados en una sola plataforma.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('public.leagues') }}" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-bold text-lg hover:bg-blue-50 transition-colors shadow-lg">
                        🏆 Ver Ligas
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-blue-500 text-white rounded-lg font-bold text-lg hover:bg-blue-400 transition-colors shadow-lg">
                        ✨ Registrarse Gratis
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    ¿Por qué FlowFast?
                </h2>
                <p class="text-xl text-gray-600">
                    Todo lo que necesitas para gestionar tu liga deportiva
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="text-center p-6 rounded-lg hover:shadow-lg transition-shadow">
                    <div class="text-5xl mb-4">📅</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Calendario Automático</h3>
                    <p class="text-gray-600">
                        Genera fixtures automáticamente con round-robin. Programa todos tus partidos en segundos.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="text-center p-6 rounded-lg hover:shadow-lg transition-shadow">
                    <div class="text-5xl mb-4">📊</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tabla de Posiciones</h3>
                    <p class="text-gray-600">
                        Actualización automática al finalizar cada partido. Estadísticas completas en tiempo real.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="text-center p-6 rounded-lg hover:shadow-lg transition-shadow">
                    <div class="text-5xl mb-4">💰</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Gestión Financiera</h3>
                    <p class="text-gray-600">
                        Control de ingresos y gastos. Cuotas, pagos a árbitros y reportes financieros.
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div class="text-center p-6 rounded-lg hover:shadow-lg transition-shadow">
                    <div class="text-5xl mb-4">👥</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Gestión de Equipos</h3>
                    <p class="text-gray-600">
                        Administra jugadores, entrenadores y staff. Roles y permisos personalizables.
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div class="text-center p-6 rounded-lg hover:shadow-lg transition-shadow">
                    <div class="text-5xl mb-4">🎖️</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Árbitros y Partidos</h3>
                    <p class="text-gray-600">
                        Asigna árbitros, actualiza marcadores y gestiona el ciclo de vida completo de cada partido.
                    </p>
                </div>

                {{-- Feature 6 --}}
                <div class="text-center p-6 rounded-lg hover:shadow-lg transition-shadow">
                    <div class="text-5xl mb-4">🌐</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Páginas Públicas</h3>
                    <p class="text-gray-600">
                        Comparte información con aficionados. Cada liga tiene su página pública personalizada.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Leagues Section --}}
    @if($leagues->isNotEmpty())
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    🏆 Ligas Activas
                </h2>
                <p class="text-xl text-gray-600">
                    Descubre las ligas que están en acción
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($leagues as $league)
                    <a href="{{ route('public.league', $league->slug) }}" class="block">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-2xl">
                                    {{ $league->sport->emoji ?? '⚽' }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg text-gray-900">{{ $league->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $league->sport->name }}</p>
                                </div>
                            </div>

                            @if($league->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ $league->description }}
                                </p>
                            @endif

                            @if($league->seasons->isNotEmpty())
                                <div class="flex items-center gap-2 text-sm text-green-600 bg-green-50 px-3 py-2 rounded">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    <span class="font-medium">Temporada Activa</span>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">
                                    Próximamente
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('public.leagues') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Ver Todas las Ligas →
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <section class="py-16 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                ¿Listo para comenzar?
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Crea tu cuenta gratis y empieza a gestionar tu liga deportiva hoy mismo.
            </p>
            @guest
            <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-bold text-lg hover:bg-blue-50 transition-colors shadow-lg">
                🚀 Crear Cuenta Gratis
            </a>
            @else
            <a href="{{ route('admin.dashboard') }}" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-bold text-lg hover:bg-blue-50 transition-colors shadow-lg">
                📊 Ir al Dashboard
            </a>
            @endguest
        </div>
    </section>
</div>
