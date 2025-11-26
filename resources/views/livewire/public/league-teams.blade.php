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
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
                    📊 Posiciones
                </a>
                <a href="{{ url('/league/' . $league->slug . '/teams') }}" 
                   class="py-4 px-6 bg-slate-800 text-cyan-400 border-b-2 border-cyan-400 font-semibold rounded-t-lg whitespace-nowrap">
                    👥 Equipos
                </a>
            </nav>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="container mx-auto px-4 py-8">
        @if($teams->isEmpty())
            {{-- Estado Vacío --}}
            <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 p-16 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No hay equipos registrados</h3>
                <p class="text-slate-400">Los equipos aparecerán aquí cuando se registren en la liga</p>
            </div>
        @else
            {{-- Header con contador --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">Equipos Participantes</h2>
                    <p class="text-slate-400">{{ $teams->count() }} {{ $teams->count() === 1 ? 'equipo inscrito' : 'equipos inscritos' }}</p>
                </div>
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 border border-cyan-500/30 rounded-lg">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-cyan-400 font-medium">{{ $teams->count() }}</span>
                </div>
            </div>

            {{-- Grid de Equipos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($teams as $team)
                    <div class="group bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 hover:border-cyan-500/50 transition-all overflow-hidden hover:shadow-2xl hover:shadow-cyan-500/10">
                        <div class="p-6">
                            {{-- Logo del Club --}}
                            <div class="relative mb-5">
                                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center border border-cyan-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shadow-xl shadow-cyan-500/10">
                                    <span class="text-4xl font-bold text-cyan-400">
                                        {{ strtoupper(substr($team->club->name ?? $team->name, 0, 2)) }}
                                    </span>
                                </div>
                                {{-- Badge decorativo --}}
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>

                            {{-- Nombre del Equipo --}}
                            <h3 class="text-center font-bold text-xl text-white mb-2 group-hover:text-cyan-400 transition-colors">
                                {{ $team->name }}
                            </h3>

                            {{-- Nombre del Club --}}
                            @if($team->club)
                                <p class="text-center text-sm text-slate-400 mb-4">
                                    {{ $team->club->name }}
                                </p>
                            @endif

                            {{-- Información Adicional --}}
                            <div class="border-t border-slate-800 pt-4 space-y-3">
                                @if($team->club && $team->club->city)
                                    <div class="flex items-center gap-3 text-slate-300 group/item hover:text-cyan-400 transition-colors">
                                        <div class="flex-shrink-0 w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center group-hover/item:bg-cyan-500/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm">{{ $team->club->city }}</span>
                                    </div>
                                @endif

                                @if($team->club && $team->club->email)
                                    <div class="flex items-center gap-3 text-slate-300 group/item hover:text-cyan-400 transition-colors">
                                        <div class="flex-shrink-0 w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center group-hover/item:bg-cyan-500/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm truncate">{{ $team->club->email }}</span>
                                    </div>
                                @endif

                                @if($team->club && $team->club->phone)
                                    <div class="flex items-center gap-3 text-slate-300 group/item hover:text-cyan-400 transition-colors">
                                        <div class="flex-shrink-0 w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center group-hover/item:bg-cyan-500/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm">{{ $team->club->phone }}</span>
                                    </div>
                                @endif

                                {{-- Si no hay información adicional --}}
                                @if(!($team->club && ($team->club->city || $team->club->email || $team->club->phone)))
                                    <div class="text-center py-2">
                                        <span class="text-xs text-slate-600">Información no disponible</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer del Card --}}
                        <div class="bg-slate-800/50 px-6 py-3 border-t border-slate-800">
                            <div class="flex items-center justify-center gap-2 text-slate-500 group-hover:text-cyan-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs font-medium">Ver detalles</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Info Banner --}}
            <div class="mt-12 bg-gradient-to-r from-blue-900/30 to-cyan-900/30 backdrop-blur-sm rounded-xl border border-blue-500/30 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-white mb-1">Información de Contacto</h4>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            La información de contacto mostrada corresponde a cada club. Para consultas específicas sobre un equipo, utiliza los datos de contacto proporcionados.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>