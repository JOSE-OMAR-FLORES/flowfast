<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Eliminar la última temporada creada
$season = \App\Models\Season::latest()->first();

if ($season) {
    echo "Eliminando temporada: {$season->name} (ID: {$season->id})\n";
    
    // Contar y eliminar todo
    $teamsCount = \App\Models\Team::where('season_id', $season->id)->count();
    $incomesCount = \App\Models\Income::where('season_id', $season->id)->count();
    
    // Eliminar jugadores de los equipos de esta temporada
    $playersCount = 0;
    $teams = \App\Models\Team::where('season_id', $season->id)->get();
    foreach($teams as $team) {
        $count = \App\Models\Player::where('team_id', $team->id)->delete();
        $playersCount += $count;
    }
    
    // Eliminar pagos de inscripción
    \App\Models\Income::where('season_id', $season->id)->delete();
    echo "- Pagos eliminados: {$incomesCount}\n";
    
    // Eliminar equipos
    \App\Models\Team::where('season_id', $season->id)->delete();
    echo "- Equipos eliminados: {$teamsCount}\n";
    echo "- Jugadores eliminados: {$playersCount}\n";
    
    // Eliminar la temporada
    $season->delete();
    echo "- Temporada eliminada\n";
    
    echo "\n✅ Listo! Ahora puedes crear una nueva temporada de prueba.\n";
} else {
    echo "Temporada no encontrada\n";
}
