<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Obtener la última temporada creada
$season = \App\Models\Season::latest()->first();

echo "=== ÚLTIMA TEMPORADA CREADA ===\n";
echo "ID: {$season->id}\n";
echo "Nombre: {$season->name}\n";
echo "Liga ID: {$season->league_id}\n";
echo "Fecha inicio: {$season->start_date}\n";
echo "Estado: {$season->status}\n\n";

// Ver equipos de esta temporada
$teams = \App\Models\Team::where('season_id', $season->id)->get();
echo "=== EQUIPOS EN ESTA TEMPORADA ===\n";
echo "Total: {$teams->count()}\n";
foreach($teams as $team) {
    $players = \App\Models\Player::where('team_id', $team->id)->count();
    echo "- {$team->name} (ID: {$team->id}) - Jugadores: {$players}\n";
}

// Ver pagos de inscripción generados
$incomes = \App\Models\Income::where('season_id', $season->id)
    ->where('income_type', 'registration_fee')
    ->get();

echo "\n=== PAGOS DE INSCRIPCIÓN GENERADOS ===\n";
echo "Total: {$incomes->count()}\n";
foreach($incomes as $income) {
    $team = \App\Models\Team::find($income->team_id);
    echo "- Equipo: {$team->name} | Monto: \${$income->amount} | Estado: {$income->payment_status}\n";
}

// Ver detalles de jugadores por equipo
echo "\n=== DETALLE DE JUGADORES POR EQUIPO ===\n";
foreach($teams as $team) {
    $players = \App\Models\Player::where('team_id', $team->id)->get();
    echo "\n{$team->name} ({$players->count()} jugadores):\n";
    foreach($players as $player) {
        echo "  - #{$player->jersey_number} {$player->first_name} {$player->last_name} - {$player->position}\n";
    }
}
