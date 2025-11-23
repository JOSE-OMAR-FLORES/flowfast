<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;

$teams = Team::where('name', 'like', 'Equipo Test%')->get();

foreach($teams as $team) {
    echo "Eliminando: {$team->name} (ID: {$team->id})\n";
    $team->incomes()->delete();
    $team->delete();
}

echo "\n✅ Equipos de prueba eliminados: " . $teams->count() . "\n";
