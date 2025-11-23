<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use App\Models\Income;

echo "=== Verificando pagos de Los Tigres FC ===\n\n";

$team = Team::where('name', 'Los Tigres FC')->first();

if (!$team) {
    echo "❌ Equipo 'Los Tigres FC' no encontrado\n";
    exit;
}

echo "✓ Equipo encontrado:\n";
echo "  - ID: {$team->id}\n";
echo "  - Nombre: {$team->name}\n";
echo "  - Coach ID: {$team->coach_id}\n\n";

echo "=== Verificando Incomes (Ingresos) ===\n";
$incomes = Income::where('team_id', $team->id)->get();
echo "Total de ingresos: {$incomes->count()}\n\n";

if ($incomes->count() > 0) {
    foreach ($incomes as $income) {
        echo "Income ID: {$income->id}\n";
        echo "  - Concepto: {$income->concept}\n";
        echo "  - Monto: \${$income->amount}\n";
        echo "  - Estado: {$income->payment_status}\n";
        echo "  - Fecha vencimiento: " . ($income->due_date ? $income->due_date->format('Y-m-d') : 'N/A') . "\n";
        echo "---\n";
    }
    
    echo "\n=== Resumen por estado ===\n";
    $pending = $incomes->where('payment_status', 'pending');
    $confirmed = $incomes->where('payment_status', 'confirmed');
    $overdue = $incomes->where('payment_status', 'overdue');
    
    echo "Pendientes: {$pending->count()} - Total: \$" . $pending->sum('amount') . "\n";
    echo "Confirmados: {$confirmed->count()} - Total: \$" . $confirmed->sum('amount') . "\n";
    echo "Vencidos: {$overdue->count()} - Total: \$" . $overdue->sum('amount') . "\n";
} else {
    echo "No hay ingresos registrados para este equipo.\n";
}

echo "\n=== Verificando acceso del coach ===\n";
if ($team->coach_id) {
    $coach = \App\Models\Coach::find($team->coach_id);
    if ($coach) {
        echo "Coach encontrado: ID {$coach->id}\n";
        echo "User ID: {$coach->user_id}\n";
        
        $user = $coach->user;
        if ($user) {
            echo "Usuario: {$user->name} ({$user->email})\n";
        }
        
        // Verificar qué equipos tiene asignados este coach
        $coachTeams = Team::where('coach_id', $coach->id)->get();
        echo "\nEquipos del coach:\n";
        foreach ($coachTeams as $t) {
            echo "  - {$t->name} (ID: {$t->id})\n";
        }
        
        // Verificar pagos que debería ver
        $teamIds = $coachTeams->pluck('id');
        $pendingForCoach = Income::whereIn('team_id', $teamIds)
            ->where('payment_status', 'pending')
            ->get();
        
        echo "\nPagos pendientes que debería ver el coach:\n";
        echo "Total: {$pendingForCoach->count()}\n";
        echo "Suma: \$" . $pendingForCoach->sum('amount') . "\n";
    } else {
        echo "❌ Coach no encontrado con ID {$team->coach_id}\n";
    }
} else {
    echo "⚠️ Este equipo no tiene coach asignado\n";
}
