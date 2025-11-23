<div class="min-h-screen bg-gray-50 py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard del Entrenador</h1>
            <p class="mt-2 text-xs sm:text-sm text-gray-600">Bienvenido, gestiona tus equipos y jugadores</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
            <!-- Equipos -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Mis Equipos</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $teams->count() }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('coach.teams.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 mt-3 sm:mt-4 inline-block transition-colors">
                    Ver equipos →
                </a>
            </div>

            <!-- Jugadores -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Jugadores</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $totalPlayers }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('coach.players.index') }}" class="text-xs sm:text-sm text-green-600 hover:text-green-800 mt-3 sm:mt-4 inline-block transition-colors">
                    Ver jugadores →
                </a>
            </div>

            <!-- Pagos Pendientes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Pagos Pendientes</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">${{ number_format($pendingPayments, 2) }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('coach.payments.index') }}" class="text-xs sm:text-sm text-red-600 hover:text-red-800 mt-3 sm:mt-4 inline-block transition-colors">
                    Ver pagos →
                </a>
            </div>
        </div>

        <!-- Mis Equipos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 sm:mb-8 overflow-hidden">
            <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Mis Equipos</h2>
            </div>
            <div class="p-3 sm:p-4 lg:p-6">
                @if($teams->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                        @foreach($teams as $team)
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition-all hover-lift">
                                <div class="flex items-start justify-between mb-2 sm:mb-3 gap-2">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $team->name }}</h3>
                                        <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $team->season->league->name }}</p>
                                    </div>
                                    @if($team->logo)
                                        <img src="{{ $team->logo }}" alt="{{ $team->name }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex-shrink-0 object-cover">
                                    @endif
                                </div>
                                <div class="space-y-1.5 sm:space-y-2 mb-3 sm:mb-4">
                                    <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="truncate">{{ $team->players->count() }} Jugadores</span>
                                    </div>
                                    <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="truncate">{{ $team->season->name }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('coach.teams.show', $team) }}" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 text-xs sm:text-sm font-medium transition-colors">
                                        Ver Equipo
                                    </a>
                                    <a href="{{ route('coach.teams.edit', $team) }}" class="flex-1 text-center px-3 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 text-xs sm:text-sm font-medium transition-colors">
                                        Editar
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 sm:py-12">
                        <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="mt-2 text-sm sm:text-base text-gray-500">No tienes equipos asignados</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Próximos Partidos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Próximos Partidos</h2>
            </div>
            <div class="p-3 sm:p-4 lg:p-6">
                @if(count($upcomingMatches) > 0)
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($upcomingMatches as $match)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border border-gray-200 rounded-lg p-3 sm:p-4 gap-3 sm:gap-4 hover:shadow-md transition-all">
                                <div class="flex-1">
                                    <div class="flex flex-col xs:flex-row xs:items-center gap-2 xs:gap-3 sm:gap-4">
                                        <div class="text-center xs:text-left flex-1">
                                            <p class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $match['home_team']['name'] ?? 'TBD' }}</p>
                                        </div>
                                        <div class="text-gray-400 font-medium text-xs sm:text-sm text-center">VS</div>
                                        <div class="text-center xs:text-left flex-1">
                                            <p class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $match['away_team']['name'] ?? 'TBD' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center sm:text-right flex-shrink-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($match['scheduled_at'])->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($match['scheduled_at'])->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 sm:py-12">
                        <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm sm:text-base text-gray-500">No hay partidos próximos</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
