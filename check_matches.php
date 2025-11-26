<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$teamId = 34; // Lobos

echo "=== FIXTURES (modelo Fixture) ===" . PHP_EOL;
$fixtures = App\Models\Fixture::with(['homeTeam', 'awayTeam'])
    ->where(function($q) use ($teamId) {
        $q->where('home_team_id', $teamId)->orWhere('away_team_id', $teamId);
    })
    ->get();

foreach ($fixtures as $f) {
    echo "ID: " . $f->id . " | Round: " . ($f->round_number ?? 'NULL') . " | Status: " . $f->status . " | " . $f->homeTeam->name . " vs " . $f->awayTeam->name . PHP_EOL;
}

echo PHP_EOL . "=== GAME MATCHES (modelo GameMatch) ===" . PHP_EOL;
$matches = App\Models\GameMatch::with(['homeTeam', 'awayTeam', 'round'])
    ->where(function($q) use ($teamId) {
        $q->where('home_team_id', $teamId)->orWhere('away_team_id', $teamId);
    })
    ->orderBy('scheduled_at')
    ->get();

foreach ($matches as $m) {
    echo "ID: " . $m->id . " | Round: " . ($m->round_id ?? 'NULL') . " | Status: " . $m->status . " | " . $m->scheduled_at . " | " . $m->homeTeam->name . " vs " . $m->awayTeam->name . PHP_EOL;
}

echo PHP_EOL . "Tablas existentes:" . PHP_EOL;
$tables = Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE '%match%'");
foreach ($tables as $t) {
    $arr = (array) $t;
    echo "  - " . reset($arr) . PHP_EOL;
}
$tables = Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE '%fixture%'");
foreach ($tables as $t) {
    $arr = (array) $t;
    echo "  - " . reset($arr) . PHP_EOL;
}
