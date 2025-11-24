<div>
    {{-- Hero Section - Diseño moderno con gradiente --}}
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white overflow-hidden">
        {{-- Decorative elements --}}
        <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:20px_20px]"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 rounded-full text-sm font-medium mb-8 backdrop-blur-sm">
                    <span>⚡</span>
                    <span>Gestión Deportiva Profesional</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Organiza tu Liga Deportiva
                    <span class="block bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mt-2">
                        de forma Simple y Eficiente
                    </span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-300 mb-10 max-w-3xl mx-auto">
                    Gestiona equipos, programa partidos, actualiza resultados y mantén a tus aficionados informados en una sola plataforma.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('public.leagues') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-semibold hover:from-cyan-600 hover:to-blue-600 transition-all shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40">
                        🏆 Ver Ligas Activas
                    </a>
                    @guest
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-lg font-semibold hover:bg-white/20 transition-all border border-white/20">
                        ✨ Crear Cuenta Gratis
                    </a>
                    @endguest
                </div>
                
                {{-- Stats --}}
                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mb-2">24/7</div>
                        <div class="text-sm text-slate-400">Disponible</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mb-2">100%</div>
                        <div class="text-sm text-slate-400">Gratis</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mb-2">∞</div>
                        <div class="text-sm text-slate-400">Equipos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mb-2">⚡</div>
                        <div class="text-sm text-slate-400">Rápido</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-20 md:py-24 bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Todo lo que Necesitas
                </h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    Herramientas completas para gestionar tu liga deportiva de principio a fin
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Feature 1 --}}
                <div class="group bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6 hover:border-cyan-500/50 hover:shadow-lg hover:shadow-cyan-500/10 transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        📅
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">
                        Calendario Automático
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Genera fixtures completos con sistema round-robin. Programa todos tus partidos en segundos y evita conflictos de horarios.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="group bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6 hover:border-cyan-500/50 hover:shadow-lg hover:shadow-cyan-500/10 transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        📊
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">
                        Tabla de Posiciones
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Actualización automática al finalizar cada partido. Estadísticas completas con goles, puntos y diferencia de gol.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="group bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6 hover:border-cyan-500/50 hover:shadow-lg hover:shadow-cyan-500/10 transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        💰
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">
                        Control Financiero
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Gestiona ingresos y gastos de tu liga. Controla cuotas de equipos, pagos a árbitros y genera reportes financieros.
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div class="group bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6 hover:border-cyan-500/50 hover:shadow-lg hover:shadow-cyan-500/10 transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        👥
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">
                        Gestión de Equipos
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Administra jugadores, entrenadores y staff técnico. Asigna roles y permisos personalizables para cada usuario.
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div class="group bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6 hover:border-cyan-500/50 hover:shadow-lg hover:shadow-cyan-500/10 transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        🎖️
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">
                        Árbitros y Partidos
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Asigna árbitros, actualiza marcadores en tiempo real y gestiona el ciclo de vida completo de cada partido.
                    </p>
                </div>

                {{-- Feature 6 --}}
                <div class="group bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6 hover:border-cyan-500/50 hover:shadow-lg hover:shadow-cyan-500/10 transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        🌐
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">
                        Páginas Públicas
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Comparte información con aficionados. Cada liga tiene su página pública personalizada con URL única.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Leagues Section --}}
    @if($leagues->isNotEmpty())
    <section class="py-20 md:py-24 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-2">
                        Ligas Activas
                    </h2>
                    <p class="text-lg text-slate-400">
                        Descubre las competiciones que están en marcha
                    </p>
                </div>
                <a href="{{ route('public.leagues') }}" 
                   class="hidden md:inline-flex items-center gap-2 px-5 py-2.5 text-cyan-400 hover:text-cyan-300 font-medium transition-colors group">
                    Ver todas
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($leagues as $league)
                    <a href="{{ route('public.league', $league->slug) }}" 
                       class="group block bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 hover:border-cyan-500/50 hover:shadow-lg hover:shadow-cyan-500/10 transition-all overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center text-3xl flex-shrink-0 group-hover:scale-110 transition-transform">
                                    {{ $league->sport->emoji ?? '⚽' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-xl text-white mb-1 truncate group-hover:text-cyan-400 transition-colors">
                                        {{ $league->name }}
                                    </h3>
                                    <p class="text-sm text-slate-400">
                                        {{ $league->sport->name }}
                                    </p>
                                </div>
                            </div>

                            @if($league->description)
                                <p class="text-slate-400 text-sm mb-4 line-clamp-2 leading-relaxed">
                                    {{ $league->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-slate-700/50">
                                @if($league->seasons->isNotEmpty())
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                        </span>
                                        <span class="text-green-400 font-medium">Temporada Activa</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-500">Próximamente</span>
                                @endif
                                
                                <span class="text-sm text-cyan-400 font-medium group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                    Ver detalles
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-12 md:hidden">
                <a href="{{ route('public.leagues') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-slate-800 text-cyan-400 rounded-lg hover:bg-slate-700 transition-colors font-medium border border-slate-700">
                    Ver Todas las Ligas
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <section class="relative py-20 md:py-24 bg-slate-900 overflow-hidden">
        {{-- Decorative gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-transparent to-blue-500/10"></div>
        
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 shadow-2xl overflow-hidden">
                <div class="p-8 md:p-16 text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 rounded-full text-sm font-medium mb-6">
                        <span>🚀</span>
                        <span>Comienza Gratis Hoy</span>
                    </div>
                    
                    <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">
                        ¿Listo para Organizar tu Liga?
                    </h2>
                    
                    <p class="text-lg text-slate-300 mb-10 max-w-2xl mx-auto">
                        Únete a los organizadores que ya confían en FlowFast para gestionar sus competiciones deportivas de forma profesional.
                    </p>
                    
                    @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-semibold hover:from-cyan-600 hover:to-blue-600 transition-all shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40">
                            Crear Cuenta Gratis
                        </a>
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-lg font-semibold hover:bg-white/20 transition-all border border-white/20">
                            Iniciar Sesión
                        </a>
                    </div>
                    
                    <p class="text-sm text-slate-400 mt-8 flex items-center justify-center gap-6 flex-wrap">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sin tarjeta de crédito
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Setup en 5 minutos
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            100% Gratis
                        </span>
                    </p>
                    @else
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-semibold hover:from-cyan-600 hover:to-blue-600 transition-all shadow-lg shadow-cyan-500/25">
                        Ir al Dashboard
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    {{-- Trust Section --}}
    <section class="py-16 bg-slate-950 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-slate-500 text-sm mb-8 uppercase tracking-wider font-medium">
                Compatible con Múltiples Deportes
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 items-center justify-items-center">
                <div class="text-center group cursor-default">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">⚽</div>
                    <p class="text-sm font-medium text-slate-400 group-hover:text-cyan-400 transition-colors">Fútbol</p>
                </div>
                <div class="text-center group cursor-default">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🏀</div>
                    <p class="text-sm font-medium text-slate-400 group-hover:text-cyan-400 transition-colors">Baloncesto</p>
                </div>
                <div class="text-center group cursor-default">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🏐</div>
                    <p class="text-sm font-medium text-slate-400 group-hover:text-cyan-400 transition-colors">Voleibol</p>
                </div>
                <div class="text-center group cursor-default">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🎾</div>
                    <p class="text-sm font-medium text-slate-400 group-hover:text-cyan-400 transition-colors">Y más...</p>
                </div>
            </div>
        </div>
    </section>
</div>