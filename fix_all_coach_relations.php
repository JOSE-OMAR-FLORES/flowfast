<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use App\Models\Coach;

echo "=== Corrigiendo relaciones de TODOS los coaches ===\n\n";

$coaches = Coach::all();

foreach ($coaches as $coach) {
    echo "Coach ID: {$coach->id}\n";
    if ($coach->user) {
        echo "Usuario: {$coach->user->email}\n";
    }
    echo "Team ID en coach: " . ($coach->team_id ?? 'NULL') . "\n";
    
    if ($coach->team_id) {
        $team = Team::find($coach->team_id);
        if ($team) {
            echo "Equipo: {$team->name} (ID: {$team->id})\n";
            echo "Coach ID actual del equipo: " . ($team->coach_id ?? 'NULL') . "\n";
            
            if ($team->coach_id !== $coach->id) {
                echo "→ Actualizando coach_id del equipo...\n";
                $team->update(['coach_id' => $coach->id]);
                echo "✓ Actualizado\n";
            } else {
                echo "✓ Ya está correcto\n";
            }
        }
    }
    echo "\n";
}

echo "\n=== Verificación final ===\n";
$teams = Team::whereNotNull('coach_id')->get();
echo "Equipos con coach asignado: {$teams->count()}\n\n";
foreach ($teams as $team) {
    echo "- {$team->name} → Coach ID: {$team->coach_id}\n";
}
