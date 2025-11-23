<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header responsive -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Temporadas</h1>
                    <p class="mt-1 text-sm text-gray-600">Gestiona las temporadas de cada liga</p>
                </div>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'league_manager')
                    <a href="{{ route('seasons.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors w-full sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nueva Temporada
                    </a>
                @endif
            </div>
        </div>

        <!-- Mensajes flash -->
        @if (session()->has('success'))
            <div class="mb-4 sm:mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-4 sm:mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filtros responsive -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Buscar por nombre..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filtro liga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Liga</label>
                    <select wire:model.live="leagueFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="statusFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="draft">Borrador</option>
                        <option value="upcoming">Próxima</option>
                        <option value="active">Activa</option>
                        <option value="completed">Completada</option>
                    </select>
                </div>

                <!-- Filtro formato -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formato</label>
                    <select wire:model.live="formatFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="round_robin">Round Robin</option>
                        <option value="playoff">Playoff</option>
                        <option value="league">Liga</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla responsive -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Vista Desktop -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    Temporada
                                    @if($sortField === 'name')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formato</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($seasons as $season)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $season->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ $season->league->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $season->format)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $season->teams->count() }} equipos
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $season->start_date?->format('d/m/Y') ?? 'Sin fecha' }}</div>
                                    <div class="text-xs text-gray-500">{{ $season->end_date?->format('d/m/Y') ?? 'Sin fecha' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $season->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $season->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $season->status === 'upcoming' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $season->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst($season->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('seasons.edit', $season) }}" class="text-blue-600 hover:text-blue-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if(auth()->user()->role === 'admin')
                                            <button wire:click="delete({{ $season->id }})" 
                                                    wire:confirm="¿Estás seguro de eliminar esta temporada?"
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2">No se encontraron temporadas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Vista Mobile/Tablet -->
            <div class="lg:hidden">
                @forelse($seasons as $season)
                    <div class="p-4 border-b border-gray-200 hover:bg-gray-50">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-900">{{ $season->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $season->league->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $season->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $season->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $season->status === 'upcoming' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $season->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($season->status) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <span class="text-xs text-gray-500">Formato:</span>
                                <span class="block text-sm font-medium text-gray-900 mt-1">
                                    {{ ucfirst(str_replace('_', ' ', $season->format)) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Equipos:</span>
                                <span class="block text-sm font-medium text-gray-900 mt-1">
                                    {{ $season->teams->count() }}
                                </span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-gray-500">Fechas:</span>
                                <span class="block text-sm font-medium text-gray-900 mt-1">
                                    {{ $season->start_date?->format('d/m/Y') ?? 'Sin fecha' }} - {{ $season->end_date?->format('d/m/Y') ?? 'Sin fecha' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
                            <a href="{{ route('seasons.edit', $season) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            @if(auth()->user()->role === 'admin')
                                <button wire:click="delete({{ $season->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta temporada?"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2">No se encontraron temporadas</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación responsive -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $seasons->links() }}
            </div>
        </div>
    </div>
</div>
