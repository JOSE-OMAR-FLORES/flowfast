<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Income;

class ConfirmCashIncome extends Component
{
    public $incomeId;
    public $showModal = false;
    public $notes = '';

    public function mount($incomeId)
    {
        $this->incomeId = $incomeId;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->notes = '';
    }

    public function confirmReceipt()
    {
        // Verificar que el usuario es admin, liga manager o referee
        $user = auth()->user();
        if (!in_array($user->user_type, ['admin', 'league_manager', 'referee'])) {
            session()->flash('error', 'No tienes permiso para confirmar pagos.');
            return;
        }

        try {
            $income = Income::findOrFail($this->incomeId);
            
            // Verificar que el pago está pendiente de confirmación
            if ($income->payment_status !== 'pending_confirmation') {
                session()->flash('error', 'Este pago no está pendiente de confirmación.');
                return;
            }

            // Confirmar el pago
            $income->update([
                'payment_status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by_user_id' => auth()->id(),
                'payment_notes' => $income->payment_notes . "\n" . $this->notes,
            ]);

            $this->dispatch('payment-confirmed');
            $this->closeModal();
            
            session()->flash('success', '¡Pago confirmado exitosamente!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al confirmar el pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $income = Income::find($this->incomeId);
        return view('livewire.payments.confirm-cash-income', [
            'income' => $income
        ]);
    }
}
