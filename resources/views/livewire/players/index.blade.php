<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Jugadores</h1>
                <p class="mt-1 text-sm text-gray-500">Gestiona los jugadores de los equipos</p>
            </div>
            <a href="{{ route('players.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                ➕ Nuevo Jugador
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Buscar jugador..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                >
            </div>

            <div>
                <select wire:model.live="leagueFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Todas las ligas</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select wire:model.live="teamFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Todos los equipos</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select wire:model.live="positionFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Todas las posiciones</option>
                    @foreach($positions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button 
                    wire:click="clearFilters" 
                    class="w-full px-3 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors text-sm"
                >
                    🔄 Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($players->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jugador</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estadísticas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($players as $player)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            @if($player->photo)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($player->photo) }}" alt="{{ $player->full_name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-600 font-semibold text-sm">
                                                        {{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $player->full_name }}</div>
                                            @if($player->age)
                                                <div class="text-sm text-gray-500">{{ $player->age }} años</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $player->team->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $player->league->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">
                                        @if($player->position === 'goalkeeper') 🧤
                                        @elseif($player->position === 'defender') 🛡️
                                        @elseif($player->position === 'midfielder') ⚙️
                                        @elseif($player->position === 'forward') ⚽
                                        @endif
                                        {{ $positions[$player->position] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($player->jersey_number)
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-600 text-white font-bold text-sm">
                                            {{ $player->jersey_number }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs space-y-1">
                                        <div>⚽ <span class="font-medium">{{ $player->goals_count }}</span> goles</div>
                                        <div>🎯 <span class="font-medium">{{ $player->assists_count }}</span> asist.</div>
                                        <div>
                                            <span class="text-yellow-600">🟨 {{ $player->yellow_cards_count }}</span>
                                            <span class="text-red-600 ml-2">🟥 {{ $player->red_cards_count }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = $statusColors[$player->status] ?? 'gray';
                                        $colorClasses = [
                                            'green' => 'bg-green-100 text-green-800',
                                            'red' => 'bg-red-100 text-red-800',
                                            'yellow' => 'bg-yellow-100 text-yellow-800',
                                            'gray' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClasses[$color] }}">
                                        {{ $statuses[$player->status] ?? $player->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('players.edit', $player) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        ✏️ Editar
                                    </a>
                                    <button 
                                        wire:click="deletePlayer({{ $player->id }})" 
                                        wire:confirm="¿Eliminar a {{ $player->full_name }}?"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        🗑️ Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t">
                {{ $players->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay jugadores</h3>
                <p class="mt-1 text-sm text-gray-500">Comienza agregando un nuevo jugador.</p>
                <div class="mt-6">
                    <a href="{{ route('players.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        ➕ Agregar Jugador
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Scripts --}}
    @script
    <script>
        $wire.on('player-deleted', (playerName) => {
            alert('Jugador "' + playerName + '" eliminado exitosamente');
        });

        $wire.on('error', (message) => {
            alert('Error: ' + message);
        });
    </script>
    @endscript
</div>
