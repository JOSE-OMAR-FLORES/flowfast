<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Eliminar usuario admin existente
DB::table('users')->where('email', 'admin@example.com')->delete();

// Crear nuevo admin
$adminId = DB::table('admins')->insertGetId([
    'first_name' => 'Super',
    'last_name' => 'Admin',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Crear usuario asociado
DB::table('users')->insert([
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'user_type' => 'admin',
    'userable_type' => 'App\Models\Admin',
    'userable_id' => $adminId,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✅ Admin creado exitosamente!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: admin@example.com\n";
echo "Password: password\n";
echo "Admin ID: $adminId\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
