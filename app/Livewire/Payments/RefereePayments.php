<?php

namespace App\Livewire\Payments;

use App\Models\Expense;
use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;

class RefereePayments extends Component
{
    use WithPagination;

    public $leagueId;
    public $statusFilter = 'all';
    public $expenseTypeFilter = 'all';

    protected $listeners = ['payment-successful' => 'refreshPayments'];

    public function mount($leagueId = null)
    {
        $this->leagueId = $leagueId;
    }

    public function refreshPayments()
    {
        // Refrescar la página actual de Livewire
        $this->dispatch('$refresh');
        session()->flash('success', '¡Pago procesado exitosamente! La lista se ha actualizado.');
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingExpenseTypeFilter()
    {
        $this->resetPage();
    }

    public function markAsApproved($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        
        if ($expense->payment_status !== 'pending') {
            session()->flash('error', 'Solo puedes aprobar pagos pendientes.');
            return;
        }
        
        $expense->update([
            'payment_status' => 'approved',
            'confirmed_by_admin_user' => auth()->id(),
            'confirmed_by_admin_at' => now(),
        ]);

        session()->flash('success', 'Pago aprobado. Ahora puedes procesarlo.');
    }

    public function markAsReadyForPayment($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        
        if ($expense->payment_status !== 'approved') {
            session()->flash('error', 'El pago debe estar aprobado primero.');
            return;
        }
        
        $expense->update([
            'payment_status' => 'ready_for_payment',
        ]);

        session()->flash('success', 'Pago listo para procesar.');
    }

    public function render()
    {
        $user = auth()->user();
        
        $query = Expense::with(['referee', 'league', 'fixture'])
            ->when($this->leagueId, function($q) {
                $q->where('league_id', $this->leagueId);
            })
            ->when($user->role === 'league_manager' && $user->leagueManager, function($q) use ($user) {
                $q->whereHas('league', function($subQ) use ($user) {
                    $subQ->where('manager_id', $user->leagueManager->id);
                });
            })
            ->when($this->statusFilter !== 'all', function($q) {
                $q->where('payment_status', $this->statusFilter);
            })
            ->when($this->expenseTypeFilter !== 'all', function($q) {
                $q->where('expense_type', $this->expenseTypeFilter);
            })
            ->orderBy('due_date', 'asc');

        $expenses = $query->paginate(10);

        // Obtener ligas disponibles para el filtro
        $leagues = ($user->role === 'admin' || !$user->leagueManager)
            ? League::all() 
            : League::where('manager_id', $user->leagueManager->id)->get();

        return view('livewire.payments.referee-payments', [
            'expenses' => $expenses,
            'leagues' => $leagues,
        ])->layout('layouts.app');
    }
}
