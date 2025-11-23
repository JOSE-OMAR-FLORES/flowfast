<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Importar Jugadores</h1>
                <p class="mt-1 text-sm text-gray-500">Carga masiva de jugadores desde CSV o Excel</p>
            </div>
            <a href="{{ route('players.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                ← Volver
            </a>
        </div>
    </div>

    {{-- Progreso --}}
    <div class="mb-6">
        <div class="flex items-center justify-center">
            <div class="flex items-center">
                <div class="flex items-center {{ $step >= 1 ? 'text-blue-600' : 'text-gray-400' }}">
                    <div class="flex items-center justify-center w-10 h-10 border-2 {{ $step >= 1 ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }} rounded-full">
                        <span class="font-semibold">1</span>
                    </div>
                    <span class="ml-2 font-medium">Subir Archivo</span>
                </div>
                <div class="w-24 h-1 mx-4 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                <div class="flex items-center {{ $step >= 2 ? 'text-blue-600' : 'text-gray-400' }}">
                    <div class="flex items-center justify-center w-10 h-10 border-2 {{ $step >= 2 ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }} rounded-full">
                        <span class="font-semibold">2</span>
                    </div>
                    <span class="ml-2 font-medium">Vista Previa</span>
                </div>
                <div class="w-24 h-1 mx-4 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                <div class="flex items-center {{ $step >= 3 ? 'text-blue-600' : 'text-gray-400' }}">
                    <div class="flex items-center justify-center w-10 h-10 border-2 {{ $step >= 3 ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }} rounded-full">
                        <span class="font-semibold">3</span>
                    </div>
                    <span class="ml-2 font-medium">Resultado</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            {{-- PASO 1: SUBIR ARCHIVO --}}
            @if($step === 1)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📁 Paso 1: Subir Archivo</h3>
                    
                    <form wire:submit="processFile" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Liga *</label>
                            <select wire:model.live="league_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Selecciona una liga</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                                @endforeach
                            </select>
                            @error('league_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo *</label>
                            <select wire:model="team_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Selecciona un equipo</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Archivo (CSV o Excel) *</label>
                            <input 
                                type="file" 
                                wire:model="file"
                                accept=".csv,.txt,.xlsx,.xls"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            
                            @if($file)
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="text-green-600 mr-2">✓</span>
                                        <span class="text-sm text-green-800">{{ $file->getClientOriginalName() }}</span>
                                        <span class="ml-auto text-xs text-green-600">{{ number_format($file->getSize() / 1024, 2) }} KB</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button 
                                type="submit"
                                class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>Procesar Archivo →</span>
                                <span wire:loading>Procesando...</span>
                            </button>
                            <a 
                                href="{{ route('players.index') }}"
                                class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            @endif

            {{-- PASO 2: VISTA PREVIA --}}
            @if($step === 2)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">👀 Paso 2: Vista Previa</h3>
                    
                    {{-- Resumen --}}
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $preview['total'] }}</div>
                            <div class="text-sm text-blue-800">Total Filas</div>
                        </div>
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $preview['valid'] }}</div>
                            <div class="text-sm text-green-800">Válidas</div>
                        </div>
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $preview['invalid'] }}</div>
                            <div class="text-sm text-red-800">Con Errores</div>
                        </div>
                    </div>

                    {{-- Filas válidas --}}
                    @if(count($validRows) > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-green-700 mb-2">✓ Filas Válidas ({{ count($validRows) }})</h4>
                            <div class="overflow-x-auto max-h-96 border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">#</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Nombre</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Posición</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Núm.</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($validRows as $row)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-sm">{{ $row['row_number'] }}</td>
                                                <td class="px-3 py-2 text-sm font-medium">{{ $row['first_name'] }} {{ $row['last_name'] }}</td>
                                                <td class="px-3 py-2 text-sm">{{ $positions[$row['position']] ?? $row['position'] }}</td>
                                                <td class="px-3 py-2 text-sm">{{ $row['jersey_number'] ?? '-' }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-500">{{ $row['email'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Filas inválidas --}}
                    @if(count($invalidRows) > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-red-700 mb-2">✗ Filas con Errores ({{ count($invalidRows) }})</h4>
                            <div class="overflow-x-auto max-h-96 border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-red-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">#</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Nombre</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Errores</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($invalidRows as $row)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-sm">{{ $row['row_number'] }}</td>
                                                <td class="px-3 py-2 text-sm">{{ $row['first_name'] ?? '' }} {{ $row['last_name'] ?? '' }}</td>
                                                <td class="px-3 py-2 text-sm text-red-600">
                                                    @foreach($row['errors'] as $error)
                                                        <div>• {{ $error }}</div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Botones --}}
                    <div class="flex gap-3 pt-4 border-t">
                        <button 
                            wire:click="import"
                            class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors"
                            wire:loading.attr="disabled"
                            @if(count($validRows) === 0) disabled @endif
                        >
                            <span wire:loading.remove>Importar {{ count($validRows) }} Jugadores</span>
                            <span wire:loading>Importando...</span>
                        </button>
                        <button 
                            wire:click="resetImport"
                            class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                        >
                            ← Cancelar
                        </button>
                    </div>
                </div>
            @endif

            {{-- PASO 3: RESULTADO --}}
            @if($step === 3)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🎉 Paso 3: Resultado</h3>
                    
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">
                            @if($importErrors === 0)
                                ✅
                            @else
                                ⚠️
                            @endif
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Importación Completada</h2>
                        <p class="text-gray-600 mb-6">
                            Se importaron <strong class="text-green-600">{{ $imported }}</strong> jugadores exitosamente
                            @if($importErrors > 0)
                                <br>con <strong class="text-red-600">{{ $importErrors }}</strong> errores
                            @endif
                        </p>

                        <div class="flex gap-3 justify-center">
                            <a 
                                href="{{ route('players.index') }}"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors"
                            >
                                Ver Jugadores
                            </a>
                            <button 
                                wire:click="resetImport"
                                class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                            >
                                Importar Más
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar de Información --}}
        <div class="space-y-6">
            {{-- Formato del archivo --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">📋 Formato del Archivo</h3>
                <p class="text-sm text-blue-800 mb-3">El archivo debe contener las siguientes columnas:</p>
                <ul class="text-xs text-blue-800 space-y-1">
                    <li>• <strong>first_name</strong> (obligatorio)</li>
                    <li>• <strong>last_name</strong> (obligatorio)</li>
                    <li>• <strong>email</strong> (opcional)</li>
                    <li>• <strong>phone</strong> (opcional)</li>
                    <li>• <strong>birth_date</strong> (opcional, YYYY-MM-DD)</li>
                    <li>• <strong>jersey_number</strong> (opcional, 0-999)</li>
                    <li>• <strong>position</strong> (obligatorio)</li>
                    <li>• <strong>status</strong> (opcional)</li>
                </ul>
            </div>

            {{-- Posiciones --}}
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 mb-2">🎯 Posiciones Válidas</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• goalkeeper o Portero</li>
                    <li>• defender o Defensa</li>
                    <li>• midfielder o Mediocampista</li>
                    <li>• forward o Delantero</li>
                </ul>
            </div>

            {{-- Estados --}}
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 mb-2">🏷️ Estados Válidos</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• active o Activo (predeterminado)</li>
                    <li>• injured o Lesionado</li>
                    <li>• suspended o Suspendido</li>
                    <li>• inactive o Inactivo</li>
                </ul>
            </div>

            {{-- Plantilla --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="font-semibold text-green-900 mb-2">📥 Plantilla</h3>
                <p class="text-sm text-green-800 mb-3">Descarga una plantilla de ejemplo:</p>
                <a 
                    href="{{ route('players.download-template') }}"
                    class="inline-block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center font-medium rounded-lg transition-colors"
                >
                    Descargar CSV
                </a>
            </div>
        </div>
    </div>
</div>
