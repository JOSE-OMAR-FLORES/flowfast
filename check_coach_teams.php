<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use App\Models\Coach;

echo "=== Verificando equipos del coach ID 2 ===\n\n";

$coach = Coach::find(2);
if ($coach) {
    echo "Coach encontrado: ID {$coach->id}\n";
    if ($coach->user) {
        echo "Usuario asociado: {$coach->user->name} ({$coach->user->email})\n";
    }
} else {
    echo "No se encontró el coach con ID 2\n";
}

echo "\n=== Equipos con coach_id = 2 ===\n";
$teams = Team::where('coach_id', 2)->get();
echo "Total de equipos encontrados: {$teams->count()}\n\n";

if ($teams->count() > 0) {
    foreach ($teams as $team) {
        echo "- ID: {$team->id}, Nombre: {$team->name}, Coach ID: {$team->coach_id}\n";
    }
} else {
    echo "No hay equipos asignados a este coach.\n";
}

echo "\n=== TODOS los equipos en la base de datos ===\n";
$allTeams = Team::all();
echo "Total de equipos: {$allTeams->count()}\n\n";

foreach ($allTeams as $team) {
    echo "- ID: {$team->id}, Nombre: {$team->name}, Coach ID: " . ($team->coach_id ?? 'NULL') . "\n";
}

echo "\n=== Coaches en la base de datos ===\n";
$coaches = Coach::all();
echo "Total de coaches: {$coaches->count()}\n\n";

foreach ($coaches as $c) {
    echo "- Coach ID: {$c->id}, Team ID: " . ($c->team_id ?? 'NULL') . "\n";
    if ($c->user) {
        echo "  Usuario: {$c->user->name} ({$c->user->email})\n";
    }
}
