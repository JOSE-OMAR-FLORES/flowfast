<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$league = \App\Models\League::first();

echo "Liga: {$league->name}\n";
echo "Cuota de inscripción: \${$league->registration_fee}\n\n";

// Ver temporadas de esta liga
$seasons = \App\Models\Season::where('league_id', $league->id)->get();
echo "Temporadas: {$seasons->count()}\n";

foreach($seasons as $season) {
    echo "\n- Temporada: {$season->name}\n";
    $teams = \App\Models\Team::where('season_id', $season->id)->get();
    echo "  Equipos: {$teams->count()}\n";
    foreach($teams as $team) {
        echo "  * {$team->name} (ID: {$team->id})\n";
    }
}

// Ver todos los equipos únicos de la liga
echo "\n\n=== Equipos únicos de la liga ===\n";
$uniqueTeams = \App\Models\Team::whereHas('season', function($query) use ($league) {
    $query->where('league_id', $league->id);
})->distinct()->get();

echo "Total: {$uniqueTeams->count()}\n";
foreach($uniqueTeams as $team) {
    echo "- {$team->name} (ID: {$team->id})\n";
}
