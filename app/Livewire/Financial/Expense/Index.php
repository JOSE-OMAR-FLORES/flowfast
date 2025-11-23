<?php

namespace App\Livewire\Financial\Expense;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\League;
use App\Models\Season;
use App\Services\ExpenseService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $leagueFilter = '';
    public $seasonFilter = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $showApproveModal = false;
    public $showPaymentModal = false;
    public $selectedExpense = null;
    public $approvalNotes = '';
    public $showPaymentMethods = [];

    protected $listeners = ['payment-successful' => 'refreshExpenses'];

    protected $queryString = ['search', 'leagueFilter', 'seasonFilter', 'typeFilter', 'statusFilter'];

    public function refreshExpenses()
    {
        // Refrescar la lista de expenses
        $this->dispatch('$refresh');
        session()->flash('success', '¡Pago procesado exitosamente! La lista se ha actualizado.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLeagueFilter()
    {
        $this->resetPage();
    }

    public function updatingSeasonFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openApproveModal($expenseId)
    {
        $this->selectedExpense = Expense::findOrFail($expenseId);
        $this->showApproveModal = true;
        $this->approvalNotes = '';
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->selectedExpense = null;
        $this->approvalNotes = '';
    }

    public function openPaymentModal($expenseId)
    {
        $this->selectedExpense = Expense::findOrFail($expenseId);
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedExpense = null;
    }

    public function approveExpense()
    {
        if (!$this->selectedExpense) {
            session()->flash('error', 'Gasto no encontrado.');
            return;
        }

        $user = Auth::user();

        if ($user->user_type !== 'admin' && $user->user_type !== 'league_manager') {
            session()->flash('error', 'No tienes permiso para aprobar gastos.');
            return;
        }

        try {
            $this->selectedExpense->approve($user->id, $this->approvalNotes);
            session()->flash('success', 'Gasto aprobado exitosamente.');
            $this->closeApproveModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al aprobar el gasto: ' . $e->getMessage());
        }
    }

    public function markAsPaid()
    {
        if (!$this->selectedExpense) {
            session()->flash('error', 'Gasto no encontrado.');
            return;
        }

        $user = Auth::user();

        if ($user->user_type !== 'admin') {
            session()->flash('error', 'Solo los administradores pueden marcar gastos como pagados.');
            return;
        }

        try {
            $this->selectedExpense->markAsReadyForPayment($user->id);
            session()->flash('success', 'Gasto marcado como listo para pago. El beneficiario debe confirmar la recepción.');
            $this->closePaymentModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function confirmReceipt($expenseId)
    {
        $user = Auth::user();
        
        try {
            $expense = Expense::findOrFail($expenseId);
            
            // Verificar que el usuario sea el beneficiario
            if ($expense->beneficiary_id !== $user->id) {
                session()->flash('error', 'Solo el beneficiario puede confirmar la recepción del pago.');
                return;
            }

            $expense->confirmByBeneficiary($user->id);
            session()->flash('success', 'Recepción de pago confirmada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function cancelExpense($expenseId)
    {
        $user = Auth::user();

        if ($user->user_type !== 'admin') {
            session()->flash('error', 'Solo los administradores pueden cancelar gastos.');
            return;
        }

        try {
            $expense = Expense::findOrFail($expenseId);
            $expense->cancel($user->id);
            session()->flash('success', 'Gasto cancelado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        $expensesQuery = Expense::with(['league', 'season', 'referee', 'beneficiary', 'match'])
            ->orderBy('created_at', 'desc');

        // Filtro por rol
        if ($user->user_type === 'league_manager') {
            $expensesQuery->whereHas('league', function ($query) use ($user) {
                $query->where('league_manager_id', $user->userable_id);
            });
        } elseif ($user->user_type === 'referee') {
            // Los árbitros solo ven sus propios pagos
            $expensesQuery->where('beneficiary_id', $user->id);
        }

        // Aplicar filtros
        if ($this->search) {
            $expensesQuery->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('beneficiary', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->leagueFilter) {
            $expensesQuery->where('league_id', $this->leagueFilter);
        }

        if ($this->seasonFilter) {
            $expensesQuery->where('season_id', $this->seasonFilter);
        }

        if ($this->typeFilter) {
            $expensesQuery->where('expense_type', $this->typeFilter);
        }

        if ($this->statusFilter) {
            $expensesQuery->where('payment_status', $this->statusFilter);
        }

        $expenses = $expensesQuery->paginate(15);

        // Obtener datos para filtros
        $leagues = League::all();
        $seasons = Season::all();

        return view('livewire.financial.expense.index', [
            'expenses' => $expenses,
            'leagues' => $leagues,
            'seasons' => $seasons,
        ])->layout('layouts.app');
    }
}
