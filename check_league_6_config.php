<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\League;

$league = League::find(6);

echo "=== Configuración de la Liga 6 ===\n\n";
echo "Liga: {$league->name}\n";
echo "Cuota de Inscripción: \${$league->registration_fee}\n";
echo "Cuota por Partido (match_fee): \${$league->match_fee}\n";
echo "Pago a Árbitro (referee_payment): \${$league->referee_payment}\n";
echo "Multa por Penalización (penalty_fee): \${$league->penalty_fee}\n\n";

if ($league->match_fee == 0) {
    echo "❌ PROBLEMA ENCONTRADO: match_fee = \$0\n";
    echo "   Esto explica por qué los pagos de equipos son \$0.00\n\n";
    echo "📝 SOLUCIÓN: En la edición de la liga, cambia:\n";
    echo "   - 'Cuota por Partido (por equipo)' de \$0.00 a \$250.00\n\n";
}

if ($league->referee_payment == 0) {
    echo "❌ PROBLEMA ENCONTRADO: referee_payment = \$0\n";
    echo "   Esto explica por qué el pago al árbitro es \$0.00\n\n";
    echo "📝 SOLUCIÓN: En la edición de la liga, cambia:\n";
    echo "   - 'Pago a Árbitros' de \$0.00 a \$160.00\n\n";
}

if ($league->match_fee > 0 && $league->referee_payment > 0) {
    echo "✅ La configuración está correcta\n";
    echo "   Los pagos deberían generarse automáticamente al finalizar partidos\n";
}
