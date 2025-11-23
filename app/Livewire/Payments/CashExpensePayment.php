<?php

namespace App\Livewire\Payments;

use App\Models\Expense;
use Livewire\Component;

class CashExpensePayment extends Component
{
    public $expense;
    public $showModal = false;
    public $paymentSuccess = false;
    public $amount;
    public $notes = '';

    public function mount($expenseId)
    {
        $this->expense = Expense::with('referee', 'league')->findOrFail($expenseId);
        $this->amount = $this->expense->amount;
    }

    public function openPaymentModal()
    {
        if ($this->expense->payment_status !== 'ready_for_payment') {
            session()->flash('error', 'El pago debe estar en estado "Listo para pagar" primero.');
            return;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->paymentSuccess = false;
        $this->notes = '';
    }

    public function confirmPayment()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Actualizar el expense
        $this->expense->update([
            'payment_status' => 'confirmed',
            'payment_method' => 'cash',
            'paid_at' => now(),
            'confirmed_at' => now(),
            'confirmed_by_system_user' => auth()->id(),
            'payment_notes' => $this->notes,
        ]);

        $this->paymentSuccess = true;
        session()->flash('success', '¡Pago en efectivo registrado exitosamente!');
        
        // Emitir evento para refrescar la lista
        $this->dispatch('payment-successful');

        // Cerrar modal después de 2 segundos
        $this->dispatch('close-modal-after-delay');
    }

    public function render()
    {
        return view('livewire.payments.cash-expense-payment');
    }
}
