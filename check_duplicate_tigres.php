<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use App\Models\Income;
use App\Models\Coach;

echo "=== Equipos llamados 'Los Tigres FC' ===\n\n";

$tigresTeams = Team::where('name', 'like', '%Tigres%')->get();

foreach ($tigresTeams as $team) {
    echo "Team ID: {$team->id}\n";
    echo "  Nombre: {$team->name}\n";
    echo "  Coach ID: {$team->coach_id}\n";
    echo "  Season ID: {$team->season_id}\n";
    
    if ($team->coach_id) {
        $coach = Coach::find($team->coach_id);
        if ($coach && $coach->user) {
            echo "  Coach: {$coach->user->name} ({$coach->user->email})\n";
        }
    }
    
    if ($team->season) {
        echo "  Season: {$team->season->name}\n";
        if ($team->season->league) {
            echo "  League: {$team->season->league->name}\n";
        }
    }
    
    $incomes = Income::where('team_id', $team->id)->get();
    echo "  Ingresos: {$incomes->count()}\n";
    
    if ($incomes->count() > 0) {
        $pending = $incomes->where('payment_status', 'pending');
        echo "  Pendientes: {$pending->count()} - Total: \$" . $pending->sum('amount') . "\n";
        
        echo "  Detalle:\n";
        foreach ($incomes as $income) {
            echo "    - ID {$income->id}: \${$income->amount} - {$income->payment_status} - {$income->concept}\n";
        }
    }
    
    echo "\n" . str_repeat('-', 60) . "\n\n";
}

echo "\n=== SOLUCIÓN ===\n";
echo "Hay dos equipos con nombre similar. Las opciones son:\n\n";
echo "1. Transferir los pagos del Team ID 32 al Team ID 15\n";
echo "2. Cambiar el coach_id del Team ID 32 para que coincida con el coach correcto\n";
echo "3. Eliminar uno de los equipos duplicados\n";
