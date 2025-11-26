<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-xl shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">👨‍⚖️ Panel del Árbitro</h1>
                    <p class="text-gray-300 mt-1">
                        Bienvenido, {{ $referee ? $referee->first_name . ' ' . $referee->last_name : auth()->user()->email }}
                    </p>
                </div>
                @if($stats['ready_to_confirm'] > 0)
                    <a href="{{ route('referee.my-payments') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 rounded-lg font-semibold transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $stats['ready_to_confirm'] }} pago(s) por confirmar
                    </a>
                @endif
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Partidos Arbitrados --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Partidos Arbitrados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['matches_refereed'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Próximos Partidos --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Próximos Partidos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_matches'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Pagos Pendientes --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pagos en Proceso</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_payments'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Ganancias del Mes --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ganancias del Mes</p>
                        <p class="text-2xl font-bold text-purple-600">${{ number_format($stats['month_earnings'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerta de Pagos por Confirmar --}}
        @if($paymentsToConfirm->count() > 0)
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-green-800">¡Tienes pagos por confirmar!</h3>
                        <p class="text-sm text-green-700 mt-1">El administrador ha registrado pagos a tu favor. Por favor confirma que los has recibido.</p>
                        <div class="mt-4 space-y-2">
                            @foreach($paymentsToConfirm as $payment)
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-green-200">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $payment->description }}</p>
                                        <p class="text-sm text-gray-500">{{ $payment->league->name ?? '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-green-600">${{ number_format($payment->amount, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('referee.my-payments') }}" 
                           class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Ir a Confirmar Pagos
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Próximos Partidos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">📅 Próximos Partidos</h3>
                </div>
                <div class="p-6">
                    @forelse($upcomingMatches as $match)
                        <div class="flex items-center justify-between p-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $match->homeTeam->name ?? 'N/A' }} vs {{ $match->awayTeam->name ?? 'N/A' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">
                                    {{ $match->match_date ? $match->match_date->format('D d M, Y - H:i') : 'Fecha por definir' }}
                                </p>
                                @if($match->season && $match->season->league)
                                    <p class="text-xs text-gray-400 mt-1">{{ $match->season->league->name }}</p>
                                @endif
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($match->status === 'scheduled') bg-blue-100 text-blue-800
                                @elseif($match->status === 'in_progress') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($match->status === 'scheduled') Programado
                                @elseif($match->status === 'in_progress') En Vivo
                                @else {{ $match->status }}
                                @endif
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-500">No tienes partidos programados</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Historial de Pagos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">💵 Historial de Pagos</h3>
                    <a href="{{ route('referee.my-payments') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Ver todos →
                    </a>
                </div>
                <div class="p-6">
                    @forelse($recentPayments as $payment)
                        <div class="flex items-center justify-between p-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $payment->description }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->league->name ?? '' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold 
                                    @if($payment->payment_status === 'confirmed') text-green-600
                                    @elseif(in_array($payment->payment_status, ['ready_for_payment', 'paid'])) text-purple-600
                                    @else text-gray-600
                                    @endif">
                                    ${{ number_format($payment->amount, 2) }}
                                </p>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    @if($payment->payment_status === 'confirmed') bg-green-100 text-green-800
                                    @elseif(in_array($payment->payment_status, ['ready_for_payment', 'paid'])) bg-purple-100 text-purple-800
                                    @elseif($payment->payment_status === 'approved') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    @if($payment->payment_status === 'confirmed') ✓ Confirmado
                                    @elseif(in_array($payment->payment_status, ['ready_for_payment', 'paid'])) Por confirmar
                                    @elseif($payment->payment_status === 'approved') Aprobado
                                    @else Pendiente
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500">No hay historial de pagos</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Acciones Rápidas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('referee.my-payments') }}" 
                   class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200">
                    <div class="flex-shrink-0 p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Mis Pagos</p>
                        <p class="text-xs text-gray-500">Ver y confirmar pagos</p>
                    </div>
                </a>

                @if($referee && $referee->league_id)
                    <a href="{{ route('fixtures.index', ['leagueId' => $referee->league_id]) }}" 
                       class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200">
                        <div class="flex-shrink-0 p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Ver Calendario</p>
                            <p class="text-xs text-gray-500">Partidos programados</p>
                        </div>
                    </a>
                @endif

                <a href="#" 
                   class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200">
                    <div class="flex-shrink-0 p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Mi Perfil</p>
                        <p class="text-xs text-gray-500">Editar información</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
