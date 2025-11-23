@section('page-title', 'Dashboard de Administrador')

<div>
    <!-- Header Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        <!-- Total Ligas -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 border border-gray-200 hover-lift">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Ligas</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">{{ $totalLeagues }}</p>
                    <p class="text-xs sm:text-sm text-green-600">{{ $activeLeagues }} activas</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Equipos -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 border border-gray-200 hover-lift">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Equipos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">{{ $totalTeams }}</p>
                    <p class="text-xs sm:text-sm text-gray-500">En {{ $totalSeasons }} temporadas</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Partidos -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 border border-gray-200 hover-lift">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Partidos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">{{ $totalMatches }}</p>
                    <p class="text-xs sm:text-sm text-blue-600">{{ $completedMatches }} completados</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-4-8v8"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Usuarios -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 border border-gray-200 hover-lift">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Usuarios</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">{{ $totalUsers }}</p>
                    <p class="text-xs sm:text-sm text-purple-600">Invitados por ti</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-5 lg:p-6 border border-gray-200 mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Actividad Reciente</h3>
            <button wire:click="refreshStats" class="w-full sm:w-auto px-4 py-2 text-sm text-indigo-600 hover:text-indigo-800 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                Actualizar
            </button>
        </div>
        <div class="space-y-3 sm:space-y-4 max-h-80 overflow-y-auto overflow-touch">
            @forelse($recentActivity as $activity)
                <div class="flex items-start space-x-2 sm:space-x-3 p-2.5 sm:p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm text-gray-900">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $activity['date'] }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-6 sm:py-8 text-sm">No hay actividad reciente</p>
            @endforelse
        </div>
    </div>

    <!-- Estadísticas por Liga -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Mis Ligas</h3>
        </div>
        <div class="overflow-x-auto overflow-touch -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Liga</th>
                            <th class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Deporte</th>
                            <th class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Temporadas</th>
                            <th class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Equipos</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($leagueStats as $league)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $league['name'] }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $league['sport'] }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $league['seasons'] }}</td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $league['teams'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 sm:px-6 py-6 sm:py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                        <p class="text-base sm:text-lg font-medium mb-1">No hay ligas creadas aún</p>
                                        <p class="text-xs sm:text-sm">Crea tu primera liga para comenzar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
