<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

// Eliminar usuarios admin existentes
echo "Eliminando usuarios admin existentes...\n";
$existingUser = User::where('email', 'admin@example.com')->first();
if ($existingUser && $existingUser->userable) {
    $existingUser->userable->delete();
}
User::where('email', 'admin@example.com')->delete();

// Crear nuevo admin
echo "Creando nuevo admin...\n";
$admin = Admin::create([
    'first_name' => 'Super',
    'last_name' => 'Admin',
    'phone' => '1234567890',
    'company_name' => 'FlowFast',
    'subscription_status' => 'active',
]);

// Crear usuario
$user = User::create([
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'user_type' => 'admin',
    'userable_id' => $admin->id,
    'userable_type' => Admin::class,
    'email_verified_at' => now(),
]);

echo "\n✅ Admin creado exitosamente!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: admin@example.com\n";
echo "Password: password\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
