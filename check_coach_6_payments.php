<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coach;
use App\Models\Income;
use App\Models\Team;

echo "=== Verificando pagos del coach ID 6 ===\n\n";

$coach = Coach::find(6);
if (!$coach) {
    echo "Coach no encontrado\n";
    exit;
}

echo "Coach ID: {$coach->id}\n";
echo "Full Name: {$coach->full_name}\n";
echo "Team ID: {$coach->team_id}\n";

if ($coach->user) {
    echo "Email: {$coach->user->email}\n";
}

echo "\n=== Equipo del Coach ===\n";
$team = Team::find($coach->team_id);
if ($team) {
    echo "Team ID: {$team->id}\n";
    echo "Team Name: {$team->name}\n";
    echo "Season ID: {$team->season_id}\n";
    echo "League ID: " . ($team->season ? $team->season->league_id : 'NULL') . "\n";
} else {
    echo "No se encontró el equipo\n";
    exit;
}

echo "\n=== Pagos (Incomes) del equipo ===\n";
$incomes = Income::where('team_id', $coach->team_id)->get();
echo "Total de pagos encontrados: {$incomes->count()}\n\n";

if ($incomes->count() > 0) {
    foreach ($incomes as $income) {
        echo "- ID: {$income->id}\n";
        echo "  Tipo: {$income->income_type}\n";
        echo "  Descripción: {$income->description}\n";
        echo "  Monto: \${$income->amount}\n";
        echo "  Estado: {$income->payment_status}\n";
        echo "  Due Date: {$income->due_date}\n";
        echo "\n";
    }
} else {
    echo "❌ No hay pagos registrados para este equipo\n\n";
    
    echo "=== Verificando si hay pagos de inscripción en la liga ===\n";
    if ($team->season) {
        $allIncomes = Income::where('season_id', $team->season_id)->get();
        echo "Total de pagos en la temporada: {$allIncomes->count()}\n";
    }
}
