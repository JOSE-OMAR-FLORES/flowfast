<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use App\Models\Coach;

echo "=== Corrigiendo relación Coach-Team ===\n\n";

$coach = Coach::find(2);
if (!$coach) {
    echo "No se encontró el coach con ID 2\n";
    exit;
}

echo "Coach ID: {$coach->id}\n";
echo "Team ID en coach: {$coach->team_id}\n\n";

if ($coach->team_id) {
    $team = Team::find($coach->team_id);
    if ($team) {
        echo "Equipo encontrado: {$team->name} (ID: {$team->id})\n";
        echo "Coach ID actual del equipo: " . ($team->coach_id ?? 'NULL') . "\n\n";
        
        if ($team->coach_id !== $coach->id) {
            echo "Actualizando coach_id del equipo...\n";
            $team->update(['coach_id' => $coach->id]);
            echo "✓ Equipo actualizado correctamente\n\n";
            
            $team->refresh();
            echo "Verificación:\n";
            echo "- Team ID: {$team->id}\n";
            echo "- Team Name: {$team->name}\n";
            echo "- Coach ID: {$team->coach_id}\n";
        } else {
            echo "La relación ya está correcta.\n";
        }
    } else {
        echo "No se encontró el equipo con ID {$coach->team_id}\n";
    }
} else {
    echo "El coach no tiene un team_id asignado.\n";
}
