<?php

namespace App\Livewire\Payments;

use App\Models\Income;
use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;

class TeamPayments extends Component
{
    use WithPagination;

    public $teamId;
    public $statusFilter = 'all';
    public $showPaymentMethods = [];

    protected $listeners = [
        'payment-successful' => 'refreshPayments',
        'payment-confirmed-by-admin' => 'refreshPayments'
    ];

    public function mount($teamId = null)
    {
        $this->teamId = $teamId;
        
        // Verificar que el usuario es entrenador o jugador
        if (!auth()->user() || !in_array(auth()->user()->user_type, ['coach', 'player'])) {
            abort(403, 'No tienes acceso a esta sección.');
        }
    }

    public function refreshPayments()
    {
        $this->dispatch('$refresh');
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        
        // Obtener los IDs de los equipos del usuario según su tipo
        $teamIds = [];
        
        if ($user->user_type === 'coach' && $user->userable) {
            // Un coach tiene UN equipo asignado
            if ($user->userable->team_id) {
                $teamIds = [$user->userable->team_id];
            }
        } elseif ($user->user_type === 'player' && $user->userable) {
            // Un jugador también tiene UN equipo asignado
            if ($user->userable->team_id) {
                $teamIds = [$user->userable->team_id];
            }
        }
        
        // Solo buscar pagos si hay equipos asociados
        $query = Income::with(['league', 'season', 'team']);
        
        if (!empty($teamIds)) {
            $query->whereIn('team_id', $teamIds);
        } else {
            // Si no hay equipos, retornar consulta vacía
            $query->whereRaw('1 = 0');
        }
        
        $query->when($this->statusFilter !== 'all', function($q) {
                $q->where('payment_status', $this->statusFilter);
            })
            ->orderBy('due_date', 'asc');

        $incomes = $query->paginate(10);

        return view('livewire.payments.team-payments', [
            'incomes' => $incomes,
        ])->layout('layouts.app');
    }
}

