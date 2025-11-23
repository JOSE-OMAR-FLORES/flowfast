<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GameMatch;
use App\Models\Income;
use App\Models\Expense;
use App\Models\League;

echo "=== Buscando últimos partidos ===\n\n";

$matches = GameMatch::orderBy('id', 'desc')->take(5)->get();

foreach ($matches as $match) {
    echo "ID: {$match->id} | {$match->home_team->name} vs {$match->away_team->name} | Estado: {$match->status}\n";
}

echo "\n=== Verificar último partido ===\n";
$lastMatch = $matches->first();

if ($lastMatch) {
    echo "\nÚltimo partido ID: {$lastMatch->id}\n";
    echo "Partido: {$lastMatch->home_team->name} vs {$lastMatch->away_team->name}\n";
    echo "Estado: {$lastMatch->status}\n";
    echo "Liga ID: {$lastMatch->league_id}\n";
    
    $league = League::find($lastMatch->league_id);
    echo "\n=== Configuración de la Liga ===\n";
    echo "Liga: {$league->name}\n";
    echo "Cuota por Partido: \${$league->match_fee}\n";
    echo "Pago a Árbitro: \${$league->referee_payment}\n\n";
    
    echo "=== Ingresos del Partido ===\n";
    $incomes = Income::where('match_id', $lastMatch->id)->get();
    echo "Total: {$incomes->count()}\n";
    foreach ($incomes as $income) {
        echo "- {$income->team->name}: \${$income->amount} ({$income->payment_status})\n";
    }
    
    echo "\n=== Egresos del Partido ===\n";
    $expenses = Expense::where('match_id', $lastMatch->id)->get();
    echo "Total: {$expenses->count()}\n";
    foreach ($expenses as $expense) {
        echo "- Árbitro: \${$expense->amount} ({$expense->payment_status})\n";
    }
}
