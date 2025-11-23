@extends('layouts.app')

@section('page-title', 'Dashboard del Árbitro')

@section('content')
<div class="space-y-6">
    <!-- Welcome Message -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Bienvenido, Árbitro
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ auth()->user()->email }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Referee Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Partidos Arbitrados -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 713.138-3.138z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Partidos Arbitrados
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                23
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos Partidos -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Próximos Partidos
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                3
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjetas Mostradas -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h2a2 2 0 002-2V5z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Tarjetas Mostradas
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                15
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ganancias del Mes -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Ingresos del Mes
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                $1,150
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Próximo Partido Asignado -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Próximo Partido Asignado
                </h3>
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-4">
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
                        <div class="mt-3 space-y-1">
                            <p class="text-xs text-gray-500">Pago: $50 USD</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Confirmado
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas del Mes -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Estadísticas de Diciembre
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Partidos Arbitrados</span>
                        <span class="text-sm font-bold text-gray-900">8</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Tarjetas Amarillas</span>
                        <span class="text-sm font-bold text-yellow-600">12</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Tarjetas Rojas</span>
                        <span class="text-sm font-bold text-red-600">2</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Penales Pitados</span>
                        <span class="text-sm font-bold text-blue-600">3</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Promedio de Faltas</span>
                        <span class="text-sm font-bold text-purple-600">18.5</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule & Payments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Horarios de la Semana -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Horarios de Esta Semana
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Domingo 15 Dec - 3:00 PM</p>
                            <p class="text-xs text-gray-500">Águilas FC vs Tigres United</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Confirmado
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Miércoles 18 Dec - 7:00 PM</p>
                            <p class="text-xs text-gray-500">Leones SC vs Halcones FC</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pendiente
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Sábado 21 Dec - 5:00 PM</p>
                            <p class="text-xs text-gray-500">Dragones FC vs Cóndores FC</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Confirmado
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Pagos -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Historial de Pagos
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Pago Noviembre 2024</p>
                            <p class="text-xs text-gray-500">8 partidos arbitrados</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600">$400</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Pagado
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Pago Diciembre 2024</p>
                            <p class="text-xs text-gray-500">5 partidos completados</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-blue-600">$250</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Procesando
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Rating -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Calificación del Rendimiento
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600 mb-1">4.8</div>
                    <p class="text-sm font-medium text-gray-900">Puntuación General</p>
                    <p class="text-xs text-gray-500">De 5.0</p>
                </div>
                
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600 mb-1">95%</div>
                    <p class="text-sm font-medium text-gray-900">Precisión</p>
                    <p class="text-xs text-gray-500">En decisiones</p>
                </div>
                
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-3xl font-bold text-yellow-600 mb-1">92%</div>
                    <p class="text-sm font-medium text-gray-900">Puntualidad</p>
                    <p class="text-xs text-gray-500">Llegada a tiempo</p>
                </div>
                
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-3xl font-bold text-purple-600 mb-1">4.9</div>
                    <p class="text-sm font-medium text-gray-900">Profesionalismo</p>
                    <p class="text-xs text-gray-500">Evaluación coaches</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection