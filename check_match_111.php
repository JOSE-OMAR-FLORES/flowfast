<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GameMatch;
use App\Models\Income;
use App\Models\Expense;
use App\Models\League;

echo "=== Verificando partido 111 ===\n\n";

$match = GameMatch::find(111);
if (!$match) {
    echo "❌ No se encontró el partido 111\n";
    exit;
}

echo "Partido: {$match->home_team->name} vs {$match->away_team->name}\n";
echo "Estado: {$match->status}\n";
echo "Liga ID: {$match->league_id}\n";
echo "Temporada ID: {$match->season_id}\n";
echo "Árbitro: " . ($match->referee ? $match->referee->full_name : 'Sin árbitro') . "\n\n";

// Verificar configuración de la liga
$league = League::find($match->league_id);
echo "=== Configuración de la Liga ===\n";
echo "Liga: {$league->name}\n";
echo "Cuota de Inscripción: \${$league->registration_fee}\n";
echo "Cuota por Partido: \${$league->match_fee}\n";
echo "Pago a Árbitro: \${$league->referee_payment}\n";
echo "Multa por Penalización: \${$league->penalty_fee}\n\n";

// Verificar ingresos generados para este partido
echo "=== Ingresos del Partido (match_id = 111) ===\n";
$incomes = Income::where('match_id', 111)->get();
echo "Total de ingresos: {$incomes->count()}\n\n";

if ($incomes->count() > 0) {
    foreach ($incomes as $income) {
        echo "- ID: {$income->id}\n";
        echo "  Equipo: " . ($income->team ? $income->team->name : 'N/A') . "\n";
        echo "  Tipo: {$income->income_type}\n";
        echo "  Monto: \${$income->amount}\n";
        echo "  Estado: {$income->payment_status}\n";
        echo "  Descripción: {$income->description}\n\n";
    }
} else {
    echo "❌ No hay ingresos registrados para este partido\n\n";
}

// Verificar egresos generados para este partido
echo "=== Egresos del Partido (match_id = 111) ===\n";
$expenses = Expense::where('match_id', 111)->get();
echo "Total de egresos: {$expenses->count()}\n\n";

if ($expenses->count() > 0) {
    foreach ($expenses as $expense) {
        echo "- ID: {$expense->id}\n";
        echo "  Árbitro: " . ($expense->referee ? $expense->referee->full_name : 'N/A') . "\n";
        echo "  Tipo: {$expense->expense_type}\n";
        echo "  Monto: \${$expense->amount}\n";
        echo "  Estado: {$expense->payment_status}\n";
        echo "  Descripción: {$expense->description}\n\n";
    }
} else {
    echo "❌ No hay egresos registrados para este partido\n\n";
}

// Verificar si la liga tiene match_fee configurado
if ($league->match_fee <= 0) {
    echo "⚠️ PROBLEMA: La liga tiene match_fee = \${$league->match_fee}\n";
    echo "   Esto explica por qué los pagos de equipos son \$0.00\n\n";
}

if ($league->referee_payment <= 0) {
    echo "⚠️ PROBLEMA: La liga tiene referee_payment = \${$league->referee_payment}\n";
    echo "   Esto explica por qué el pago al árbitro es \$0.00\n\n";
}

echo "✅ Verificación completa\n";
