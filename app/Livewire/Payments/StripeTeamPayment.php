<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Income;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeTeamPayment extends Component
{
    public $incomeId;
    public $showModal = false;
    public $clientSecret = null;
    public $publicKey;
    public $processing = false;
    public $amount;

    public function mount($incomeId, $amount = null)
    {
        $this->incomeId = $incomeId;
        $this->publicKey = config('services.stripe.key');
        $this->amount = $amount ?? Income::find($incomeId)?->amount ?? 0;
    }

    public function openModal()
    {
        $income = Income::findOrFail($this->incomeId);
        
        // Crear Payment Intent en Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        
        $paymentIntent = PaymentIntent::create([
            'amount' => $income->amount * 100, // Stripe usa centavos
            'currency' => 'usd',
            'description' => 'Pago de equipo: ' . $income->description,
            'metadata' => [
                'income_id' => $income->id,
                'team_id' => $income->team_id,
                'user_id' => auth()->id(),
            ],
        ]);

        $this->clientSecret = $paymentIntent->client_secret;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->clientSecret = null;
    }

    public function confirmPayment($paymentIntentId)
    {
        $this->processing = true;
        
        try {
            $income = Income::findOrFail($this->incomeId);
            
            // Actualizar el income como pagado y confirmado automáticamente
            $income->update([
                'payment_status' => 'confirmed',
                'payment_method' => 'card',
                'stripe_payment_intent_id' => $paymentIntentId,
                'paid_at' => now(),
                'paid_by_user' => auth()->id(),
                'confirmed_at' => now(),
                'confirmed_by_user_id' => auth()->id(),
            ]);

            $this->dispatch('payment-successful');
            $this->dispatch('payment-made-by-coach')->to('financial.income.index');
            $this->closeModal();
            
            session()->flash('success', '¡Pago procesado y confirmado exitosamente!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar el pago: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }

    public function render()
    {
        return view('livewire.payments.stripe-team-payment');
    }
}
