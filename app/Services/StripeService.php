<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Crear un Payment Intent para procesar el pago
     * 
     * @param float $amount Monto en pesos mexicanos
     * @param string $description Descripción del pago
     * @param array $metadata Metadata adicional
     * @return array
     */
    public function createPaymentIntent($amount, $description, $metadata = [])
    {
        try {
            // Convertir a centavos (Stripe trabaja en centavos)
            $amountInCents = (int) ($amount * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => config('stripe.currency', 'mxn'),
                'description' => $description,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener información de un Payment Intent
     * 
     * @param string $paymentIntentId
     * @return PaymentIntent|null
     */
    public function getPaymentIntent($paymentIntentId)
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Get Payment Intent Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar si un pago fue exitoso
     * 
     * @param string $paymentIntentId
     * @return bool
     */
    public function isPaymentSuccessful($paymentIntentId)
    {
        $paymentIntent = $this->getPaymentIntent($paymentIntentId);
        
        return $paymentIntent && $paymentIntent->status === 'succeeded';
    }

    /**
     * Obtener el monto pagado
     * 
     * @param string $paymentIntentId
     * @return float|null
     */
    public function getPaymentAmount($paymentIntentId)
    {
        $paymentIntent = $this->getPaymentIntent($paymentIntentId);
        
        if (!$paymentIntent) {
            return null;
        }

        // Convertir de centavos a pesos
        return $paymentIntent->amount / 100;
    }

    /**
     * Reembolsar un pago
     * 
     * @param string $paymentIntentId
     * @param float|null $amount Monto a reembolsar (null = total)
     * @return array
     */
    public function refundPayment($paymentIntentId, $amount = null)
    {
        try {
            $refundData = ['payment_intent' => $paymentIntentId];
            
            if ($amount !== null) {
                $refundData['amount'] = (int) ($amount * 100);
            }

            $refund = \Stripe\Refund::create($refundData);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Refund Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener la clave pública de Stripe
     * 
     * @return string
     */
    public function getPublicKey()
    {
        return config('stripe.key');
    }
}
