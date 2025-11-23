<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Income;
use App\Models\Team;

echo "=== Verificando TODOS los Incomes en el sistema ===\n\n";

$totalIncomes = Income::count();
echo "Total de ingresos en el sistema: {$totalIncomes}\n\n";

if ($totalIncomes > 0) {
    echo "=== Primeros 10 ingresos ===\n";
    $incomes = Income::with('team')->take(10)->get();
    
    foreach ($incomes as $income) {
        echo "\nID: {$income->id}\n";
        echo "  Team ID: {$income->team_id}\n";
        echo "  Team Name: " . ($income->team ? $income->team->name : 'N/A') . "\n";
        echo "  Concepto: {$income->concept}\n";
        echo "  Monto: \${$income->amount}\n";
        echo "  Estado: {$income->payment_status}\n";
        echo "  ---\n";
    }
    
    echo "\n=== Buscando específicamente Los Tigres FC ===\n";
    $tigres = Team::where('name', 'like', '%Tigres%')->first();
    if ($tigres) {
        echo "Tigres Team ID: {$tigres->id}\n";
        $tigresIncomes = Income::where('team_id', $tigres->id)->get();
        echo "Ingresos de Los Tigres: {$tigresIncomes->count()}\n";
        
        foreach ($tigresIncomes as $income) {
            echo "\n  ID: {$income->id} - {$income->concept} - \${$income->amount} - {$income->payment_status}\n";
        }
    }
    
    echo "\n=== Resumen por estado ===\n";
    $pending = Income::where('payment_status', 'pending')->count();
    $confirmed = Income::where('payment_status', 'confirmed')->count();
    $overdue = Income::where('payment_status', 'overdue')->count();
    
    echo "Pendientes: {$pending}\n";
    echo "Confirmados: {$confirmed}\n";
    echo "Vencidos: {$overdue}\n";
    
    echo "\n=== Ingresos pendientes por equipo ===\n";
    $pendingByTeam = Income::where('payment_status', 'pending')
        ->with('team')
        ->get()
        ->groupBy('team_id');
    
    foreach ($pendingByTeam as $teamId => $teamIncomes) {
        $team = $teamIncomes->first()->team;
        $teamName = $team ? $team->name : "Team ID: {$teamId}";
        $total = $teamIncomes->sum('amount');
        echo "  {$teamName}: {$teamIncomes->count()} pagos - Total: \${$total}\n";
    }
} else {
    echo "❌ No hay ingresos en el sistema.\n";
}
