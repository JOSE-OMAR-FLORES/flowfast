<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREAR PAGOS DE PRUEBA PARA STRIPE ===\n\n";

// Obtener la última temporada
$season = \App\Models\Season::latest()->first();

if (!$season) {
    echo "❌ No hay temporadas creadas. Crea una temporada primero.\n";
    exit;
}

echo "Temporada: {$season->name}\n";
echo "Liga: {$season->league->name}\n\n";

// Obtener equipos de esta temporada
$teams = \App\Models\Team::where('season_id', $season->id)->get();

if ($teams->isEmpty()) {
    echo "❌ No hay equipos en esta temporada.\n";
    exit;
}

echo "Equipos encontrados: {$teams->count()}\n\n";

// Crear pagos de prueba para cada equipo
foreach ($teams as $team) {
    // Verificar si ya tiene un pago de inscripción
    $existingIncome = \App\Models\Income::where('team_id', $team->id)
        ->where('season_id', $season->id)
        ->where('income_type', 'registration_fee')
        ->first();
    
    if ($existingIncome) {
        echo "✓ {$team->name} - Ya tiene pago de inscripción (ID: {$existingIncome->id})\n";
        continue;
    }
    
    // Crear pago de inscripción
    $income = \App\Models\Income::create([
        'league_id' => $season->league_id,
        'season_id' => $season->id,
        'team_id' => $team->id,
        'income_type' => 'registration_fee',
        'amount' => 500.00,
        'description' => 'Cuota de inscripción - ' . $season->name,
        'due_date' => now()->addDays(15),
        'payment_status' => 'pending',
    ]);
    
    echo "✓ {$team->name} - Pago creado (ID: {$income->id}) - \${$income->amount}\n";
}

echo "\n=== RESUMEN ===\n";
$totalIncomes = \App\Models\Income::where('season_id', $season->id)
    ->where('income_type', 'registration_fee')
    ->count();
    
$totalAmount = \App\Models\Income::where('season_id', $season->id)
    ->where('income_type', 'registration_fee')
    ->sum('amount');

echo "Total de pagos: {$totalIncomes}\n";
echo "Monto total: \${$totalAmount}\n\n";

echo "✅ Listo! Ahora puedes probar los pagos en:\n";
echo "URL: http://flowfast-saas.test/payments/team\n\n";

// Mostrar un equipo de ejemplo
$firstTeam = $teams->first();
echo "Ejemplo para equipo específico:\n";
echo "URL: http://flowfast-saas.test/payments/team/{$firstTeam->id}\n";
