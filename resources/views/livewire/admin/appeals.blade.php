<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">📋 Gestión de Apelaciones</h1>
                    <p class="mt-1 text-sm text-gray-600">Administra las solicitudes de reagendación de partidos</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
            <div class="flex flex-wrap gap-2">
                <button wire:click="setStatusFilter('pending')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'pending' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    ⏳ Pendientes
                </button>
                <button wire:click="setStatusFilter('admin_approved')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'admin_approved' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    ✅ Esperando Oponente
                </button>
                <button wire:click="setStatusFilter('approved')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'approved' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    🎉 Aprobadas
                </button>
                <button wire:click="setStatusFilter('rejected')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'rejected' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    ❌ Rechazadas
                </button>
                <button wire:click="setStatusFilter('all')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    📁 Todas
                </button>
            </div>
        </div>

        <!-- Tabla de Apelaciones (Desktop) -->
        <div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($appeals->isEmpty())
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay apelaciones</h3>
                    <p class="mt-1 text-sm text-gray-500">No se encontraron apelaciones con los filtros seleccionados.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partido</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Actual</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitada</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($appeals as $appeal)
                                <tr class="hover:bg-gray-50" wire:key="appeal-{{ $appeal->id }}">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $appeal->fixture?->homeTeam?->name ?? 'N/A' }} vs {{ $appeal->fixture?->awayTeam?->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($appeal->fixture?->round_number)
                                                🏆 Jornada {{ $appeal->fixture->round_number }} - {{ $appeal->fixture->season?->name ?? '' }}
                                            @else
                                                ⚽ Partido sin jornada 
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $appeal->requestingTeam?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">👤 {{ $appeal->requestingCoach?->user?->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            📅 {{ $appeal->original_datetime?->format('d/m/Y') ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            🕐 {{ $appeal->original_datetime?->format('H:i') ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            📅 {{ $appeal->requested_datetime?->format('d/m/Y') ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            🕐 {{ $appeal->requested_datetime?->format('H:i') ?? '' }}
                                        </div>
                                        @if($appeal->max_reschedule_date)
                                            <div class="text-xs text-orange-500">
                                                ⚠️ Máx: {{ $appeal->max_reschedule_date->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($appeal->status) }}">
                                            {{ $this->getStatusLabel($appeal->status) }}
                                        </span>
                                        @if($appeal->admin_approved_at)
                                            <div class="text-xs text-green-600 mt-1">✅ Admin aprobó</div>
                                        @endif
                                        @if($appeal->opponent_approved_at)
                                            <div class="text-xs text-green-600">✅ Oponente aprobó</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            @if($appeal->canBeApprovedByAdmin())
                                                <button wire:click="openApproveModal({{ $appeal->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Aprobar
                                                </button>
                                                <button wire:click="openRejectModal({{ $appeal->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Rechazar
                                                </button>
                                            @endif

                                            @if($appeal->status === 'fully_approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ✅ Reagendado
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @if($appeal->reason || $appeal->rejection_reason)
                                    <tr class="bg-gray-50" wire:key="appeal-details-{{ $appeal->id }}">
                                        <td colspan="6" class="px-6 py-3">
                                            @if($appeal->reason)
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700">💬 Razón de la solicitud:</span>
                                                    <span class="text-gray-600">{{ $appeal->reason }}</span>
                                                </div>
                                            @endif
                                            @if($appeal->rejection_reason)
                                                <div class="text-sm mt-1">
                                                    <span class="font-medium text-red-700">❌ Razón del rechazo:</span>
                                                    <span class="text-red-600">{{ $appeal->rejection_reason }}</span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($appeals->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $appeals->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Cards de Apelaciones (Mobile & Tablet) -->
        <div class="lg:hidden space-y-4">
            @forelse($appeals as $appeal)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4" wire:key="appeal-card-{{ $appeal->id }}">
                    <!-- Header con partido -->
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ $appeal->fixture?->homeTeam?->name ?? 'N/A' }} vs {{ $appeal->fixture?->awayTeam?->name ?? 'N/A' }}
                            </h3>
                            <div class="text-xs text-gray-500 mt-1">
                                @if($appeal->fixture?->round_number)
                                    🏆 Jornada {{ $appeal->fixture->round_number }}
                                @else
                                    ⚽ Partido sin jornada
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($appeal->status) }}">
                            {{ $this->getStatusLabel($appeal->status) }}
                        </span>
                    </div>

                    <!-- Solicitante -->
                    <div class="mb-3 pb-3 border-b border-gray-200">
                        <div class="text-xs font-medium text-gray-500 uppercase mb-1">👤 Solicitante</div>
                        <div class="text-sm font-medium text-gray-900">{{ $appeal->requestingTeam?->name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $appeal->requestingCoach?->user?->name ?? 'N/A' }}</div>
                    </div>

                    <!-- Fechas -->
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase mb-1">📅 Fecha Actual</div>
                            <div class="text-sm text-gray-900">{{ $appeal->original_datetime?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase mb-1">📅 Fecha Solicitada</div>
                            <div class="text-sm text-gray-900">{{ $appeal->requested_datetime?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Razón -->
                    @if($appeal->reason)
                        <div class="mb-3 pb-3 border-b border-gray-200">
                            <div class="text-xs font-medium text-gray-500 uppercase mb-1">💬 Razón</div>
                            <div class="text-sm text-gray-600">{{ $appeal->reason }}</div>
                        </div>
                    @endif

                    <!-- Estado de aprobaciones -->
                    <div class="flex gap-4 mb-4 text-sm">
                        <span class="{{ $appeal->admin_approved_at ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $appeal->admin_approved_at ? '✅' : '⏳' }} Admin
                        </span>
                        <span class="{{ $appeal->opponent_approved_at ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $appeal->opponent_approved_at ? '✅' : '⏳' }} Oponente
                        </span>
                    </div>

                    @if($appeal->rejection_reason)
                        <div class="mb-4 p-2 bg-red-50 rounded-lg">
                            <div class="text-xs font-medium text-red-700">❌ Razón del rechazo:</div>
                            <div class="text-sm text-red-600">{{ $appeal->rejection_reason }}</div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    @if($appeal->canBeApprovedByAdmin())
                        <div class="flex gap-2 pt-3 border-t border-gray-200">
                            <button wire:click="openApproveModal({{ $appeal->id }})"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Aprobar
                            </button>
                            <button wire:click="openRejectModal({{ $appeal->id }})"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Rechazar
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay apelaciones</h3>
                    <p class="mt-1 text-sm text-gray-500">No se encontraron apelaciones con los filtros seleccionados.</p>
                </div>
            @endforelse

            @if($appeals->hasPages())
                <div class="mt-4">
                    {{ $appeals->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Aprobación -->
    @if($showApproveModal && $selectedAppeal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    ✅ Aprobar Apelación
                                </h3>
                                <div class="mt-4 space-y-3">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $selectedAppeal->fixture?->homeTeam?->name }} vs {{ $selectedAppeal->fixture?->awayTeam?->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Solicitante: {{ $selectedAppeal->requestingTeam?->name }}
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-xs text-gray-500">Fecha actual</p>
                                            <p class="text-sm font-medium">{{ $selectedAppeal->original_datetime?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Fecha solicitada</p>
                                            <p class="text-sm font-medium text-green-600">{{ $selectedAppeal->requested_datetime?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @if($selectedAppeal->reason)
                                        <div>
                                            <p class="text-xs text-gray-500">Razón</p>
                                            <p class="text-sm">{{ $selectedAppeal->reason }}</p>
                                        </div>
                                    @endif
                                    <div class="p-3 bg-blue-50 rounded-lg">
                                        <p class="text-sm text-blue-700">
                                            ℹ️ Al aprobar, la apelación aún requerirá la aprobación del equipo oponente ({{ $selectedAppeal->opponentTeam?->name }}) para que el partido sea reagendado.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button wire:click="approveAppeal" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                            Aprobar
                        </button>
                        <button wire:click="closeModals" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Rechazo -->
    @if($showRejectModal && $selectedAppeal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    ❌ Rechazar Apelación
                                </h3>
                                <div class="mt-4">
                                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700">
                                        Razón del rechazo *
                                    </label>
                                    <textarea
                                        wire:model="rejectionReason"
                                        id="rejectionReason"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Explica por qué se rechaza esta solicitud..."></textarea>
                                    @error('rejectionReason')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button wire:click="rejectAppeal" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                            Rechazar
                        </button>
                        <button wire:click="closeModals" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
