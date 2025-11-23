<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->boot();

$sports = App\Models\Sport::select('id', 'name', 'slug')->get();

echo "Deportes disponibles:\n";
foreach($sports as $sport) {
    echo "ID: {$sport->id} - {$sport->name} ({$sport->slug})\n";
}