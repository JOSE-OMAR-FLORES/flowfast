<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\Income;

echo "=== TEST: Generación Automática de Pagos de Inscripción ===\n\n";

// Buscar una liga con cuota de inscripción configurada
$league = League::where('registration_fee', '>', 0)->first();

if (!$league) {
    echo "⚠️ No hay ligas con cuota de inscripción configurada.\n";
    echo "Configurando cuota de \$500 en la primera liga...\n";
    $league = League::first();
    $league->registration_fee = 500.00;
    $league->save();
}

echo "Liga seleccionada: {$league->name}\n";
echo "Cuota de inscripción: \${$league->registration_fee}\n\n";

// Buscar una temporada de esa liga
$season = Season::where('league_id', $league->id)->first();

if (!$season) {
    echo "❌ No hay temporadas en esta liga.\n";
    exit;
}

echo "Temporada: {$season->name}\n\n";

// Crear un equipo de prueba
echo "Creando equipo de prueba...\n";
$teamName = "Equipo Test " . time();

$team = Team::create([
    'season_id' => $season->id,
    'name' => $teamName,
    'slug' => \Illuminate\Support\Str::slug($teamName),
    'primary_color' => '#FF0000',
    'secondary_color' => '#FFFFFF',
    'registration_paid' => false,
]);

echo "✅ Equipo creado: {$team->name} (ID: {$team->id})\n\n";

// Simular la generación del pago de inscripción
echo "Generando pago de inscripción...\n";

$income = Income::create([
    'league_id' => $league->id,
    'season_id' => $season->id,
    'team_id' => $team->id,
    'income_type' => 'registration_fee',
    'amount' => $league->registration_fee,
    'description' => 'Cuota de inscripción - ' . $season->name,
    'due_date' => now()->addDays(15),
    'payment_status' => 'pending',
    'generated_by' => \App\Models\User::where('user_type', 'admin')->first()->id ?? null,
]);

echo "✅ Pago generado:\n";
echo "   - ID: {$income->id}\n";
echo "   - Monto: \${$income->amount}\n";
echo "   - Estado: {$income->payment_status}\n";
echo "   - Vencimiento: {$income->due_date->format('Y-m-d')}\n\n";

// Verificar el pago
echo "=== VERIFICACIÓN ===\n";
$registrationPayments = Income::where('team_id', $team->id)
    ->where('income_type', 'registration_fee')
    ->get();

echo "Pagos de inscripción para '{$team->name}': {$registrationPayments->count()}\n\n";

foreach ($registrationPayments as $payment) {
    echo "- ID {$payment->id}: \${$payment->amount} - {$payment->payment_status}\n";
}

echo "\n✅ ¡Test completado!\n\n";
echo "📌 Ahora prueba crear un equipo desde:\n";
echo "   http://flowfast-saas.test/admin/teams/create\n\n";
echo "📌 Verifica los pagos en:\n";
echo "   http://flowfast-saas.test/admin/incomes\n";
