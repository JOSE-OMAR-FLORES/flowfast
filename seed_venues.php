<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Venue;
use App\Models\League;

$leagues = League::all();

foreach ($leagues as $league) {
    Venue::create([
        'league_id' => $league->id,
        'name' => 'Estadio Principal ' . $league->name,
        'address' => 'Av. Principal 123',
        'city' => 'Ciudad Central',
        'capacity' => 5000,
        'rental_cost' => 500.00,
        'is_active' => true,
    ]);

    Venue::create([
        'league_id' => $league->id,
        'name' => 'Cancha Municipal ' . $league->name,
        'address' => 'Calle Deportes 456',
        'city' => 'Ciudad Central',
        'capacity' => 2000,
        'rental_cost' => 300.00,
        'is_active' => true,
    ]);
    
    echo "✓ Canchas creadas para: {$league->name}\n";
}

echo "\n¡Listo! Se crearon " . Venue::count() . " canchas en total.\n";
