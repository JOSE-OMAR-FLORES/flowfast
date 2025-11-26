<?php

namespace App\Livewire\FriendlyMatches;

use Livewire\Component;
use App\Models\GameMatch;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class Show extends Component
{
    public GameMatch $match;
    
    // Para confirmar pagos
    public $showConfirmIncomeModal = false;
    public $showConfirmExpenseModal = false;
    public $selectedIncomeId = null;
    public $selectedExpenseId = null;
    public $paymentMethod = 'cash';
    public $paymentReference = '';

    public function mount($id)
    {
        $this->match = GameMatch::with([
            'homeTeam.season.league.sport',
            'awayTeam',
            'referee.user',
            'incomes.team',
            'expenses.referee.user'
        ])->where('is_friendly', true)->findOrFail($id);
    }

    // Confirmar pago de ingreso (equipo pagó)
    public function openConfirmIncomeModal($incomeId)
    {
        $this->selectedIncomeId = $incomeId;
        $this->paymentMethod = 'cash';
        $this->paymentReference = '';
        $this->showConfirmIncomeModal = true;
    }

    public function confirmIncome()
    {
        $income = Income::findOrFail($this->selectedIncomeId);
        
        $income->update([
            'payment_status' => 'confirmed',
            'payment_method' => $this->paymentMethod,
            'payment_reference' => $this->paymentReference ?: null,
            'paid_at' => now(),
            'paid_by_user' => auth()->id(),
            'confirmed_by_admin_at' => now(),
            'confirmed_by_admin_user' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $this->showConfirmIncomeModal = false;
        $this->selectedIncomeId = null;
        session()->flash('success', 'Pago confirmado exitosamente');
        
        // Refrescar datos
        $this->mount($this->match->id);
    }

    // Cancelar pago confirmado
    public function cancelIncomePayment($incomeId)
    {
        $income = Income::findOrFail($incomeId);
        
        $income->update([
            'payment_status' => 'pending',
            'payment_method' => null,
            'payment_reference' => null,
            'paid_at' => null,
            'paid_by_user' => null,
            'confirmed_by_admin_at' => null,
            'confirmed_by_admin_user' => null,
            'confirmed_at' => null,
        ]);

        session()->flash('success', 'Pago revertido a pendiente');
        $this->mount($this->match->id);
    }

    // Confirmar pago de egreso (pago al árbitro)
    public function openConfirmExpenseModal($expenseId)
    {
        $this->selectedExpenseId = $expenseId;
        $this->paymentMethod = 'cash';
        $this->paymentReference = '';
        $this->showConfirmExpenseModal = true;
    }

    public function confirmExpense()
    {
        $expense = Expense::findOrFail($this->selectedExpenseId);
        
        $expense->update([
            'payment_status' => 'confirmed',
            'payment_method' => $this->paymentMethod,
            'payment_reference' => $this->paymentReference ?: null,
            'paid_at' => now(),
            'paid_by' => auth()->id(),
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $this->showConfirmExpenseModal = false;
        $this->selectedExpenseId = null;
        session()->flash('success', 'Pago al árbitro confirmado');
        
        $this->mount($this->match->id);
    }

    // Cancelar pago de egreso
    public function cancelExpensePayment($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        
        $expense->update([
            'payment_status' => 'pending',
            'payment_method' => null,
            'payment_reference' => null,
            'paid_at' => null,
            'paid_by' => null,
            'approved_at' => null,
            'approved_by' => null,
            'confirmed_at' => null,
        ]);

        session()->flash('success', 'Pago revertido a pendiente');
        $this->mount($this->match->id);
    }

    public function render()
    {
        $incomes = $this->match->incomes()->with('team')->get();
        $expenses = $this->match->expenses()->with('referee.user')->get();
        
        $totalIncome = $incomes->sum('amount');
        $totalPaidIncome = $incomes->where('payment_status', 'confirmed')->sum('amount');
        $totalPendingIncome = $incomes->where('payment_status', 'pending')->sum('amount');
        
        $totalExpense = $expenses->sum('amount');
        $totalPaidExpense = $expenses->where('payment_status', 'confirmed')->sum('amount');
        $totalPendingExpense = $expenses->where('payment_status', 'pending')->sum('amount');

        return view('livewire.friendly-matches.show', [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalPaidIncome' => $totalPaidIncome,
            'totalPendingIncome' => $totalPendingIncome,
            'totalExpense' => $totalExpense,
            'totalPaidExpense' => $totalPaidExpense,
            'totalPendingExpense' => $totalPendingExpense,
            'netBalance' => $totalPaidIncome - $totalPaidExpense,
        ])->layout('layouts.app', ['title' => 'Detalle Partido Amistoso']);
    }
}
