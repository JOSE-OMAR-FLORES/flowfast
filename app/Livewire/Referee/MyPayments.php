<?php

namespace App\Livewire\Referee;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\Referee;
use Illuminate\Support\Facades\Auth;

class MyPayments extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $showConfirmModal = false;
    public $selectedExpense = null;
    public $confirmationNotes = '';

    protected $queryString = ['statusFilter'];

    public function mount()
    {
        // Verificar que el usuario sea árbitro
        if (Auth::user()->user_type !== 'referee') {
            abort(403, 'Acceso denegado');
        }
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openConfirmModal($expenseId)
    {
        $this->selectedExpense = Expense::findOrFail($expenseId);
        
        // Verificar que sea un pago para este árbitro
        if ($this->selectedExpense->beneficiary_user_id !== Auth::id()) {
            session()->flash('error', 'Este pago no te pertenece.');
            return;
        }

        // Solo se puede confirmar si está en estado 'paid' o 'ready_for_payment'
        if (!in_array($this->selectedExpense->payment_status, ['paid', 'ready_for_payment'])) {
            session()->flash('error', 'Este pago no está listo para confirmar.');
            return;
        }

        $this->showConfirmModal = true;
        $this->confirmationNotes = '';
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->selectedExpense = null;
        $this->confirmationNotes = '';
    }

    public function confirmPaymentReceived()
    {
        if (!$this->selectedExpense) {
            session()->flash('error', 'No se seleccionó ningún pago.');
            return;
        }

        try {
            $this->selectedExpense->confirmByBeneficiary(Auth::id(), $this->confirmationNotes);
            
            session()->flash('success', '¡Pago confirmado exitosamente! Gracias por confirmar la recepción.');
            $this->closeConfirmModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar el pago: ' . $e->getMessage());
        }
    }

    public function getStats()
    {
        $user = Auth::user();
        // La relación es polimórfica: users.userable_id = referees.id
        $referee = Referee::find($user->userable_id);
        
        if (!$referee) {
            return [
                'pending' => 0,
                'ready' => 0,
                'confirmed' => 0,
                'total_pending' => 0,
                'total_confirmed' => 0,
            ];
        }

        // Construir la consulta base correctamente agrupando las condiciones OR
        $pending = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->whereIn('payment_status', ['pending', 'approved'])
            ->count();

        $ready = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->whereIn('payment_status', ['ready_for_payment', 'paid'])
            ->count();

        $confirmed = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->where('payment_status', 'confirmed')
            ->count();

        $totalPending = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->whereIn('payment_status', ['ready_for_payment', 'paid'])
            ->sum('amount');

        $totalConfirmed = Expense::where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id)
                  ->orWhere('referee_id', $referee->id);
            })
            ->where('payment_status', 'confirmed')
            ->sum('amount');

        return [
            'pending' => $pending,
            'ready' => $ready,
            'confirmed' => $confirmed,
            'total_pending' => $totalPending,
            'total_confirmed' => $totalConfirmed,
        ];
    }

    public function render()
    {
        $user = Auth::user();
        // La relación es polimórfica: users.userable_id = referees.id
        $referee = Referee::find($user->userable_id);

        $query = Expense::with(['league', 'fixture.homeTeam', 'fixture.awayTeam'])
            ->where(function ($q) use ($user, $referee) {
                $q->where('beneficiary_user_id', $user->id);
                if ($referee) {
                    $q->orWhere('referee_id', $referee->id);
                }
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                if ($this->statusFilter === 'pending') {
                    $q->whereIn('payment_status', ['pending', 'approved']);
                } elseif ($this->statusFilter === 'ready') {
                    $q->whereIn('payment_status', ['ready_for_payment', 'paid']);
                } elseif ($this->statusFilter === 'confirmed') {
                    $q->where('payment_status', 'confirmed');
                }
            })
            ->orderBy('created_at', 'desc');

        $expenses = $query->paginate(10);
        $stats = $this->getStats();

        return view('livewire.referee.my-payments', [
            'expenses' => $expenses,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
