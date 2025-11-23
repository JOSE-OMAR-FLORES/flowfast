<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | Estas son tus llaves de API de Stripe. En modo test (sandbox) puedes
    | usar tarjetas de prueba sin mover dinero real.
    |
    */

    'key' => env('STRIPE_KEY'),
    
    'secret' => env('STRIPE_SECRET'),
    
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | Stripe Test Cards
    |--------------------------------------------------------------------------
    |
    | Tarjetas de prueba disponibles:
    | - 4242 4242 4242 4242 (Éxito)
    | - 4000 0000 0000 9995 (Fondos insuficientes)
    | - 4000 0000 0000 0002 (Tarjeta declinada)
    | - 4000 0000 0000 0341 (Pago requiere autenticación)
    |
    */
    
    'currency' => 'mxn', // Pesos mexicanos
    
];
