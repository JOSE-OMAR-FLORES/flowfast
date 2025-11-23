<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Income;
use Illuminate\Support\Facades\Log;

class CashTeamPayment extends Component
{
    public $incomeId;
    public $showModal = false;
    public $amount;
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
        $this->notes = '';
    }

    public function confirmPayment()
    {
        $this->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $income = Income::findOrFail($this->incomeId);
            
            Log::info('Procesando pago en efectivo', [
                'income_id' => $this->incomeId,
                'user_id' => auth()->id(),
                'amount' => $income->amount,
            ]);
            
            // Actualizar como pagado por equipo (esperando confirmación admin)
            $income->update([
                'payment_status' => 'paid_by_team',
                'payment_method' => 'cash',
                'notes' => $this->notes,
                'paid_at' => now(),
                'paid_by_user' => auth()->id(),
            ]);

            Log::info('Pago en efectivo procesado correctamente', ['income_id' => $this->incomeId]);

            $this->dispatch('payment-successful');
            $this->dispatch('payment-made-by-coach')->to('financial.income.index');
            $this->closeModal();
            
            session()->flash('success', '¡Pago en efectivo registrado! Esperando confirmación del administrador.');
        } catch (\Exception $e) {
            Log::error('Error al procesar pago en efectivo', [
                'income_id' => $this->incomeId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.payments.cash-team-payment');
    }
}
