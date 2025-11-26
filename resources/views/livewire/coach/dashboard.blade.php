<div class="min-h-screen bg-gray-50 py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8">
        
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">🏆 Dashboard del Entrenador</h1>
            <p class="mt-2 text-sm text-gray-600">Bienvenido, {{ $coach->first_name ?? 'Entrenador' }}. Gestiona tus equipos y revisa tu rendimiento.</p>
        </div>

        {{-- Stats Cards Principal --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
            {{-- Equipos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mis Equipos</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $teams->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">⚽</span>
                    </div>
                </div>
            </div>

            {{-- Jugadores --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jugadores</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalPlayers }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                </div>
            </div>

            {{-- Pagos Pendientes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Por Pagar</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">${{ number_format($paymentsSummary['pending_amount'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">💳</span>
                    </div>
                </div>
            </div>

            {{-- Pagos Confirmados --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pagado</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($paymentsSummary['paid_amount'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">✅</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Columna Principal (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Estadísticas de Equipos --}}
                @if($teams->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            📊 Estadísticas de Mis Equipos
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PJ</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">G</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">E</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">P</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GF</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GC</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DIF</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PTS</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($teams as $team)
                                    @php $stats = $teamStats[$team->id] ?? null; @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                                    {{ strtoupper(substr($team->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 text-sm">{{ $team->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $team->season->league->name ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center text-sm text-gray-600">{{ $stats['played'] ?? 0 }}</td>
                                        <td class="px-3 py-3 text-center text-sm font-medium text-green-600">{{ $stats['wins'] ?? 0 }}</td>
                                        <td class="px-3 py-3 text-center text-sm text-gray-600">{{ $stats['draws'] ?? 0 }}</td>
                                        <td class="px-3 py-3 text-center text-sm font-medium text-red-600">{{ $stats['losses'] ?? 0 }}</td>
                                        <td class="px-3 py-3 text-center text-sm text-gray-600">{{ $stats['goals_for'] ?? 0 }}</td>
                                        <td class="px-3 py-3 text-center text-sm text-gray-600">{{ $stats['goals_against'] ?? 0 }}</td>
                                        <td class="px-3 py-3 text-center text-sm {{ ($stats['goal_difference'] ?? 0) > 0 ? 'text-green-600' : (($stats['goal_difference'] ?? 0) < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ ($stats['goal_difference'] ?? 0) > 0 ? '+' : '' }}{{ $stats['goal_difference'] ?? 0 }}
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 font-bold rounded-full text-sm">
                                                {{ $stats['points'] ?? 0 }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Próximos Partidos --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            📅 Próximos Partidos
                        </h2>
                    </div>
                    <div class="p-4">
                        @if($upcomingMatches->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingMatches as $match)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center gap-4 flex-1">
                                            {{-- Fecha --}}
                                            <div class="text-center flex-shrink-0">
                                                <div class="text-xs text-gray-500 uppercase">{{ \Carbon\Carbon::parse($match->match_date)->isoFormat('ddd') }}</div>
                                                <div class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($match->match_date)->format('d') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($match->match_date)->isoFormat('MMM') }}</div>
                                            </div>
                                            
                                            {{-- Equipos --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-medium text-gray-900 truncate {{ in_array($match->home_team_id, $teams->pluck('id')->toArray()) ? 'text-blue-600' : '' }}">
                                                        {{ $match->homeTeam->name }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-gray-900 truncate {{ in_array($match->away_team_id, $teams->pluck('id')->toArray()) ? 'text-blue-600' : '' }}">
                                                        {{ $match->awayTeam->name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Hora y Lugar --}}
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-lg font-bold text-green-600">
                                                {{ \Carbon\Carbon::parse($match->match_date)->format('H:i') }}
                                            </div>
                                            @if($match->venue)
                                                <div class="text-xs text-gray-500">{{ $match->venue->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-3xl">📅</span>
                                </div>
                                <p class="text-gray-500">No hay partidos programados próximamente</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Últimos Resultados --}}
                @if($recentResults->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            🏁 Últimos Resultados
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            @foreach($recentResults as $match)
                                @php
                                    $myTeamId = $teams->pluck('id')->first(fn($id) => $id == $match->home_team_id || $id == $match->away_team_id);
                                    $isHome = $match->home_team_id == $myTeamId;
                                    $myScore = $isHome ? $match->home_score : $match->away_score;
                                    $opponentScore = $isHome ? $match->away_score : $match->home_score;
                                    $result = $myScore > $opponentScore ? 'win' : ($myScore < $opponentScore ? 'loss' : 'draw');
                                    $resultColor = $result === 'win' ? 'bg-green-100 text-green-800' : ($result === 'loss' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800');
                                    $resultText = $result === 'win' ? 'V' : ($result === 'loss' ? 'D' : 'E');
                                @endphp
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-full {{ $resultColor }} flex items-center justify-center font-bold text-sm">
                                            {{ $resultText }}
                                        </span>
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ $match->homeTeam->name }} 
                                                <span class="font-bold">{{ $match->home_score }}</span> - 
                                                <span class="font-bold">{{ $match->away_score }}</span> 
                                                {{ $match->awayTeam->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($match->match_date)->isoFormat('D MMM YYYY') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Columna Lateral (1/3) --}}
            <div class="space-y-6">
                
                {{-- Resumen de Pagos --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            💰 Estado de Pagos
                        </h2>
                    </div>
                    <div class="p-4 space-y-4">
                        {{-- Pendientes --}}
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pendientes</p>
                                    <p class="text-xs text-gray-500">{{ $paymentsSummary['pending_count'] }} pagos</p>
                                </div>
                            </div>
                            <span class="text-lg font-bold text-orange-600">${{ number_format($paymentsSummary['pending_amount'], 0) }}</span>
                        </div>

                        {{-- Por Confirmar --}}
                        @if($paymentsSummary['awaiting_count'] > 0)
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Por Confirmar</p>
                                    <p class="text-xs text-gray-500">{{ $paymentsSummary['awaiting_count'] }} pagos</p>
                                </div>
                            </div>
                            <span class="text-lg font-bold text-blue-600">${{ number_format($paymentsSummary['awaiting_amount'], 0) }}</span>
                        </div>
                        @endif

                        {{-- Confirmados --}}
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pagados</p>
                                    <p class="text-xs text-gray-500">{{ $paymentsSummary['paid_count'] }} pagos</p>
                                </div>
                            </div>
                            <span class="text-lg font-bold text-green-600">${{ number_format($paymentsSummary['paid_amount'], 0) }}</span>
                        </div>

                        <a href="{{ route('coach.payments.index') }}" 
                           class="block w-full text-center py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                            Ver todos los pagos →
                        </a>
                    </div>
                </div>

                {{-- Pagos Próximos a Vencer --}}
                @if($pendingPayments->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            ⚠️ Pagos Pendientes
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($pendingPayments as $payment)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 text-sm truncate">{{ $payment->description ?? 'Cuota' }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $payment->team->name ?? '' }}</p>
                                        @if($payment->due_date)
                                            <p class="text-xs {{ \Carbon\Carbon::parse($payment->due_date)->isPast() ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                                Vence: {{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-lg font-bold text-gray-900">${{ number_format($payment->amount, 0) }}</span>
                                        <div class="mt-1">
                                            @if($payment->payment_status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pendiente
                                                </span>
                                            @elseif($payment->payment_status === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Aprobado
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Acciones Rápidas --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">⚡ Acciones Rápidas</h2>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('coach.teams.index') }}" 
                           class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-blue-50 rounded-lg transition-colors group">
                            <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center transition-colors">
                                <span class="text-xl">👥</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Mis Equipos</p>
                                <p class="text-xs text-gray-500">Ver y gestionar equipos</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('coach.players.index') }}" 
                           class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-green-50 rounded-lg transition-colors group">
                            <div class="w-10 h-10 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center transition-colors">
                                <span class="text-xl">🏃</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Jugadores</p>
                                <p class="text-xs text-gray-500">Administrar plantilla</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('coach.payments.index') }}" 
                           class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-orange-50 rounded-lg transition-colors group">
                            <div class="w-10 h-10 bg-orange-100 group-hover:bg-orange-200 rounded-lg flex items-center justify-center transition-colors">
                                <span class="text-xl">💳</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Pagos</p>
                                <p class="text-xs text-gray-500">Ver estado de cuotas</p>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
