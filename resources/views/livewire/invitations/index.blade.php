<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Invitaciones</h1>
                <p class="mt-1 text-sm text-gray-500">Gestiona las invitaciones enviadas a usuarios</p>
            </div>
            <a href="{{ route('invitations.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                + Nueva Invitación
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Búsqueda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Token, liga o equipo..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            {{-- Filtro por tipo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select wire:model.live="tokenTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los tipos</option>
                    <option value="league_manager">Encargado de Liga</option>
                    <option value="coach">Entrenador</option>
                    <option value="player">Jugador</option>
                    <option value="referee">Árbitro</option>
                </select>
            </div>

            {{-- Filtro por liga --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Liga</label>
                <select wire:model.live="leagueFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todas las ligas</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="valid">Válidos</option>
                    <option value="expired">Expirados</option>
                    <option value="exhausted">Agotados</option>
                </select>
            </div>
        </div>

        {{-- Botón limpiar filtros --}}
        @if($search || $tokenTypeFilter || $leagueFilter || $statusFilter)
            <div class="mt-3">
                <button wire:click="clearFilters" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Limpiar filtros
                </button>
            </div>
        @endif
    </div>

    {{-- Lista de tokens --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($tokens->isEmpty())
            <div class="p-12 text-center">
                <div class="text-gray-400 mb-3">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg mb-2">No hay invitaciones</p>
                <p class="text-sm text-gray-500">Crea una nueva invitación para invitar usuarios</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liga/Equipo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Usos</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Expira</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tokens as $token)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded w-full block">{{ url('/invite/' . $token->token) }}</code>
                                        <button 
                                            wire:click="copyToken({{ $token->id }})"
                                            class="text-gray-400 hover:text-gray-600 text-xs flex items-center gap-1"
                                            title="Copiar URL de invitación"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            Copiar URL
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($token->token_type === 'league_manager') bg-purple-100 text-purple-800
                                        @elseif($token->token_type === 'coach') bg-blue-100 text-blue-800
                                        @elseif($token->token_type === 'player') bg-green-100 text-green-800
                                        @elseif($token->token_type === 'referee') bg-yellow-100 text-yellow-800
                                        @endif
                                    ">
                                        @if($token->token_type === 'league_manager') Encargado
                                        @elseif($token->token_type === 'coach') Entrenador
                                        @elseif($token->token_type === 'player') Jugador
                                        @elseif($token->token_type === 'referee') Árbitro
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $token->targetLeague->name ?? '-' }}</div>
                                        @if($token->targetTeam)
                                            <div class="text-gray-500">{{ $token->targetTeam->name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm {{ $token->current_uses >= $token->max_uses ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                        {{ $token->current_uses }} / {{ $token->max_uses }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ $token->expires_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($token->current_uses >= $token->max_uses)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            Agotado
                                        </span>
                                    @elseif($token->expires_at->isPast())
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            Expirado
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button 
                                        wire:click="revokeToken({{ $token->id }})"
                                        wire:confirm="¿Estás seguro de revocar este token? Esta acción no se puede deshacer."
                                        class="text-red-600 hover:text-red-700 text-sm font-medium"
                                    >
                                        Revocar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tokens->links() }}
            </div>
        @endif
    </div>

    {{-- Scripts para copiar token --}}
    @script
    <script>
        $wire.on('token-copied', (event) => {
            const url = event[0].url;
            navigator.clipboard.writeText(url).then(() => {
                alert('Enlace de invitación copiado al portapapeles:\n\n' + url);
            });
        });

        $wire.on('success', (event) => {
            alert(event[0]);
        });

        $wire.on('error', (event) => {
            alert('Error: ' + event[0]);
        });
    </script>
    @endscript
</div>
