<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Crear nuevo admin con email diferente
$adminId = DB::table('admins')->insertGetId([
    'first_name' => 'Test',
    'last_name' => 'Admin',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Crear usuario asociado
DB::table('users')->insert([
    'email' => 'test@admin.com',
    'password' => Hash::make('123456'),
    'user_type' => 'admin',
    'userable_type' => 'App\Models\Admin',
    'userable_id' => $adminId,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✅ Nuevo admin creado!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: test@admin.com\n";
echo "Password: 123456\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
