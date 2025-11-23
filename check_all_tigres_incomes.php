<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use App\Models\Income;
use App\Models\Player;

echo "=== Verificando TODOS los pagos de Los Tigres FC ===\n\n";

$team = Team::where('name', 'like', '%Tigres%')->first();

if (!$team) {
    echo "❌ Equipo 'Los Tigres FC' no encontrado\n";
    exit;
}

echo "✓ Equipo encontrado:\n";
echo "  - ID: {$team->id}\n";
echo "  - Nombre: {$team->name}\n";
echo "  - Coach ID: {$team->coach_id}\n";
echo "  - Season ID: {$team->season_id}\n\n";

echo "=== TODOS los Incomes del sistema para Los Tigres FC ===\n";

// Buscar por team_id
$incomesByTeam = Income::where('team_id', $team->id)->get();
echo "Ingresos por team_id: {$incomesByTeam->count()}\n";

// Buscar por player_id (jugadores del equipo)
$playerIds = Player::where('team_id', $team->id)->pluck('id');
echo "Jugadores en el equipo: {$playerIds->count()}\n";

$incomesByPlayer = Income::whereIn('player_id', $playerIds)->get();
echo "Ingresos por player_id: {$incomesByPlayer->count()}\n";

// Combinar todos
$allIncomes = Income::where('team_id', $team->id)
    ->orWhereIn('player_id', $playerIds)
    ->get();

echo "\nTotal de ingresos relacionados: {$allIncomes->count()}\n\n";

if ($allIncomes->count() > 0) {
    echo "=== Detalle de cada ingreso ===\n";
    foreach ($allIncomes as $income) {
        echo "\nIncome ID: {$income->id}\n";
        echo "  - Concepto: {$income->concept}\n";
        echo "  - Tipo: {$income->income_type}\n";
        echo "  - Monto: \${$income->amount}\n";
        echo "  - Estado: {$income->payment_status}\n";
        echo "  - Team ID: {$income->team_id}\n";
        echo "  - Player ID: {$income->player_id}\n";
        echo "  - Season ID: {$income->season_id}\n";
        echo "  - Fecha vencimiento: " . ($income->due_date ? $income->due_date->format('Y-m-d') : 'N/A') . "\n";
        
        if ($income->player_id) {
            $player = Player::find($income->player_id);
            if ($player) {
                echo "  - Jugador: {$player->first_name} {$player->last_name}\n";
                echo "  - Team del jugador: {$player->team_id}\n";
            }
        }
        echo "  ---\n";
    }
    
    echo "\n=== Resumen por estado ===\n";
    $pending = $allIncomes->where('payment_status', 'pending');
    $confirmed = $allIncomes->where('payment_status', 'confirmed');
    $overdue = $allIncomes->where('payment_status', 'overdue');
    $paid = $allIncomes->whereIn('payment_status', ['paid_by_team', 'confirmed_by_admin']);
    
    echo "Pendientes: {$pending->count()} - Total: \$" . $pending->sum('amount') . "\n";
    echo "Confirmados: {$confirmed->count()} - Total: \$" . $confirmed->sum('amount') . "\n";
    echo "Vencidos: {$overdue->count()} - Total: \$" . $overdue->sum('amount') . "\n";
    echo "Pagados: {$paid->count()} - Total: \$" . $paid->sum('amount') . "\n";
    
    echo "\n=== Lo que debería ver el coach ===\n";
    $coachShouldSee = Income::where('team_id', $team->id)
        ->where('payment_status', 'pending')
        ->sum('amount');
    
    echo "Suma de pagos pendientes con team_id={$team->id}: \${$coachShouldSee}\n";
} else {
    echo "❌ No hay ingresos registrados para este equipo.\n";
}

echo "\n=== Verificando coach y user ===\n";
if ($team->coach_id) {
    $coach = \App\Models\Coach::find($team->coach_id);
    if ($coach) {
        echo "Coach ID: {$coach->id}\n";
        echo "User ID: {$coach->user_id}\n";
        
        if ($coach->user_id) {
            $user = \App\Models\User::find($coach->user_id);
            if ($user) {
                echo "Usuario: {$user->name} ({$user->email})\n";
                echo "Role: {$user->role}\n";
            }
        }
    }
}
