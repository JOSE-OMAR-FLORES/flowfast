<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Income;

class TransferTeamPayment extends Component
{
    public $incomeId;
    public $showModal = false;
    public $amount;
    public $reference = '';
    public $bankName = '';
    public $notes = '';

    public function mount($incomeId)
    {
        $this->incomeId = $incomeId;
        $income = Income::find($incomeId);
        $this->amount = $income ? $income->amount : 0;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reference = '';
        $this->bankName = '';
        $this->notes = '';
    }

    public function confirmPayment()
    {
        $this->validate([
            'reference' => 'required|string|max:100',
            'bankName' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $income = Income::findOrFail($this->incomeId);
            
            // Actualizar como pendiente de confirmación
            $income->update([
                'payment_status' => 'pending_confirmation',
                'payment_method' => 'transfer',
                'payment_reference' => $this->reference,
                'bank_name' => $this->bankName,
                'payment_notes' => $this->notes,
                'paid_at' => now(),
                'paid_by_user' => auth()->id(),
            ]);

            $this->dispatch('payment-successful');
            $this->dispatch('payment-made-by-coach')->to('financial.income.index');
            $this->closeModal();
            
            session()->flash('success', '¡Transferencia registrada! Esperando confirmación del administrador.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la transferencia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.payments.transfer-team-payment');
    }
}
