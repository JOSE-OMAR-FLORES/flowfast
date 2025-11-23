<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('user_type', 'admin')->first();
echo "Admin ID: " . ($user ? $user->id : 'No encontrado') . PHP_EOL;
