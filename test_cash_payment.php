<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Income;
use App\Models\User;

echo "=== Test de actualización de pago en efectivo ===\n\n";

// Buscar el income ID 34 (pendiente)
$income = Income::find(34);

if (!$income) {
    echo "No se encontró el income ID 34\n";
    exit;
}

echo "Income encontrado:\n";
echo "- ID: {$income->id}\n";
echo "- Descripción: {$income->description}\n";
echo "- Monto: \${$income->amount}\n";
echo "- Estado actual: {$income->payment_status}\n";
echo "- Team ID: {$income->team_id}\n\n";

// Buscar el coach
$user = User::where('email', 'arturo@flowfast.com')->first();

echo "Usuario coach:\n";
echo "- ID: {$user->id}\n";
echo "- Email: {$user->email}\n\n";

// Intentar actualizar el income
try {
    echo "Intentando actualizar el pago...\n";
    
    $income->update([
        'payment_status' => 'paid_by_team',
        'payment_method' => 'cash',
        'notes' => 'Test de pago desde script PHP',
        'paid_at' => now(),
        'paid_by_user' => $user->id,
    ]);
    
    $income->refresh();
    
    echo "✓ Pago actualizado correctamente!\n\n";
    
    echo "Estado después de la actualización:\n";
    echo "- Payment Status: {$income->payment_status}\n";
    echo "- Payment Method: {$income->payment_method}\n";
    echo "- Notes: {$income->notes}\n";
    echo "- Paid At: {$income->paid_at}\n";
    echo "- Paid By User: {$income->paid_by_user}\n";
    
} catch (\Exception $e) {
    echo "✗ Error al actualizar: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}
