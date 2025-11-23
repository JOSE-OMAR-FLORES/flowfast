<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">👨‍🏫 Entrenadores</h1>
                    <p class="mt-1 text-sm text-gray-600">Gestiona los entrenadores y sus equipos</p>
                </div>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Búsqueda -->
                <div class="sm:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        🔍 Buscar
                    </label>
                    <input 
                        type="text" 
                        id="search"
                        wire:model.live="search" 
                        placeholder="Nombre, teléfono, licencia..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                </div>

                <!-- Filtro por deporte -->
                <div>
                    <label for="sport" class="block text-sm font-medium text-gray-700 mb-1">
                        ⚽ Deporte
                    </label>
                    <select 
                        id="sport"
                        wire:model.live="filterSport"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option value="">Todos</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por liga -->
                <div>
                    <label for="league" class="block text-sm font-medium text-gray-700 mb-1">
                        🏆 Liga
                    </label>
                    <select 
                        id="league"
                        wire:model.live="filterLeague"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option value="">Todas</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por licencia -->
                <div>
                    <label for="license" class="block text-sm font-medium text-gray-700 mb-1">
                        📜 Licencia
                    </label>
                    <select 
                        id="license"
                        wire:model.live="filterLicense"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option value="">Todos</option>
                        <option value="1">Con licencia</option>
                        <option value="0">Sin licencia</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de entrenadores (Desktop) -->
        <div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" wire:click="sortBy('first_name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Entrenador
                                    @if($sortField === 'first_name')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            @else
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contacto
                            </th>
                            <th scope="col" wire:click="sortBy('team')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Equipo
                                    @if($sortField === 'team')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            @else
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Liga / Deporte
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Licencia
                            </th>
                            <th scope="col" wire:click="sortBy('experience_years')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Experiencia
                                    @if($sortField === 'experience_years')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            @else
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($coaches as $coach)
                            <tr class="hover:bg-gray-50">
                                <!-- Entrenador -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($coach->first_name, 0, 1) . substr($coach->last_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $coach->full_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contacto -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">📞 {{ $coach->phone }}</div>
                                    @if($coach->user)
                                        <div class="text-sm text-gray-500">📧 {{ $coach->user->email }}</div>
                                    @endif
                                </td>

                                <!-- Equipo -->
                                <td class="px-6 py-4">
                                    @if($coach->team)
                                        <div class="text-sm font-medium text-gray-900">{{ $coach->team->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $coach->team->season->name ?? 'Sin temporada' }}</div>
                                    @else
                                        <span class="text-sm text-gray-500">Sin equipo asignado</span>
                                    @endif
                                </td>

                                <!-- Liga / Deporte -->
                                <td class="px-6 py-4">
                                    @if($coach->team && $coach->team->season && $coach->team->season->league)
                                        <div class="text-sm text-gray-900">🏆 {{ $coach->team->season->league->name }}</div>
                                        <div class="text-sm text-gray-500">⚽ {{ $coach->team->season->league->sport->name ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>

                                <!-- Licencia -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($coach->license_number)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ {{ $coach->license_number }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⚠ Sin licencia
                                        </span>
                                    @endif
                                </td>

                                <!-- Experiencia -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($coach->experience_years)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $coach->experience_years }} {{ $coach->experience_years == 1 ? 'año' : 'años' }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>

                                <!-- Estado -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($coach->user)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 mr-1.5 bg-green-400 rounded-full"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <span class="w-1.5 h-1.5 mr-1.5 bg-gray-400 rounded-full"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <!-- Acciones -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button 
                                            wire:click="editCoach({{ $coach->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors"
                                            title="Editar entrenador"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </button>
                                        <button 
                                            wire:click="confirmDelete({{ $coach->id }})" 
                                            wire:confirm="¿Estás seguro de eliminar a {{ $coach->full_name }}? Esta acción no se puede deshacer."
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors"
                                            title="Eliminar entrenador"
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
                                    <div class="text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="mt-2 text-sm">No se encontraron entrenadores</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($coaches->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $coaches->links() }}
                </div>
            @endif
        </div>

        <!-- Cards de entrenadores (Mobile & Tablet) -->
        <div class="lg:hidden space-y-4">
            @forelse($coaches as $coach)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <!-- Header con nombre y avatar -->
                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0 h-12 w-12 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($coach->first_name, 0, 1) . substr($coach->last_name, 0, 1)) }}
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-base font-semibold text-gray-900">{{ $coach->full_name }}</h3>
                            <div class="mt-1">
                                @if($coach->user)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 mr-1 bg-green-400 rounded-full"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-1.5 h-1.5 mr-1 bg-gray-400 rounded-full"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información de contacto -->
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="text-sm text-gray-600">📞 {{ $coach->phone }}</div>
                        @if($coach->user)
                            <div class="text-sm text-gray-600">📧 {{ $coach->user->email }}</div>
                        @endif
                    </div>

                    <!-- Equipo -->
                    <div class="mb-3">
                        <div class="text-xs font-medium text-gray-500 uppercase mb-1">🏀 Equipo</div>
                        @if($coach->team)
                            <div class="text-sm font-medium text-gray-900">{{ $coach->team->name }}</div>
                            <div class="text-sm text-gray-500">{{ $coach->team->season->name ?? 'Sin temporada' }}</div>
                        @else
                            <div class="text-sm text-gray-500">Sin equipo asignado</div>
                        @endif
                    </div>

                    <!-- Liga y Deporte -->
                    @if($coach->team && $coach->team->season && $coach->team->season->league)
                        <div class="mb-3">
                            <div class="text-xs font-medium text-gray-500 uppercase mb-1">🏆 Liga / Deporte</div>
                            <div class="text-sm text-gray-900">{{ $coach->team->season->league->name }}</div>
                            <div class="text-sm text-gray-500">⚽ {{ $coach->team->season->league->sport->name ?? 'N/A' }}</div>
                        </div>
                    @endif

                    <!-- Licencia y Experiencia -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($coach->license_number)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ {{ $coach->license_number }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                ⚠ Sin licencia
                            </span>
                        @endif

                        @if($coach->experience_years)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ⭐ {{ $coach->experience_years }} {{ $coach->experience_years == 1 ? 'año' : 'años' }}
                            </span>
                        @endif
                    </div>

                    <!-- Acciones -->
                    <div class="flex gap-2 pt-3 border-t border-gray-200">
                        <button 
                            wire:click="editCoach({{ $coach->id }})" 
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>
                        <button 
                            wire:click="confirmDelete({{ $coach->id }})" 
                            wire:confirm="¿Estás seguro de eliminar a {{ $coach->full_name }}?"
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
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No se encontraron entrenadores</p>
                </div>
            @endforelse

            @if($coaches->hasPages())
                <div class="mt-4">
                    {{ $coaches->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
