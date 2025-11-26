<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">📋 Apelaciones de Fecha</h1>
            <p class="mt-2 text-sm text-gray-600">Solicita cambios de fecha/hora para tus partidos</p>
        </div>

        {{-- Alertas --}}
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                {{ session('warning') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabs --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button wire:click="setTab('my_appeals')" 
                            class="px-6 py-3 border-b-2 font-medium text-sm {{ $tab === 'my_appeals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        📝 Mis Apelaciones
                        @if($myAppeals->where('status', 'pending')->count() > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                {{ $myAppeals->where('status', 'pending')->count() }}
                            </span>
                        @endif
                    </button>
                    <button wire:click="setTab('pending_approval')" 
                            class="px-6 py-3 border-b-2 font-medium text-sm {{ $tab === 'pending_approval' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        ⏳ Pendientes de mi Aprobación
                        @if($pendingApproval->count() > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-800">
                                {{ $pendingApproval->count() }}
                            </span>
                        @endif
                    </button>
                    <button wire:click="setTab('new_appeal')" 
                            class="px-6 py-3 border-b-2 font-medium text-sm {{ $tab === 'new_appeal' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        ➕ Nueva Apelación
                    </button>
                </nav>
            </div>
        </div>

        {{-- Tab: Mis Apelaciones --}}
        @if($tab === 'my_appeals')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Mis Solicitudes de Reagendación</h2>
                </div>
                <div class="p-4">
                    @if($myAppeals->count() > 0)
                        <div class="space-y-4">
                            @foreach($myAppeals as $appeal)
                                <div class="border rounded-lg p-4 {{ $appeal->status === 'fully_approved' ? 'bg-green-50 border-green-200' : ($appeal->isRejected() ? 'bg-red-50 border-red-200' : 'bg-gray-50') }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-medium text-gray-900">
                                                    {{ $appeal->fixture->homeTeam->name }} vs {{ $appeal->fixture->awayTeam->name }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs rounded-full 
                                                    @if($appeal->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($appeal->status === 'admin_approved') bg-blue-100 text-blue-800
                                                    @elseif($appeal->status === 'opponent_approved') bg-indigo-100 text-indigo-800
                                                    @elseif($appeal->status === 'fully_approved') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $appeal->status_label }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <p>📅 <strong>Fecha original:</strong> {{ $appeal->original_datetime->format('d/m/Y H:i') }}</p>
                                                <p>📅 <strong>Fecha solicitada:</strong> {{ $appeal->requested_datetime->format('d/m/Y H:i') }}</p>
                                                <p>💬 <strong>Razón:</strong> {{ $appeal->reason }}</p>
                                            </div>
                                            
                                            {{-- Estado de aprobaciones --}}
                                            <div class="mt-3 flex gap-4 text-sm">
                                                <span class="{{ $appeal->admin_approved_at ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ $appeal->admin_approved_at ? '✅' : '⏳' }} Admin
                                                </span>
                                                <span class="{{ $appeal->opponent_approved_at ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ $appeal->opponent_approved_at ? '✅' : '⏳' }} {{ $appeal->opponentTeam->name }}
                                                </span>
                                            </div>

                                            @if($appeal->rejection_reason)
                                                <p class="mt-2 text-sm text-red-600">
                                                    ❌ <strong>Razón del rechazo:</strong> {{ $appeal->rejection_reason }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            @if($appeal->isPending())
                                                <button wire:click="cancelAppeal({{ $appeal->id }})"
                                                        wire:confirm="¿Cancelar esta apelación?"
                                                        class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                                                    Cancelar
                                                </button>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-2">
                                                {{ $appeal->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No has creado ninguna apelación</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Tab: Pendientes de mi Aprobación --}}
        @if($tab === 'pending_approval')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Apelaciones que Requieren tu Aprobación</h2>
                    <p class="text-sm text-gray-500">Otros equipos han solicitado reagendar partidos contigo</p>
                </div>
                <div class="p-4">
                    @if($pendingApproval->count() > 0)
                        <div class="space-y-4">
                            @foreach($pendingApproval as $appeal)
                                <div class="border border-orange-200 rounded-lg p-4 bg-orange-50">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-medium text-gray-900">
                                                    {{ $appeal->fixture->homeTeam->name }} vs {{ $appeal->fixture->awayTeam->name }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-800">
                                                    Esperando tu respuesta
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <p>👤 <strong>Solicitado por:</strong> {{ $appeal->requestingTeam->name }} ({{ $appeal->requestingCoach->user->name ?? 'Coach' }})</p>
                                                <p>📅 <strong>Fecha original:</strong> {{ $appeal->original_datetime->format('d/m/Y H:i') }}</p>
                                                <p>📅 <strong>Fecha solicitada:</strong> {{ $appeal->requested_datetime->format('d/m/Y H:i') }}</p>
                                                <p>💬 <strong>Razón:</strong> {{ $appeal->reason }}</p>
                                            </div>
                                            
                                            @if($appeal->admin_approved_at)
                                                <p class="mt-2 text-sm text-green-600">✅ Ya aprobado por el administrador</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <button wire:click="openApprovalModal({{ $appeal->id }})"
                                                    class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                                                ✅ Aprobar
                                            </button>
                                            <button wire:click="openRejectModal({{ $appeal->id }})"
                                                    class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                ❌ Rechazar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No hay apelaciones pendientes de tu aprobación</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Tab: Nueva Apelación --}}
        @if($tab === 'new_appeal')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Selecciona un Partido para Apelar</h2>
                    <p class="text-sm text-gray-500">Solo puedes apelar partidos programados que aún no han comenzado</p>
                </div>
                <div class="p-4">
                    @if($eligibleMatches->count() > 0)
                        <div class="space-y-3">
                            @foreach($eligibleMatches as $fixture)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            📅 {{ $fixture->match_date->format('d/m/Y') }} {{ $fixture->match_time ?? '' }}
                                            @if($fixture->round_number)
                                                • Jornada {{ $fixture->round_number }}
                                            @endif
                                        </p>
                                    </div>
                                    <button wire:click="openCreateModal({{ $fixture->id }})"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                        📝 Apelar Fecha
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No tienes partidos disponibles para apelar</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Crear Apelación --}}
    @if($showCreateModal && $matchInfo)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📝 Solicitar Reagendación</h3>
                
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <p class="font-medium">{{ $matchInfo['home_team'] }} vs {{ $matchInfo['away_team'] }}</p>
                    <p class="text-sm text-gray-600">Fecha actual: {{ $matchInfo['scheduled_at'] }}</p>
                    <p class="text-sm text-gray-600">{{ $matchInfo['round'] }}</p>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Fecha</label>
                            <input type="date" wire:model="requestedDate" 
                                   @if($maxDate) max="{{ $maxDate }}" @endif
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @if($maxDate)
                                <p class="text-xs text-gray-500 mt-1">Máx: {{ \Carbon\Carbon::parse($maxDate)->format('d/m/Y') }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Hora</label>
                            <input type="time" wire:model="requestedTime"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Razón de la Apelación</label>
                        <textarea wire:model="reason" rows="3"
                                  placeholder="Explica por qué necesitas cambiar la fecha/hora..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg @error('reason') border-red-500 @enderror"></textarea>
                        @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">
                            ⚠️ <strong>Importante:</strong> La apelación debe ser aprobada por el administrador 
                            y por el coach del equipo contrario para que el partido sea reagendado.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeCreateModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button wire:click="createAppeal"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Enviar Apelación
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Aprobar Apelación --}}
    @if($showApprovalModal && $selectedAppeal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">✅ Aprobar Reagendación</h3>
                
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <p class="font-medium">{{ $selectedAppeal->fixture->homeTeam->name }} vs {{ $selectedAppeal->fixture->awayTeam->name }}</p>
                    <p class="text-sm text-gray-600">Fecha original: {{ $selectedAppeal->original_datetime->format('d/m/Y H:i') }}</p>
                    <p class="text-sm text-green-600 font-medium">Nueva fecha: {{ $selectedAppeal->requested_datetime->format('d/m/Y H:i') }}</p>
                    <p class="text-sm text-gray-600 mt-2">Razón: {{ $selectedAppeal->reason }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                    <textarea wire:model="approvalNotes" rows="2"
                              placeholder="Agrega un comentario..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeApprovalModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button wire:click="approveAppeal"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Confirmar Aprobación
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Rechazar Apelación --}}
    @if($showRejectModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">❌ Rechazar Reagendación</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón del Rechazo</label>
                    <textarea wire:model="rejectionReason" rows="3"
                              placeholder="Explica por qué rechazas esta apelación..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg @error('rejectionReason') border-red-500 @enderror"></textarea>
                    @error('rejectionReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeRejectModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button wire:click="rejectAppeal"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Confirmar Rechazo
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
