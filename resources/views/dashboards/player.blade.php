@extends('layouts.app')

@section('page-title', 'Dashboard del Jugador')

@section('content')
<div class="space-y-6">
    <!-- Welcome Message -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Bienvenido, Jugador
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ auth()->user()->email }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Partidos Jugados -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-4-8v8"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Partidos Jugados
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                9
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goles -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Goles
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                5
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asistencias -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Asistencias
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                3
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Minutos Jugados -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Minutos
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                720
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mi Equipo -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Mi Equipo
                </h3>
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-4">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl font-bold text-white">AF</span>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Águilas FC</h4>
                        <p class="text-sm text-gray-600">Delantero</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center">
                            <p class="font-medium text-gray-900">Posición Liga</p>
                            <p class="text-2xl font-bold text-purple-600">1º</p>
                        </div>
                        <div class="text-center">
                            <p class="font-medium text-gray-900">Puntos</p>
                            <p class="text-2xl font-bold text-purple-600">20</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximo Partido -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Próximo Partido
                </h3>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-900">Águilas FC</p>
                            <p class="text-xs text-gray-500">(Local)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">VS</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-900">Tigres United</p>
                            <p class="text-xs text-gray-500">(Visitante)</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Domingo 15 Diciembre, 2024</p>
                        <p class="text-sm text-gray-600">3:00 PM - Estadio Central</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-2">
                            Confirmado para jugar
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart & Recent Matches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rendimiento Personal -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Mi Rendimiento
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Promedio de Goles por Partido</p>
                            <p class="text-xs text-gray-500">Últimos 5 partidos</p>
                        </div>
                        <span class="text-lg font-bold text-green-600">0.8</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Precisión de Pases</p>
                            <p class="text-xs text-gray-500">Esta temporada</p>
                        </div>
                        <span class="text-lg font-bold text-blue-600">85%</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Minutos por Partido</p>
                            <p class="text-xs text-gray-500">Promedio</p>
                        </div>
                        <span class="text-lg font-bold text-yellow-600">80</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Partidos -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Mis Últimos Partidos
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Águilas FC 3-1 Leones SC</p>
                            <p class="text-xs text-gray-500">2 goles, 80 minutos</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Victoria
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Dragones FC 1-1 Águilas FC</p>
                            <p class="text-xs text-gray-500">0 goles, 90 minutos</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Empate
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Águilas FC 2-0 Halcones FC</p>
                            <p class="text-xs text-gray-500">1 gol, 1 asistencia, 90 minutos</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Victoria
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievements -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Logros de la Temporada
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Mejor Jugador</p>
                    <p class="text-xs text-gray-500">Semana 8</p>
                </div>
                
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Hat-trick</p>
                    <p class="text-xs text-gray-500">vs Halcones FC</p>
                </div>
                
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-lg font-bold text-white">5</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Goles Consecutivos</p>
                    <p class="text-xs text-gray-500">Racha actual</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection