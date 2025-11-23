<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\League;
use App\Models\User;

echo "=== Ligas Activas ===\n";
$leagues = League::where('status', 'active')->get(['id', 'name', 'admin_id']);
foreach ($leagues as $league) {
    echo "ID: {$league->id} | Nombre: {$league->name} | Admin ID: {$league->admin_id}\n";
}

echo "\n=== Usuario 7 ===\n";
$user = User::find(7);
if ($user) {
    echo "assigned_leagues: {$user->assigned_leagues}\n";
    echo "userable_id: {$user->userable_id}\n";
}
