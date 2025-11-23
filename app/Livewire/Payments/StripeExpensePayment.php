<?php

namespace App\Livewire\Payments;

use App\Models\Expense;
use App\Services\StripeService;
use Livewire\Component;

class StripeExpensePayment extends Component
{
    public $expense; // El pago (Expense) que se va a procesar
    public $showModal = false;
    public $clientSecret;
    public $paymentIntentId;
    public $stripePublicKey;
    public $processing = false;
    public $paymentSuccess = false;
    public $errorMessage = '';

    protected $listeners = ['paymentCompleted'];

    public function mount($expenseId)
    {
        $this->expense = Expense::with('referee', 'league')->findOrFail($expenseId);
        $stripeService = new StripeService();
        $this->stripePublicKey = $stripeService->getPublicKey();
    }

    public function openPaymentModal()
    {
        \Log::info('openPaymentModal llamado para expense: ' . $this->expense->id);
        \Log::info('Estado actual: ' . $this->expense->payment_status);
        
        // Verificar que el pago esté listo para procesar
        if ($this->expense->payment_status !== 'ready_for_payment') {
            \Log::warning('Pago no está en estado ready_for_payment');
            session()->flash('error', 'El pago debe estar en estado "Listo para pagar" primero.');
            return;
        }

        // Crear Payment Intent
        $stripeService = new StripeService();
        
        $refereeName = $this->expense->referee 
            ? "{$this->expense->referee->first_name} {$this->expense->referee->last_name}"
            : 'Árbitro';
        
        \Log::info('Creando Payment Intent para: ' . $refereeName);
        
        $result = $stripeService->createPaymentIntent(
            $this->expense->amount,
            "Pago a árbitro: {$refereeName} - {$this->expense->description}",
            [
                'expense_id' => $this->expense->id,
                'referee_id' => $this->expense->referee_id,
                'league_id' => $this->expense->league_id,
                'expense_type' => $this->expense->expense_type,
            ]
        );

        \Log::info('Resultado de Payment Intent:', $result);

        if ($result['success']) {
            $this->clientSecret = $result['client_secret'];
            $this->paymentIntentId = $result['payment_intent_id'];
            $this->showModal = true;
            
            \Log::info('Modal abierto, emitiendo evento stripe-modal-opened-' . $this->expense->id);
            
            // Emitir evento con los datos necesarios para Stripe
            $this->dispatch('stripe-modal-opened-' . $this->expense->id, [
                'clientSecret' => $this->clientSecret,
                'publicKey' => $this->stripePublicKey,
                'expenseId' => $this->expense->id,
            ]);
        } else {
            \Log::error('Error al crear Payment Intent: ' . $result['error']);
            $this->errorMessage = $result['error'];
            session()->flash('error', 'Error al iniciar el pago: ' . $result['error']);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->clientSecret = null;
        $this->paymentIntentId = null;
        $this->processing = false;
        $this->paymentSuccess = false;
        $this->errorMessage = '';
    }

    public function paymentCompleted($paymentIntentId)
    {
        \Log::info('paymentCompleted llamado', ['paymentIntentId' => $paymentIntentId, 'expense_id' => $this->expense->id]);
        // Verificar el pago con Stripe
        $stripeService = new StripeService();
        $isSuccess = $stripeService->isPaymentSuccessful($paymentIntentId);
        \Log::info('Resultado de isPaymentSuccessful', ['success' => $isSuccess]);
        if ($isSuccess) {
            // Actualizar el expense
            $this->expense->update([
                'payment_status' => 'confirmed',
                'payment_method' => 'card',
                'stripe_payment_intent_id' => $paymentIntentId,
                'paid_at' => now(),
                'confirmed_at' => now(),
                'confirmed_by_system_user' => auth()->id(),
            ]);
            \Log::info('Expense actualizado a confirmado', ['expense_id' => $this->expense->id]);
            $this->paymentSuccess = true;
            session()->flash('success', '¡Pago al árbitro procesado exitosamente!');
            // Cerrar modal después de 2 segundos
            $this->dispatch('payment-successful');
        } else {
            \Log::warning('El pago no pudo ser verificado', ['paymentIntentId' => $paymentIntentId]);
            $this->errorMessage = 'El pago no pudo ser verificado.';
        }
    }

    public function render()
    {
        return view('livewire.payments.stripe-expense-payment');
    }
}
