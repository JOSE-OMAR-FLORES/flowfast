<div class="min-h-screen bg-slate-950">
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
                   class="py-4 px-6 bg-slate-800 text-cyan-400 border-b-2 border-cyan-400 font-semibold rounded-t-lg whitespace-nowrap">
                    📅 Calendario
                </a>
                <a href="{{ url('/league/' . $league->slug . '/standings') }}" 
                   class="py-4 px-6 text-slate-300 hover:text-white hover:bg-slate-800 rounded-t-lg transition-all whitespace-nowrap">
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
        @if($fixtures->isEmpty())
            {{-- Estado Vacío --}}
            <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 p-16 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No hay partidos programados</h3>
                <p class="text-slate-400">Los partidos aparecerán aquí cuando se publique el calendario</p>
            </div>
        @else
            {{-- Lista de Partidos --}}
            <div class="space-y-8">
                @foreach($fixtures as $date => $dateFixtures)
                    <div class="space-y-4">
                        {{-- Encabezado de Fecha --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D [de] MMMM') }}
                                </h3>
                                <p class="text-sm text-slate-400">
                                    {{ \Carbon\Carbon::parse($date)->isoFormat('YYYY') }}
                                </p>
                            </div>
                        </div>

                        {{-- Partidos del día --}}
                        <div class="space-y-3">
                            @foreach($dateFixtures as $fixture)
                                <div class="group bg-slate-900/50 backdrop-blur-sm rounded-xl border border-slate-800 hover:border-cyan-500/50 transition-all overflow-hidden hover:shadow-lg hover:shadow-cyan-500/10">
                                    <div class="p-5">
                                        <div class="flex items-center justify-between gap-6 flex-wrap">
                                            {{-- Hora --}}
                                            <div class="flex-shrink-0">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-cyan-400">
                                                        {{ \Carbon\Carbon::parse($fixture->date)->format('H:i') }}
                                                    </div>
                                                    <div class="text-xs text-slate-500 uppercase tracking-wider">
                                                        Hora
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Equipos y Resultado --}}
                                            <div class="flex-1 min-w-[320px]">
                                                {{-- Equipo Local --}}
                                                <div class="flex items-center justify-between mb-3">
                                                    <div class="flex items-center gap-3 flex-1">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg flex items-center justify-center text-sm font-bold text-slate-400 border border-slate-700">
                                                            {{ strtoupper(substr($fixture->homeTeam->name, 0, 3)) }}
                                                        </div>
                                                        <span class="font-semibold text-white group-hover:text-cyan-400 transition-colors">
                                                            {{ $fixture->homeTeam->name }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if($fixture->status === 'completed')
                                                        <div class="text-2xl font-bold text-white px-4">
                                                            {{ $fixture->home_score ?? 0 }}
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Equipo Visitante --}}
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3 flex-1">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg flex items-center justify-center text-sm font-bold text-slate-400 border border-slate-700">
                                                            {{ strtoupper(substr($fixture->awayTeam->name, 0, 3)) }}
                                                        </div>
                                                        <span class="font-semibold text-white group-hover:text-cyan-400 transition-colors">
                                                            {{ $fixture->awayTeam->name }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if($fixture->status === 'completed')
                                                        <div class="text-2xl font-bold text-white px-4">
                                                            {{ $fixture->away_score ?? 0 }}
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Estado --}}
                                                @if($fixture->status === 'in_progress')
                                                    <div class="mt-3 flex items-center gap-2">
                                                        <span class="relative flex h-3 w-3">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                                        </span>
                                                        <span class="text-green-400 text-sm font-medium">En vivo</span>
                                                    </div>
                                                @elseif($fixture->status === 'completed')
                                                    <div class="mt-3">
                                                        <span class="text-slate-500 text-sm">Finalizado</span>
                                                    </div>
                                                @else
                                                    <div class="mt-3">
                                                        <span class="text-slate-500 text-sm">Programado</span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Sede y Detalles --}}
                                            <div class="flex-shrink-0 text-right">
                                                @if($fixture->venue)
                                                    <div class="flex items-center gap-2 justify-end mb-2">
                                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        <span class="text-sm text-slate-400">{{ $fixture->venue->name }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if($fixture->referee)
                                                    <div class="flex items-center gap-2 justify-end">
                                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        <span class="text-sm text-slate-400">{{ $fixture->referee->name }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(!$fixture->venue && !$fixture->referee)
                                                    <span class="text-sm text-slate-600">Detalles pendientes</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Leyenda --}}
            <div class="mt-12 bg-slate-900/50 backdrop-blur-sm rounded-xl border border-slate-800 p-6">
                <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Leyenda</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-sm text-slate-300">Partido en vivo</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-cyan-500 rounded-full"></div>
                        <span class="text-sm text-slate-300">Próximo partido</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-slate-600 rounded-full"></div>
                        <span class="text-sm text-slate-300">Finalizado</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

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