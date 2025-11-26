<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Partido en Vivo</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $sport->emoji ?? '🏆' }} {{ $match->season->league->name }} - {{ $match->season->name }}
                </p>
            </div>
            <a href="{{ route('fixtures.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                ← Volver
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sección Principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Marcador --}}
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg shadow-lg p-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl">{{ $sport->emoji ?? '🏆' }}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $match->isLive() ? 'bg-green-500' : ($match->isFinished() ? 'bg-gray-500' : 'bg-blue-500') }}">
                            {{ ucfirst($match->status) }}
                        </span>
                        @if($match->isLive())
                            <span class="flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                        @endif
                    </div>
                    @if($match->venue)
                        <div class="text-sm opacity-90">📍 {{ $match->venue->name }}</div>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    {{-- Home Team --}}
                    <div class="flex-1 text-center">
                        <div class="text-2xl font-bold mb-2">{{ $match->homeTeam->name }}</div>
                        <div class="text-6xl font-extrabold">{{ $match->home_score ?? 0 }}</div>
                    </div>
                    {{-- VS --}}
                    <div class="px-6">
                        <div class="text-3xl font-bold opacity-75">VS</div>
                    </div>
                    {{-- Away Team --}}
                    <div class="flex-1 text-center">
                        <div class="text-2xl font-bold mb-2">{{ $match->awayTeam->name }}</div>
                        <div class="text-6xl font-extrabold">{{ $match->away_score ?? 0 }}</div>
                    </div>
                </div>
                {{-- Match Controls --}}
                <div class="mt-8 flex justify-center space-x-4">
                    @if($match->status === 'scheduled')
                        <button wire:click="startMatch" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition-colors shadow-lg">
                            ▶ Iniciar Partido
                        </button>
                    @elseif($match->status === 'in_progress')
                        <button wire:click="finishMatch" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors shadow-lg">
                            ⏹ Finalizar Partido
                        </button>
                    @elseif($match->status === 'completed')
                        <span class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold">
                            ✓ Partido Finalizado
                        </span>
                    @endif
                </div>
            </div>

            {{-- Sección de Pagos (solo si el partido ha finalizado) --}}
            @if($match->status === 'completed')
                {{-- Pagos de Equipos (Income) --}}
                @if(in_array(auth()->user()->user_type, ['admin', 'league_manager', 'referee']))
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900">💰 Pagos de Equipos</h3>
                            <span class="text-xs text-gray-500">{{ $match->incomes->count() }} pagos</span>
                        </div>
                        @if($match->incomes->count() > 0)
                            <div class="space-y-2">
                                @foreach($match->incomes as $income)
                                    <div class="flex items-center justify-between p-3 {{ $income->payment_status === 'confirmed' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 text-sm">
                                                {{ $income->team->name ?? 'Equipo' }}
                                            </div>
                                            <div class="text-sm text-gray-700 font-semibold">
                                                ${{ number_format($income->amount, 2) }}
                                            </div>
                                            <div class="text-xs mt-1">
                                                @if($income->payment_status === 'pending')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        🔴 Pendiente de pago
                                                    </span>
                                                @elseif($income->payment_status === 'paid')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        🟡 Pagado - Por confirmar
                                                    </span>
                                                @elseif($income->payment_status === 'confirmed')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        🟢 Confirmado
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($income->payment_status === 'paid')
                                            <button wire:click="confirmTeamPayment({{ $income->id }})" class="ml-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg font-medium transition-colors">
                                                ✓ Confirmar
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-sm text-gray-500">No hay pagos generados</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Pagos a Árbitros (Expense) - Vista para Referee --}}
                @if(auth()->user()->user_type === 'referee')
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900">💵 Mi Pago como Árbitro</h3>
                        </div>
                        @php
                            $myPayments = $match->expenses->where('beneficiary_user_id', auth()->id());
                        @endphp
                        @if($myPayments->count() > 0)
                            <div class="space-y-2">
                                @foreach($myPayments as $expense)
                                    <div class="flex items-center justify-between p-3 {{ $expense->payment_status === 'confirmed' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold text-gray-900">
                                                ${{ number_format($expense->amount, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-600 mt-1">
                                                {{ $expense->description }}
                                            </div>
                                            <div class="text-xs mt-1">
                                                @if($expense->payment_status === 'pending')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        🔴 Pendiente de aprobación
                                                    </span>
                                                @elseif($expense->payment_status === 'approved')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        🟡 Aprobado - En proceso de pago
                                                    </span>
                                                @elseif($expense->payment_status === 'ready_for_payment')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        🟢 Pagado - Confirma recepción
                                                    </span>
                                                @elseif($expense->payment_status === 'confirmed')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✅ Confirmado recibido
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($expense->payment_status === 'ready_for_payment')
                                            <button wire:click="confirmMyPayment({{ $expense->id }})" class="ml-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg font-medium transition-colors">
                                                ✓ Confirmar Recepción
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-sm text-gray-500">No tienes pagos asignados en este partido</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Pagos a Árbitros (Expense) - Vista para Admin/Manager --}}
                @if(in_array(auth()->user()->user_type, ['admin', 'league_manager']))
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900">💵 Pagos a Árbitros</h3>
                            <span class="text-xs text-gray-500">{{ $match->expenses->count() }} pagos</span>
                        </div>
                        @if($match->expenses->count() > 0)
                            <div class="space-y-2">
                                @foreach($match->expenses as $expense)
                                    <div class="flex items-center justify-between p-3 {{ $expense->payment_status === 'confirmed' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 text-sm">
                                                {{ $expense->referee->first_name ?? '' }} {{ $expense->referee->last_name ?? '' }}
                                            </div>
                                            <div class="text-sm text-gray-700 font-semibold">
                                                ${{ number_format($expense->amount, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-600 mt-0.5">
                                                {{ $expense->description }}
                                            </div>
                                            <div class="text-xs mt-1">
                                                @if($expense->payment_status === 'pending')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        🔴 Pendiente de aprobación
                                                    </span>
                                                @elseif($expense->payment_status === 'approved')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        🟡 Aprobado - Pendiente de pagar
                                                    </span>
                                                @elseif($expense->payment_status === 'ready_for_payment')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        🟢 Pagado - Esperando confirmación del árbitro
                                                    </span>
                                                @elseif($expense->payment_status === 'confirmed')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✅ Confirmado por árbitro
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-2 flex flex-col gap-1">
                                            @if($expense->payment_status === 'pending')
                                                <button wire:click="approveRefereePayment({{ $expense->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg font-medium transition-colors whitespace-nowrap">
                                                    ✓ Aprobar
                                                </button>
                                            @elseif($expense->payment_status === 'approved')
                                                <button wire:click="markAsPaid({{ $expense->id }})" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg font-medium transition-colors whitespace-nowrap">
                                                    💵 Marcar Pagado
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-sm text-gray-500">No hay pagos generados</p>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            {{-- Eventos del Partido (Dinámico según deporte) --}}
            @if($match->isLive())
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200 mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-900">
                            {{ $sport->emoji ?? '🏆' }} Eventos del Partido
                        </h3>
                        <span class="text-xs text-gray-500">{{ $sport->name ?? 'Deporte' }}</span>
                    </div>
                    
                    {{-- Eventos que afectan el marcador --}}
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Puntaje / Anotaciones</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($scoringEvents as $eventKey => $eventConfig)
                                <button wire:click="openEventForm('{{ $eventKey }}', {{ $match->home_team_id }})" 
                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-1">
                                    <span>{{ $eventConfig['emoji'] }}</span>
                                    <span class="truncate">{{ $eventConfig['label'] }} Local</span>
                                </button>
                                <button wire:click="openEventForm('{{ $eventKey }}', {{ $match->away_team_id }})" 
                                    class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-1">
                                    <span>{{ $eventConfig['emoji'] }}</span>
                                    <span class="truncate">{{ $eventConfig['label'] }} Visit</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Eventos que NO afectan el marcador --}}
                    @if(count($nonScoringEvents) > 0)
                        <div>
                            <p class="text-xs text-gray-500 mb-2 font-medium">Otros Eventos</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($nonScoringEvents as $eventKey => $eventConfig)
                                    @if($eventKey !== 'substitution')
                                        <button wire:click="openEventForm('{{ $eventKey }}', {{ $match->home_team_id }})" 
                                            class="px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-1">
                                            <span>{{ $eventConfig['emoji'] }}</span>
                                            <span class="truncate">{{ $eventConfig['label'] }} Local</span>
                                        </button>
                                        <button wire:click="openEventForm('{{ $eventKey }}', {{ $match->away_team_id }})" 
                                            class="px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-1">
                                            <span>{{ $eventConfig['emoji'] }}</span>
                                            <span class="truncate">{{ $eventConfig['label'] }} Visit</span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Timeline de Eventos --}}
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">Eventos</h3>
                    <span class="text-xs text-gray-500">{{ count($events) }} eventos</span>
                </div>
                @if(count($events) > 0)
                    <div class="space-y-4 relative">
                        <div class="absolute left-4 top-0 bottom-0 w-px bg-gray-200"></div>
                        @foreach($events as $event)
                            <div class="flex items-start space-x-3 relative">
                                <div class="w-8 h-8 rounded-full {{ $event->team_id == $match->home_team_id ? 'bg-blue-500' : 'bg-green-500' }} flex items-center justify-center text-white font-bold relative z-10">
                                    {{ $event->emoji }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($event->minute){{ $event->minute }}'@endif
                                        @if($event->period) <span class="text-xs text-gray-500">({{ $periodConfig['name'] }} {{ $event->period }})</span> @endif
                                        {{ $event->label }}
                                        @if($event->points > 1)
                                            <span class="text-blue-600 font-bold">+{{ $event->points }}</span>
                                        @endif
                                        @if($event->player)
                                            - {{ $event->player->first_name }} {{ $event->player->last_name }}
                                        @endif
                                        <span class="text-xs text-gray-500">
                                            ({{ $event->team_id == $match->home_team_id ? $match->homeTeam->name : $match->awayTeam->name }})
                                        </span>
                                    </div>
                                    @if(!empty($event->description))
                                        <div class="text-xs text-gray-500 mt-1">{{ $event->description }}</div>
                                    @endif
                                </div>
                                @if($match->isLive())
                                    <button wire:click="deleteEvent({{ $event->id }})" class="text-red-400 hover:text-red-600 text-xs">
                                        ✕
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-gray-500">No hay eventos registrados</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Árbitros --}}
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">🎽 Árbitros</h3>
                    @if($match->status === 'scheduled')
                        <button wire:click="openRefereeModal" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                            + Asignar
                        </button>
                    @endif
                </div>
                @if(count($match->referees) > 0)
                    <div class="space-y-3">
                        @foreach($match->referees as $referee)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <div class="h-2 w-2 rounded-full 
                                        {{ $referee->pivot->referee_type === 'main' ? 'bg-green-500' : 
                                           ($referee->pivot->referee_type === 'assistant' ? 'bg-blue-500' : 'bg-yellow-500') }}">
                                    </div>
                                    <span class="text-sm">
                                        {{ $referee->userable->first_name ?? '' }} {{ $referee->userable->last_name ?? '' }}
                                        <span class="text-xs text-gray-500">
                                            ({{ $referee->pivot->referee_type === 'main' ? 'Principal' : 
                                                ($referee->pivot->referee_type === 'assistant' ? 'Asistente' : 'Cuarto') }})
                                        </span>
                                    </span>
                                </div>
                                @if($match->status === 'scheduled')
                                    <button wire:click="removeReferee('{{ $referee->id }}')" class="text-red-500 hover:text-red-600">
                                        ×
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-sm text-gray-500">No hay árbitros asignados</p>
                    </div>
                @endif
            </div>

            {{-- Información del Partido --}}
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3">ℹ️ Información</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Fecha</dt>
                            <dd class="font-medium">{{ $match->match_date ? \Carbon\Carbon::parse($match->match_date)->format('d/m/Y') : 'No definida' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Hora</dt>
                            <dd class="font-medium">{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i') : 'No definida' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Temporada</dt>
                        <dd class="font-medium">{{ $match->season->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Liga</dt>
                        <dd class="font-medium">{{ $match->season->league->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Estado</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium 
                                {{ $match->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 
                                   ($match->status === 'live' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($match->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Jugadores Equipo Local --}}
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3">👥 {{ $match->homeTeam->name }}</h3>
                @if(count($homePlayers ?? []) > 0)
                    <div class="space-y-1">
                        @foreach($homePlayers as $player)
                            <div class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-50">
                                <span class="w-6 h-6 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center">
                                    {{ $player->jersey_number }}
                                </span>
                                <span class="text-sm">
                                    {{ $player->first_name }} {{ $player->last_name }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-sm text-gray-500">No hay jugadores registrados</p>
                    </div>
                @endif
            </div>

            {{-- Jugadores Equipo Visitante --}}
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3">👥 {{ $match->awayTeam->name }}</h3>
                @if(count($awayPlayers ?? []) > 0)
                    <div class="space-y-1">
                        @foreach($awayPlayers as $player)
                            <div class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-50">
                                <span class="w-6 h-6 rounded-full bg-green-500 text-white text-xs flex items-center justify-center">
                                    {{ $player->jersey_number }}
                                </span>
                                <span class="text-sm">
                                    {{ $player->first_name }} {{ $player->last_name }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-sm text-gray-500">No hay jugadores registrados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal para registrar eventos (Dinámico según deporte) --}}
    <div x-data="{ open: @entangle('showEventForm') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="open" class="fixed inset-0 bg-black opacity-50" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-50" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-50" x-transition:leave-end="opacity-0"></div>
            <div x-show="open" class="bg-white rounded-lg w-full max-w-md mx-auto z-50 overflow-hidden shadow-xl transform transition-all" x-transition:enter="transition-transform ease-out duration-300" x-transition:enter-start="scale-95" x-transition:enter-end="scale-100" x-transition:leave="transition-transform ease-in duration-200" x-transition:leave-start="scale-100" x-transition:leave-end="scale-95">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                        @if(isset($eventTypes[$eventType]))
                            <span>{{ $eventTypes[$eventType]['emoji'] ?? '📝' }}</span>
                            <span>Registrar {{ $eventTypes[$eventType]['label'] ?? 'Evento' }}</span>
                        @else
                            <span>📝 Registrar Evento</span>
                        @endif
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        {{-- Periodo/Cuarto/Set/Inning --}}
                        @if($periodConfig['uses_periods'] ?? false)
                            <div>
                                <label for="period" class="block text-sm font-medium text-gray-700">{{ $periodConfig['name'] ?? 'Periodo' }}</label>
                                <select wire:model="period" id="period" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    @for($i = 1; $i <= ($periodConfig['count'] ?? 4); $i++)
                                        <option value="{{ $i }}">{{ $periodConfig['name'] ?? 'Periodo' }} {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        @endif

                        {{-- Minuto (opcional para deportes sin tiempo corrido) --}}
                        <div>
                            <label for="minute" class="block text-sm font-medium text-gray-700">
                                @if(in_array($sport->slug ?? '', ['basquetbol', 'voleibol']))
                                    Tiempo (opcional)
                                @else
                                    Minuto
                                @endif
                            </label>
                            <input type="number" wire:model="minute" id="minute" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Ej. 35">
                            @error('minute') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Jugador --}}
                        <div>
                            <label for="player_id" class="block text-sm font-medium text-gray-700">Jugador (opcional)</label>
                            <select wire:model="playerId" id="player_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="">Selecciona un jugador</option>
                                @if($teamId == $match->home_team_id)
                                    @if(count($homePlayers ?? []) > 0)
                                        @foreach($homePlayers as $player)
                                            <option value="{{ $player->id }}">{{ $player->jersey_number }} - {{ $player->first_name }} {{ $player->last_name }}</option>
                                        @endforeach
                                    @else
                                        <option disabled>Sin jugadores disponibles</option>
                                    @endif
                                @elseif($teamId == $match->away_team_id)
                                    @if(count($awayPlayers ?? []) > 0)
                                        @foreach($awayPlayers as $player)
                                            <option value="{{ $player->id }}">{{ $player->jersey_number }} - {{ $player->first_name }} {{ $player->last_name }}</option>
                                        @endforeach
                                    @else
                                        <option disabled>Sin jugadores disponibles</option>
                                    @endif
                                @else
                                    <option disabled>Sin jugadores disponibles</option>
                                @endif
                            </select>
                            @error('playerId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Puntos (solo para básquet) --}}
                        @if(in_array($eventType, ['point_1', 'point_2', 'point_3']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Puntos</label>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-2xl font-bold text-blue-600">+{{ $eventTypes[$eventType]['points'] ?? $points }}</span>
                                    <span class="text-gray-500">puntos</span>
                                </div>
                            </div>
                        @endif

                        {{-- Notas --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notas (opcional)</label>
                            <textarea wire:model="description" id="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Detalles adicionales..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-2">
                    <button wire:click="closeEventForm" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="addEvent" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para asignar árbitros --}}
    <div x-data="{ open: @entangle('showRefereeModal') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="open" class="fixed inset-0 bg-black opacity-50" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-50" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-50" x-transition:leave-end="opacity-0"></div>
            <div x-show="open" class="bg-white rounded-lg w-full max-w-md mx-auto z-50 overflow-hidden shadow-xl transform transition-all" x-transition:enter="transition-transform ease-out duration-300" x-transition:enter-start="scale-95" x-transition:enter-end="scale-100" x-transition:leave="transition-transform ease-in duration-200" x-transition:leave-start="scale-100" x-transition:leave-end="scale-95">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900">Asignar Árbitro</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <label for="referee_id" class="block text-sm font-medium text-gray-700">Árbitro</label>
                            <select wire:model="selectedRefereeId" id="referee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="">Selecciona un árbitro</option>
                                @if(count($availableReferees) > 0)
                                    @foreach($availableReferees as $referee)
                                        <option value="{{ $referee['id'] }}">{{ $referee['full_name'] }}</option>
                                    @endforeach
                                @else
                                    <option disabled>No hay árbitros disponibles en esta liga</option>
                                @endif
                            </select>
                            @error('selectedRefereeId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="referee_type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select wire:model="selectedRefereeType" id="referee_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="main">Principal</option>
                                <option value="assistant">Asistente</option>
                                <option value="fourth_official">Cuarto Árbitro</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-2">
                    <button wire:click="closeRefereeModal" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="assignReferee" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Asignar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 