<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🏆 Todas las Ligas</h1>
            <p class="text-lg text-gray-600">Descubre y sigue tus ligas deportivas favoritas</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        🔍 Buscar Liga
                    </label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Nombre de la liga..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Sport Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ⚽ Filtrar por Deporte
                    </label>
                    <select wire:model.live="sportFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los deportes</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}">
                                {{ $sport->emoji }} {{ $sport->name }} ({{ $sport->leagues_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Leagues Grid --}}
        @if($leagues->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($leagues as $league)
                    <a href="{{ route('public.league', $league->slug) }}" class="block group">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 h-full hover:shadow-lg hover:border-blue-300 transition-all">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                                    {{ $league->sport->emoji ?? '⚽' }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-xl text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $league->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $league->sport->name }}</p>
                                </div>
                            </div>

                            @if($league->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                    {{ $league->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                @if($league->seasons->isNotEmpty())
                                    <div class="flex items-center gap-2 text-sm text-green-600">
                                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                        <span class="font-medium">Temporada Activa</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Próximamente</span>
                                @endif
                                
                                <span class="text-blue-600 group-hover:text-blue-700 font-medium text-sm">
                                    Ver más →
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No se encontraron ligas</h3>
                <p class="text-gray-600 mb-6">
                    @if($search || $sportFilter)
                        Intenta ajustar los filtros de búsqueda.
                    @else
                        No hay ligas públicas disponibles en este momento.
                    @endif
                </p>
                @if($search || $sportFilter)
                    <button wire:click="$set('search', ''); $set('sportFilter', '')" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Limpiar Filtros
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
