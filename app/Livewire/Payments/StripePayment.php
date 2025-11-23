<?php

namespace App\Livewire\Payments;

use App\Models\Income;
use App\Services\StripeService;
use Livewire\Component;

class StripePayment extends Component
{
    public $income; // El pago (Income) que se va a procesar
    public $showModal = false;
    public $clientSecret;
    public $paymentIntentId;
    public $stripePublicKey;
    public $processing = false;
    public $paymentSuccess = false;
    public $errorMessage = '';

    protected $listeners = ['paymentCompleted'];

    public function mount($incomeId)
    {
        $this->income = Income::with('team', 'league')->findOrFail($incomeId);
        $stripeService = new StripeService();
        $this->stripePublicKey = $stripeService->getPublicKey();
    }

    public function openPaymentModal()
    {
        // Crear Payment Intent
        $stripeService = new StripeService();
        
        $result = $stripeService->createPaymentIntent(
            $this->income->amount,
            "Pago de {$this->income->description} - Equipo: {$this->income->team->name}",
            [
                'income_id' => $this->income->id,
                'team_id' => $this->income->team_id,
                'league_id' => $this->income->league_id,
            ]
        );

        if ($result['success']) {
            $this->clientSecret = $result['client_secret'];
            $this->paymentIntentId = $result['payment_intent_id'];
            $this->showModal = true;
            $this->dispatch('stripe-modal-opened');
        } else {
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
        // Verificar el pago con Stripe
        $stripeService = new StripeService();
        
        if ($stripeService->isPaymentSuccessful($paymentIntentId)) {
            // Actualizar el income
            $this->income->update([
                'payment_status' => 'confirmed',
                'payment_method' => 'card',
                'stripe_payment_intent_id' => $paymentIntentId,
                'paid_at' => now(),
                'confirmed_at' => now(),
                'paid_by_user' => auth()->id(),
            ]);

            $this->paymentSuccess = true;
            session()->flash('success', '¡Pago procesado exitosamente!');
            
            // Cerrar modal después de 2 segundos
            $this->dispatch('payment-successful');
        } else {
            $this->errorMessage = 'El pago no pudo ser verificado.';
        }
    }

    public function render()
    {
        return view('livewire.payments.stripe-payment');
    }
}
