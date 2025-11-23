<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\League;
use App\Models\Fixture;

echo "=== Verificando ligas con fixtures ===\n\n";

$allLeagues = League::all();
echo "Total de ligas: {$allLeagues->count()}\n\n";

foreach ($allLeagues as $league) {
    $fixturesCount = Fixture::whereHas('season', function($q) use ($league) {
        $q->where('league_id', $league->id);
    })->count();
    
    echo "Liga: {$league->name} (ID: {$league->id})\n";
    echo "  - Fixtures: {$fixturesCount}\n";
    
    if ($fixturesCount > 0) {
        echo "  ✓ Tiene fixtures\n";
    } else {
        echo "  ✗ NO tiene fixtures\n";
    }
    echo "\n";
}

echo "\n=== Ligas que DEBEN aparecer en /admin/fixtures ===\n\n";

$leaguesWithFixtures = League::whereHas('seasons.fixtures')->get();
echo "Total: {$leaguesWithFixtures->count()}\n\n";

foreach ($leaguesWithFixtures as $league) {
    echo "- {$league->name} (ID: {$league->id})\n";
}
