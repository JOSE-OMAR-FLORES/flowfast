<div class="min-h-screen bg-slate-950 py-12">
    <style>
    /* Line clamp utility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 rounded-full text-sm font-medium mb-4">
                <span>🏆</span>
                <span>Explorar Ligas</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">
                Todas las Ligas
            </h1>
            <p class="text-lg text-slate-400">
                Descubre y sigue tus ligas deportivas favoritas
            </p>
        </div>

        {{-- Filters --}}
        <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 p-6 mb-8 shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Buscar Liga
                    </label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Nombre de la liga..."
                           class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                </div>

                {{-- Sport Filter --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtrar por Deporte
                    </label>
                    <select wire:model.live="sportFilter" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                        <option value="">Todos los deportes</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}">
                                {{ $sport->emoji }} {{ $sport->name }} ({{ $sport->leagues_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Active Filters Badge --}}
            @if($search || $sportFilter)
                <div class="mt-4 pt-4 border-t border-slate-800">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-sm text-slate-400">Filtros activos:</span>
                        @if($search)
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-cyan-500/20 border border-cyan-500/30 rounded-lg text-cyan-300 text-sm">
                                <span>Búsqueda: "{{ $search }}"</span>
                                <button wire:click="$set('search', '')" class="hover:text-cyan-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($sportFilter)
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-cyan-500/20 border border-cyan-500/30 rounded-lg text-cyan-300 text-sm">
                                <span>Deporte filtrado</span>
                                <button wire:click="$set('sportFilter', '')" class="hover:text-cyan-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Results Counter --}}
        @if($leagues->count() > 0)
            <div class="mb-6 flex items-center justify-between">
                <p class="text-slate-400">
                    Mostrando <span class="text-cyan-400 font-semibold">{{ $leagues->count() }}</span> 
                    {{ $leagues->count() === 1 ? 'liga' : 'ligas' }}
                </p>
                @if($search || $sportFilter)
                    <button wire:click="$set('search', ''); $set('sportFilter', '')" 
                            class="text-sm text-slate-400 hover:text-cyan-400 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Limpiar filtros
                    </button>
                @endif
            </div>
        @endif

        {{-- Leagues Grid --}}
        @if($leagues->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($leagues as $league)
                    <a href="{{ route('public.league', $league->slug) }}" 
                       class="block group bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 hover:border-cyan-500/50 transition-all overflow-hidden hover:shadow-2xl hover:shadow-cyan-500/10">
                        <div class="p-6">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="flex-shrink-0 w-16 h-16 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                    {{ $league->sport->emoji ?? '⚽' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-xl text-white group-hover:text-cyan-400 transition-colors mb-1 line-clamp-2">
                                        {{ $league->name }}
                                    </h3>
                                    <p class="text-sm text-slate-400 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-slate-600 rounded-full"></span>
                                        {{ $league->sport->name }}
                                    </p>
                                </div>
                            </div>

                            @if($league->description)
                                <p class="text-slate-400 text-sm mb-4 line-clamp-3 leading-relaxed">
                                    {{ $league->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                                @if($league->seasons->isNotEmpty())
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="relative flex h-2.5 w-2.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                        </span>
                                        <span class="text-green-400 font-medium">Activa</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-600 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Próximamente
                                    </span>
                                @endif
                                
                                <span class="text-cyan-400 group-hover:text-cyan-300 font-medium text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                                    Ver más
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $leagues->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 p-16 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <div class="text-5xl">🔍</div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">
                    No se encontraron ligas
                </h3>
                <p class="text-slate-400 mb-8 max-w-md mx-auto">
                    @if($search || $sportFilter)
                        No hay ligas que coincidan con tu búsqueda. Intenta ajustar los filtros para ver más resultados.
                    @else
                        No hay ligas públicas disponibles en este momento. Vuelve pronto para descubrir nuevas competiciones.
                    @endif
                </p>
                @if($search || $sportFilter)
                    <button wire:click="$set('search', ''); $set('sportFilter', '')" 
                            class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-semibold hover:from-cyan-600 hover:to-blue-600 transition-all shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Limpiar Filtros
                    </button>
                @endif
            </div>
        @endif

        {{-- Info Banner --}}
        @if($leagues->count() > 0)
            <div class="mt-12 bg-gradient-to-r from-blue-900/30 to-cyan-900/30 backdrop-blur-sm rounded-xl border border-blue-500/30 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-white mb-1">¿Buscas una liga específica?</h4>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Usa el buscador y filtros para encontrar rápidamente la liga que te interesa. Todas las ligas con temporada activa muestran información en tiempo real.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>