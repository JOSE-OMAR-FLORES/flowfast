<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Income;
use App\Models\Team;

echo "=== Verificando pagos del coach ===\n\n";

// Buscar el coach con email arturo@flowfast.com
$user = User::where('email', 'arturo@flowfast.com')->first();

if (!$user) {
    echo "No se encontró el usuario\n";
    exit;
}

echo "Usuario: {$user->email}\n";
echo "User Type: {$user->user_type}\n";
echo "Userable Type: {$user->userable_type}\n";
echo "Userable ID: {$user->userable_id}\n\n";

if ($user->userable) {
    $coach = $user->userable;
    echo "Coach ID: {$coach->id}\n";
    echo "Coach Name: {$coach->full_name}\n";
    echo "Team ID: " . ($coach->team_id ?? 'NULL') . "\n\n";
    
    if ($coach->team_id) {
        $team = Team::find($coach->team_id);
        if ($team) {
            echo "=== Equipo ===\n";
            echo "ID: {$team->id}\n";
            echo "Nombre: {$team->name}\n";
            echo "Liga ID: " . ($team->league_id ?? 'NULL') . "\n";
            echo "Temporada ID: " . ($team->season_id ?? 'NULL') . "\n\n";
            
            echo "=== Pagos del equipo (Income) ===\n";
            $incomes = Income::where('team_id', $team->id)
                ->with(['league', 'season'])
                ->get();
            
            echo "Total de pagos encontrados: {$incomes->count()}\n\n";
            
            if ($incomes->count() > 0) {
                foreach ($incomes as $income) {
                    echo "- ID: {$income->id}\n";
                    echo "  Concepto: {$income->description}\n";
                    echo "  Monto: \${$income->amount}\n";
                    echo "  Estado: {$income->payment_status}\n";
                    echo "  Fecha vencimiento: {$income->due_date}\n";
                    if ($income->league) {
                        echo "  Liga: {$income->league->name}\n";
                    }
                    if ($income->season) {
                        echo "  Temporada: {$income->season->name}\n";
                    }
                    echo "\n";
                }
            } else {
                echo "No hay pagos registrados para este equipo.\n";
            }
        }
    }
}
