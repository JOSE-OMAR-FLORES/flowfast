<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

$user = User::where('email', 'admin@example.com')->first();

if (!$user) {
    echo "❌ Usuario no encontrado\n";
    exit(1);
}

echo "Usuario encontrado:\n";
echo "- ID: {$user->id}\n";
echo "- Email: {$user->email}\n";
echo "- User Type: {$user->user_type}\n";
echo "- Password Hash: " . substr($user->password, 0, 20) . "...\n\n";

$password = 'password';
$check = Hash::check($password, $user->password);

echo "Verificación de contraseña:\n";
echo "- Password ingresado: '{$password}'\n";
echo "- Hash::check(): " . ($check ? '✅ CORRECTO' : '❌ INCORRECTO') . "\n\n";

// Probar con diferentes passwords
$tests = ['password', 'Password', 'PASSWORD', 'admin', '12345678'];
echo "Probando diferentes passwords:\n";
foreach ($tests as $test) {
    $result = Hash::check($test, $user->password);
    echo "- '{$test}': " . ($result ? '✅' : '❌') . "\n";
}
