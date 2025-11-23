<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->boot();

use App\Models\Fixture;

echo "Fixtures disponibles:\n";

$fixtures = Fixture::select('id', 'home_team_id', 'away_team_id', 'status', 'match_date')
    ->with(['homeTeam:id,name', 'awayTeam:id,name'])
    ->get();

if ($fixtures->isEmpty()) {
    echo "No hay fixtures creados. Primero debes generar fixtures desde el admin.\n";
} else {
    foreach ($fixtures as $fixture) {
        echo "ID: {$fixture->id} - {$fixture->homeTeam->name} vs {$fixture->awayTeam->name} - Status: {$fixture->status} - Fecha: {$fixture->match_date}\n";
    }
}