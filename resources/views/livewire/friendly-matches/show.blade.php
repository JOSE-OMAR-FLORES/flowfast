<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('friendly-matches.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                ← Volver a Partidos Amistosos
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">🤝 Detalle del Partido Amistoso</h1>
        </div>

        {{-- Alertas --}}
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Info del Partido --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            @if($match->status === 'scheduled') bg-blue-100 text-blue-800
                            @elseif($match->status === 'live') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($match->status === 'scheduled') 📅 Programado
                            @elseif($match->status === 'live') 🔴 En Vivo
                            @else ✅ Finalizado @endif
                        </span>
                        <span class="text-gray-500">
                            {{ \Carbon\Carbon::parse($match->scheduled_at)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 items-center text-center">
                        <div>
                            <p class="font-bold text-xl text-gray-900">🏠 {{ $match->homeTeam->name }}</p>
                            <p class="text-sm text-gray-500">Local</p>
                        </div>
                        <div>
                            @if($match->status === 'finished')
                                <p class="text-3xl font-bold">{{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}</p>
                            @else
                                <p class="text-2xl text-gray-400">VS</p>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-xl text-gray-900">✈️ {{ $match->awayTeam->name }}</p>
                            <p class="text-sm text-gray-500">Visitante</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 md:mt-0 md:ml-6 text-right">
                    <p class="text-sm text-gray-500">Deporte</p>
                    <p class="font-medium text-purple-600">{{ $match->homeTeam->season->league->sport->name }}</p>
                    @if($match->referee)
                        <p class="text-sm text-gray-500 mt-2">Árbitro</p>
                        <p class="font-medium">{{ $match->referee->user->name ?? 'Sin asignar' }}</p>
                    @endif
                    @if($match->venue)
                        <p class="text-sm text-gray-500 mt-2">Sede</p>
                        <p class="font-medium">{{ $match->venue }}</p>
                    @endif
                </div>
            </div>
            
            @if($match->friendly_notes)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-500">Notas</p>
                    <p class="text-gray-700">{{ $match->friendly_notes }}</p>
                </div>
            @endif
        </div>

        {{-- Resumen Financiero --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Ingresos Totales</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format($totalIncome, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Ingresos Cobrados</p>
                <p class="text-2xl font-bold text-green-700">${{ number_format($totalPaidIncome, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Egresos Totales</p>
                <p class="text-2xl font-bold text-red-600">${{ number_format($totalExpense, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Balance Neto</p>
                <p class="text-2xl font-bold {{ $netBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ${{ number_format($netBalance, 2) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Ingresos (Cuotas de Equipos) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">💰 Ingresos (Cuotas de Equipos)</h2>
                </div>
                <div class="p-4">
                    @if($incomes->count() > 0)
                        <div class="space-y-3">
                            @foreach($incomes as $income)
                                <div class="flex items-center justify-between p-3 rounded-lg border 
                                    {{ $income->payment_status === 'confirmed' ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $income->team->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $income->description }}</p>
                                        @if($income->payment_status === 'confirmed')
                                            <p class="text-xs text-green-600 mt-1">
                                                ✅ Pagado el {{ \Carbon\Carbon::parse($income->paid_at)->format('d/m/Y H:i') }}
                                                @if($income->payment_method)
                                                    - {{ ucfirst($income->payment_method) }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg {{ $income->payment_status === 'confirmed' ? 'text-green-600' : 'text-yellow-600' }}">
                                            ${{ number_format($income->amount, 2) }}
                                        </p>
                                        @if($income->payment_status === 'pending')
                                            <button wire:click="openConfirmIncomeModal({{ $income->id }})"
                                                    class="mt-2 px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                Confirmar Pago
                                            </button>
                                        @else
                                            <button wire:click="cancelIncomePayment({{ $income->id }})"
                                                    wire:confirm="¿Revertir este pago a pendiente?"
                                                    class="mt-2 px-3 py-1 bg-gray-200 text-gray-600 text-sm rounded hover:bg-gray-300">
                                                Revertir
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay ingresos registrados</p>
                    @endif
                </div>
            </div>

            {{-- Egresos (Pagos a Árbitros) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">💸 Egresos (Pagos a Árbitros)</h2>
                </div>
                <div class="p-4">
                    @if($expenses->count() > 0)
                        <div class="space-y-3">
                            @foreach($expenses as $expense)
                                <div class="flex items-center justify-between p-3 rounded-lg border 
                                    {{ $expense->payment_status === 'confirmed' ? 'bg-green-50 border-green-200' : 'bg-orange-50 border-orange-200' }}">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            🧑‍⚖️ {{ $expense->referee->user->name ?? 'Árbitro' }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $expense->description }}</p>
                                        @if($expense->payment_status === 'confirmed')
                                            <p class="text-xs text-green-600 mt-1">
                                                ✅ Pagado el {{ \Carbon\Carbon::parse($expense->paid_at)->format('d/m/Y H:i') }}
                                                @if($expense->payment_method)
                                                    - {{ ucfirst($expense->payment_method) }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg {{ $expense->payment_status === 'confirmed' ? 'text-green-600' : 'text-orange-600' }}">
                                            ${{ number_format($expense->amount, 2) }}
                                        </p>
                                        @if($expense->payment_status === 'pending')
                                            <button wire:click="openConfirmExpenseModal({{ $expense->id }})"
                                                    class="mt-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                                Marcar Pagado
                                            </button>
                                        @else
                                            <button wire:click="cancelExpensePayment({{ $expense->id }})"
                                                    wire:confirm="¿Revertir este pago a pendiente?"
                                                    class="mt-2 px-3 py-1 bg-gray-200 text-gray-600 text-sm rounded hover:bg-gray-300">
                                                Revertir
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay egresos registrados</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Confirmar Ingreso --}}
    @if($showConfirmIncomeModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">💰 Confirmar Pago de Equipo</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                        <select wire:model="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="cash">Efectivo</option>
                            <option value="transfer">Transferencia</option>
                            <option value="card">Tarjeta</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referencia (opcional)</label>
                        <input type="text" wire:model="paymentReference" 
                               placeholder="Número de referencia, folio, etc."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showConfirmIncomeModal', false)"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button wire:click="confirmIncome"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Confirmar Pago
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Confirmar Egreso --}}
    @if($showConfirmExpenseModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">💸 Confirmar Pago a Árbitro</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                        <select wire:model="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="cash">Efectivo</option>
                            <option value="transfer">Transferencia</option>
                            <option value="card">Tarjeta</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referencia (opcional)</label>
                        <input type="text" wire:model="paymentReference" 
                               placeholder="Número de referencia, folio, etc."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showConfirmExpenseModal', false)"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button wire:click="confirmExpense"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Confirmar Pago
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
