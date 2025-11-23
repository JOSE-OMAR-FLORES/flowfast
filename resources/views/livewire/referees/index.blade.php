<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">👨‍⚖️ Árbitros</h1>
                    <p class="mt-1 text-sm text-gray-600">Gestiona todos los árbitros disponibles</p>
                </div>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Búsqueda -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        🔍 Buscar Árbitro
                    </label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Nombre, apellido o teléfono..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filtro por tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        🎯 Tipo de Árbitro
                    </label>
                    <select wire:model.live="refereeTypeFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los tipos</option>
                        <option value="main">Principal</option>
                        <option value="assistant">Asistente</option>
                        <option value="scorer">Anotador</option>
                    </select>
                </div>

                <!-- Filtro por deporte -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ⚽ Deporte
                    </label>
                    <select wire:model.live="sportFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los deportes</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por liga -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        🏆 Liga
                    </label>
                    <select wire:model.live="leagueFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las ligas</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="lg:col-span-2 flex items-end">
                    <div class="bg-blue-50 rounded-lg p-3 w-full">
                        <p class="text-sm text-blue-800">
                            <span class="font-semibold">{{ $referees->total() }}</span> árbitros encontrados
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla Desktop -->
        <div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th wire:click="sortBy('first_name')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    Nombre
                                    @if($sortField === 'first_name')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teléfono
                            </th>
                            <th wire:click="sortBy('league')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    Liga
                                    @if($sortField === 'league')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deporte
                            </th>
                            <th wire:click="sortBy('payment_rate')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    Tarifa
                                    @if($sortField === 'payment_rate')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($referees as $referee)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">
                                                {{ strtoupper(substr($referee->first_name, 0, 1)) }}{{ strtoupper(substr($referee->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $referee->full_name }}
                                            </div>
                                            @if($referee->user)
                                                <div class="text-sm text-gray-500">
                                                    {{ $referee->user->email }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($referee->referee_type === 'main') bg-green-100 text-green-800
                                        @elseif($referee->referee_type === 'assistant') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $this->getRefereeTypeLabel($referee->referee_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $referee->phone ?? 'No especificado' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($referee->league)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $referee->league->name }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($referee->league && $referee->league->sport)
                                        <span class="text-sm text-gray-900">
                                            {{ $referee->league->sport->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ${{ number_format($referee->payment_rate, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($referee->user)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ Activo
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Sin usuario
                                        </span>
                                    @endif
                                </td>

                                <!-- Acciones -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button 
                                            wire:click="editReferee({{ $referee->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors"
                                            title="Editar árbitro"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </button>
                                        <button 
                                            wire:click="confirmDelete({{ $referee->id }})" 
                                            wire:confirm="¿Estás seguro de eliminar a {{ $referee->first_name }} {{ $referee->last_name }}? Esta acción no se puede deshacer."
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors"
                                            title="Eliminar árbitro"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">No se encontraron árbitros</p>
                                        <p class="text-sm mt-1">Intenta ajustar los filtros de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($referees->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $referees->links() }}
                </div>
            @endif
        </div>

        <!-- Vista Mobile (Cards) -->
        <div class="lg:hidden space-y-4">
            @forelse($referees as $referee)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <!-- Header del card -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">
                                    {{ strtoupper(substr($referee->first_name, 0, 1)) }}{{ strtoupper(substr($referee->last_name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-base font-semibold text-gray-900">{{ $referee->full_name }}</h3>
                                @if($referee->user)
                                    <p class="text-sm text-gray-500">{{ $referee->user->email }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($referee->referee_type === 'main') bg-green-100 text-green-800
                            @elseif($referee->referee_type === 'assistant') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $this->getRefereeTypeLabel($referee->referee_type) }}
                        </span>
                    </div>

                    <!-- Información -->
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>{{ $referee->phone ?? 'No especificado' }}</span>
                        </div>

                        @if($referee->league)
                            <div class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span>{{ $referee->league->name }}</span>
                            </div>
                        @endif

                        @if($referee->league && $referee->league->sport)
                            <div class="flex items-center text-gray-700">
                                <span class="mr-2">⚽</span>
                                <span>{{ $referee->league->sport->name }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <span class="text-gray-600">Tarifa:</span>
                            <span class="font-semibold text-gray-900">${{ number_format($referee->payment_rate, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Estado:</span>
                            @if($referee->user)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ Activo
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Sin usuario
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button 
                            wire:click="editReferee({{ $referee->id }})" 
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>
                        <button 
                            wire:click="confirmDelete({{ $referee->id }})" 
                            wire:confirm="¿Estás seguro de eliminar a {{ $referee->first_name }} {{ $referee->last_name }}?"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-900">No se encontraron árbitros</p>
                    <p class="text-sm text-gray-500 mt-1">Intenta ajustar los filtros de búsqueda</p>
                </div>
            @endforelse

            <!-- Paginación Mobile -->
            @if($referees->hasPages())
                <div class="mt-6">
                    {{ $referees->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

