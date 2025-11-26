<div class="min-h-screen bg-slate-950">
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white py-16 overflow-hidden">
        {{-- Decorative elements --}}
        <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:20px_20px]"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-8">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 backdrop-blur-sm border border-cyan-500/30 flex items-center justify-center text-6xl shadow-2xl shadow-cyan-500/20">
                    {{ $league->sport->emoji ?? '⚽' }}
                </div>
                <div class="flex-1">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-3 bg-gradient-to-r from-white to-cyan-100 bg-clip-text text-transparent">
                        {{ $league->name }}
                    </h1>
                    <p class="text-xl text-cyan-300 flex items-center gap-2">
                        <span class="px-3 py-1 bg-cyan-500/20 border border-cyan-500/30 rounded-lg text-sm font-medium">
                            {{ $league->sport->name }}
                        </span>
                    </p>
                </div>
            </div>

            @if($league->description)
                <p class="text-lg text-slate-300 max-w-3xl mb-6 leading-relaxed">
                    {{ $league->description }}
                </p>
            @endif

            @if($activeSeason)
                <div class="inline-flex items-center gap-3 bg-gradient-to-r from-green-500/20 to-emerald-500/20 backdrop-blur-sm border border-green-500/30 text-white px-5 py-3 rounded-xl shadow-lg">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="font-semibold text-green-100">{{ $activeSeason->name }} - Temporada Activa</span>
                </div>
            @endif
        </div> 
    </section>

    {{-- Navigation --}}
    <section class="bg-slate-900/95 backdrop-blur-sm border-b border-slate-800 sticky top-0 z-10 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-1 overflow-x-auto scrollbar-hide">
                <a href="{{ route('public.league', $league->slug) }}" 
                   class="py-4 px-6 bg-slate-800 text-cyan-400 border-b-2 border-cyan-400 font-semibold rounded-t-lg whitespace-nowrap">
                    🏠 Inicio
                </a>
                <a href="{{ route('public.league.fixtures', $league->slug) }}" 
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
                    📅 Calendario
                </a>
                <a href="{{ route('public.league.standings', $league->slug) }}" 
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
                    📊 Posiciones
                </a>
                <a href="{{ route('public.league.teams', $league->slug) }}" 
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
                    👥 Equipos
                </a>
            </nav>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($activeSeason)
                {{-- Quick Links Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    {{-- Calendario --}}
                    <a href="{{ route('public.league.fixtures', $league->slug) }}" 
                       class="group bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 hover:border-cyan-500/50 p-8 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-4xl mb-5 group-hover:scale-110 transition-transform">
                            📅
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-cyan-400 transition-colors">
                            Ver Calendario
                        </h3>
                        <p class="text-slate-400 leading-relaxed">
                            Consulta todos los partidos programados y mantente al día con los horarios
                        </p>
                        <div class="mt-4 flex items-center gap-2 text-cyan-400 font-medium group-hover:gap-3 transition-all">
                            <span>Explorar</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>

                    {{-- Posiciones --}}
                    <a href="{{ route('public.league.standings', $league->slug) }}" 
                       class="group bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 hover:border-cyan-500/50 p-8 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-4xl mb-5 group-hover:scale-110 transition-transform">
                            📊
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-cyan-400 transition-colors">
                            Tabla de Posiciones
                        </h3>
                        <p class="text-slate-400 leading-relaxed">
                            Mira cómo van los equipos y consulta las estadísticas actualizadas
                        </p>
                        <div class="mt-4 flex items-center gap-2 text-cyan-400 font-medium group-hover:gap-3 transition-all">
                            <span>Ver tabla</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>

                    {{-- Equipos --}}
                    <a href="{{ route('public.league.teams', $league->slug) }}" 
                       class="group bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 hover:border-cyan-500/50 p-8 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center text-4xl mb-5 group-hover:scale-110 transition-transform">
                            👥
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-cyan-400 transition-colors">
                            Ver Equipos
                        </h3>
                        <p class="text-slate-400 leading-relaxed">
                            Conoce todos los equipos participantes y sus plantillas completas
                        </p>
                        <div class="mt-4 flex items-center gap-2 text-cyan-400 font-medium group-hover:gap-3 transition-all">
                            <span>Descubrir</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                </div>

                {{-- Welcome Message --}}
                <div class="relative bg-gradient-to-br from-slate-900/80 to-slate-800/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 shadow-2xl overflow-hidden">
                    {{-- Decorative gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 via-transparent to-blue-500/5"></div>
                    
                    <div class="relative p-10 md:p-16 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 rounded-full text-sm font-medium mb-6">
                            <span>🎉</span>
                            <span>Bienvenido</span>
                        </div>
                        
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                            ¡Bienvenido a {{ $league->name }}!
                        </h2>
                        
                        <p class="text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed mb-8">
                            Sigue toda la acción de la temporada {{ $activeSeason->name }}. 
                            Consulta calendarios, resultados y la tabla de posiciones actualizada en tiempo real.
                        </p>

                        {{-- Stats Cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-3xl mx-auto">
                            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6">
                                <div class="text-3xl font-bold text-cyan-400 mb-1">
                                    {{ $activeSeason->name }}
                                </div>
                                <div class="text-sm text-slate-400 uppercase tracking-wider">
                                    Temporada Actual
                                </div>
                            </div>
                            
                            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6">
                                <div class="text-3xl font-bold text-cyan-400 mb-1">
                                    En Vivo
                                </div>
                                <div class="text-sm text-slate-400 uppercase tracking-wider">
                                    Actualizaciones
                                </div>
                            </div>
                            
                            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6">
                                <div class="text-3xl font-bold text-cyan-400 mb-1">
                                    24/7
                                </div>
                                <div class="text-sm text-slate-400 uppercase tracking-wider">
                                    Disponible
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Banner --}}
                <div class="mt-8 bg-gradient-to-r from-blue-900/30 to-cyan-900/30 backdrop-blur-sm rounded-xl border border-blue-500/30 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-white mb-1">¿Nuevo por aquí?</h4>
                            <p class="text-slate-300 text-sm leading-relaxed">
                                Explora las diferentes secciones usando el menú de navegación. Toda la información se actualiza automáticamente después de cada partido.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                {{-- No Active Season --}}
                <div class="relative bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-800/50 via-transparent to-slate-900/50"></div>
                    
                    <div class="relative p-16 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-8 border border-slate-700">
                            <div class="text-6xl">⏳</div>
                        </div>
                        
                        <h3 class="text-3xl md:text-4xl font-bold text-white mb-4">
                            Próximamente
                        </h3>
                        
                        <p class="text-lg text-slate-400 max-w-md mx-auto leading-relaxed mb-8">
                            Esta liga aún no tiene una temporada activa. ¡Vuelve pronto para más información y no te pierdas el inicio de la acción!
                        </p>

                        <div class="inline-flex items-center gap-2 px-6 py-3 bg-slate-800 border border-slate-700 text-slate-300 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="font-medium">Te notificaremos cuando comience</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

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
</div>