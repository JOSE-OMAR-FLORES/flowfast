<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\League;
use App\Models\Season;
use App\Models\Team;

echo "\n=== ANÁLISIS DE LIGAS, TEMPORADAS Y EQUIPOS ===\n\n";

$leagues = League::with(['seasons.teams'])->get();

foreach($leagues as $league) {
    echo "🏆 LIGA: {$league->name} (ID: {$league->id})\n";
    echo str_repeat('-', 70) . "\n";
    
    $seasons = $league->seasons;
    
    if($seasons->isEmpty()) {
        echo "   ⚠️  No tiene temporadas\n\n";
        continue;
    }
    
    foreach($seasons as $season) {
        echo "   📅 Temporada: {$season->name} (ID: {$season->id})\n";
        $teams = $season->teams;
        echo "      Equipos en esta temporada: {$teams->count()}\n";
        
        if($teams->count() > 0) {
            foreach($teams as $team) {
                echo "         • {$team->name} (ID: {$team->id}, season_id: {$team->season_id})\n";
            }
        } else {
            echo "         ⚠️  Sin equipos\n";
        }
        echo "\n";
    }
    echo "\n";
}

echo "\n=== RESUMEN FIXTURES GENERADOS ===\n\n";

$fixturesCount = \App\Models\Fixture::count();
echo "Total de fixtures en la base de datos: {$fixturesCount}\n\n";

if($fixturesCount > 0) {
    $fixtures = \App\Models\Fixture::with(['season.league', 'homeTeam', 'awayTeam'])
        ->orderBy('season_id')
        ->orderBy('round_number')
        ->get();
    
    $fixturesBySeason = $fixtures->groupBy('season_id');
    
    foreach($fixturesBySeason as $seasonId => $seasonFixtures) {
        $season = $seasonFixtures->first()->season;
        $league = $season->league;
        
        echo "Liga: {$league->name} → Temporada: {$season->name}\n";
        echo "   Fixtures: {$seasonFixtures->count()}\n";
        echo "   Jornadas: " . $seasonFixtures->pluck('round_number')->unique()->count() . "\n";
        
        $teamIds = $seasonFixtures->pluck('home_team_id')
            ->merge($seasonFixtures->pluck('away_team_id'))
            ->unique();
        
        echo "   Equipos participantes: {$teamIds->count()}\n";
        
        foreach($teamIds as $teamId) {
            $team = Team::find($teamId);
            if($team) {
                echo "      • {$team->name} (season_id: {$team->season_id})\n";
            }
        }
        echo "\n";
    }
}

echo "\n✅ Análisis completado\n\n";
