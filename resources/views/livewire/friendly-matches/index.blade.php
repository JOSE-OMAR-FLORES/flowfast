<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🤝 Partidos Amistosos</h1>
                    <p class="mt-2 text-sm text-gray-600">Gestiona todos los partidos amistosos</p>
                </div>
                <a href="{{ route('friendly-matches.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    + Nuevo Partido Amistoso
                </a>
            </div>
        </div>

        {{-- Alertas --}}
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Búsqueda --}}
                <div class="md:col-span-2">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="🔍 Buscar por equipo..."
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white">
                </div>

                {{-- Filtro por Deporte --}}
                <div>
                    <select wire:model.live="sport_filter" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white">
                        <option value="">Todos los deportes</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Estado --}}
                <div>
                    <select wire:model.live="status_filter" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white">
                        <option value="">Todos los estados</option>
                        <option value="scheduled">Programados</option>
                        <option value="live">En vivo</option>
                        <option value="finished">Finalizados</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Lista de partidos --}}
        @if($matches->count() > 0)
            {{-- Vista Desktop --}}
            <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deporte</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuotas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($matches as $match)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($match->match_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($match->match_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">🏠 {{ $match->homeTeam->name }}</div>
                                        <div class="text-gray-500">✈️ {{ $match->awayTeam->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ $match->homeTeam->season->league->sport->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($match->status === 'scheduled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            📅 Programado
                                        </span>
                                    @elseif($match->status === 'live')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            🔴 En vivo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            ✅ Finalizado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($match->status === 'finished')
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $match->home_team_score ?? 0 }} - {{ $match->away_team_score ?? 0 }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">
                                            - vs -
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-500">
                                        <div>Local: ${{ number_format($match->home_team_fee, 2) }}</div>
                                        <div>Visit: ${{ number_format($match->away_team_fee, 2) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        @if($match->status === 'scheduled')
                                            <a href="{{ route('matches.edit', $match->id) }}" 
                                               class="text-blue-600 hover:text-blue-900" 
                                               title="Editar">
                                                ✏️
                                            </a>
                                        @endif
                                        <button wire:click="deleteMatch({{ $match->id }})" 
                                                wire:confirm="¿Estás seguro de eliminar este partido amistoso? Se eliminarán también los ingresos y egresos relacionados."
                                                class="text-red-600 hover:text-red-900" 
                                                title="Eliminar">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Vista Mobile --}}
            <div class="md:hidden space-y-4">
                @foreach($matches as $match)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4">
                            {{-- Estado y Fecha --}}
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    @if($match->status === 'scheduled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            📅 Programado
                                        </span>
                                    @elseif($match->status === 'live')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            🔴 En vivo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            ✅ Finalizado
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($match->match_date)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($match->match_time)->format('H:i') }}
                                </div>
                            </div>

                            {{-- Equipos --}}
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-900">🏠 {{ $match->homeTeam->name }}</span>
                                    @if($match->status === 'finished')
                                        <span class="font-bold text-lg">{{ $match->home_team_score ?? 0 }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">✈️ {{ $match->awayTeam->name }}</span>
                                    @if($match->status === 'finished')
                                        <span class="font-bold text-lg">{{ $match->away_team_score ?? 0 }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Detalles --}}
                            <div class="border-t pt-3 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Deporte:</span>
                                    <span class="font-medium">{{ $match->homeTeam->season->league->sport->name }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Cuota Local:</span>
                                    <span class="font-medium">${{ number_format($match->home_team_fee, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Cuota Visitante:</span>
                                    <span class="font-medium">${{ number_format($match->away_team_fee, 2) }}</span>
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="border-t pt-3 mt-3 flex justify-end gap-2">
                                @if($match->status === 'scheduled')
                                    <a href="{{ route('matches.edit', $match->id) }}" 
                                       class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm">
                                        ✏️ Editar
                                    </a>
                                @endif
                                <button wire:click="deleteMatch({{ $match->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar este partido amistoso?"
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm">
                                    🗑️ Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $matches->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-500 text-lg mb-4">No hay partidos amistosos registrados</p>
                <a href="{{ route('friendly-matches.create') }}" class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    + Crear Primer Partido Amistoso
                </a>
            </div>
        @endif
    </div>
</div>
