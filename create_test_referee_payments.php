<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\League;
use App\Models\Referee;
use App\Models\Fixture;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

echo "=== SCRIPT DE PRUEBA: GENERAR PAGOS A ÁRBITROS ===\n\n";

try {
    DB::beginTransaction();

    // 1. Buscar una liga activa
    $league = League::where('status', 'active')->first();
    
    if (!$league) {
        echo "❌ No se encontró ninguna liga activa.\n";
        echo "   Por favor, crea una liga primero.\n";
        exit(1);
    }
    
    echo "✅ Liga encontrada: {$league->name} (ID: {$league->id})\n\n";

    // 2. Buscar o crear árbitros
    $referees = Referee::where('league_id', $league->id)->get();
    
    if ($referees->isEmpty()) {
        echo "⚠️  No hay árbitros en esta liga. Creando árbitros de prueba...\n";
        
        $refereeData = [
            ['Juan', 'Pérez', 'juan.perez@email.com', '5551234567'],
            ['María', 'González', 'maria.gonzalez@email.com', '5557654321'],
            ['Carlos', 'Ramírez', 'carlos.ramirez@email.com', '5559876543'],
        ];
        
        foreach ($refereeData as $data) {
            Referee::create([
                'league_id' => $league->id,
                'first_name' => $data[0],
                'last_name' => $data[1],
                'email' => $data[2],
                'phone' => $data[3],
                'license_number' => 'ARB-' . rand(1000, 9999),
                'experience_level' => 'intermediate',
                'status' => 'active',
            ]);
            echo "   ✓ Árbitro creado: {$data[0]} {$data[1]}\n";
        }
        
        $referees = Referee::where('league_id', $league->id)->get();
        echo "\n";
    } else {
        echo "✅ Se encontraron {$referees->count()} árbitros\n\n";
    }

    // 3. Buscar fixtures (partidos)
    $fixtures = Fixture::whereHas('season', function($q) use ($league) {
        $q->where('league_id', $league->id);
    })->take(5)->get();

    if ($fixtures->isEmpty()) {
        echo "⚠️  No hay fixtures/partidos creados.\n";
        echo "   Se crearán pagos sin fixture asociado.\n\n";
    } else {
        echo "✅ Se encontraron {$fixtures->count()} fixtures\n\n";
    }

    // 4. Crear diferentes tipos de pagos a árbitros
    echo "=== CREANDO PAGOS DE PRUEBA ===\n\n";

    $paymentTypes = [
        [
            'type' => 'referee_payment',
            'description' => 'Pago por arbitraje - Partido de temporada regular',
            'amount' => 500.00,
            'status' => 'pending',
        ],
        [
            'type' => 'referee_payment',
            'description' => 'Pago por arbitraje - Partido de semifinal',
            'amount' => 750.00,
            'status' => 'approved',
        ],
        [
            'type' => 'referee_bonus',
            'description' => 'Bono por buen desempeño',
            'amount' => 200.00,
            'status' => 'ready_for_payment',
        ],
        [
            'type' => 'referee_travel',
            'description' => 'Viáticos - Desplazamiento a sede externa',
            'amount' => 300.00,
            'status' => 'pending',
        ],
        [
            'type' => 'referee_payment',
            'description' => 'Pago por arbitraje - Final de torneo',
            'amount' => 1000.00,
            'status' => 'confirmed',
        ],
    ];

    $createdPayments = 0;

    foreach ($paymentTypes as $index => $paymentData) {
        $referee = $referees->random();
        $fixture = $fixtures->isNotEmpty() ? $fixtures->random() : null;
        
        $expense = Expense::create([
            'league_id' => $league->id,
            'referee_id' => $referee->id,
            'fixture_id' => $fixture?->id,
            'season_id' => $fixture?->season_id,
            'expense_type' => $paymentData['type'],
            'description' => $paymentData['description'],
            'amount' => $paymentData['amount'],
            'payment_status' => $paymentData['status'],
            'due_date' => now()->addDays(rand(7, 30)),
            'notes' => 'Pago generado por script de prueba',
        ]);

        // Si el pago está confirmado, agregar datos de pago
        if ($paymentData['status'] === 'confirmed') {
            $expense->update([
                'payment_method' => 'card',
                'paid_at' => now(),
                'confirmed_at' => now(),
                'approved_at' => now()->subDays(2),
                'payment_reference' => 'TEST-' . strtoupper(uniqid()),
            ]);
        } elseif ($paymentData['status'] === 'ready_for_payment') {
            $expense->update([
                'approved_at' => now()->subDays(1),
            ]);
        } elseif ($paymentData['status'] === 'approved') {
            $expense->update([
                'approved_at' => now()->subHours(6),
            ]);
        }

        $statusIcon = [
            'pending' => '⏳',
            'approved' => '✅',
            'ready_for_payment' => '💳',
            'confirmed' => '✓',
        ][$paymentData['status']] ?? '•';

        echo "{$statusIcon} Pago #{$expense->id} creado:\n";
        echo "   Árbitro: {$referee->first_name} {$referee->last_name}\n";
        echo "   Tipo: {$paymentData['type']}\n";
        echo "   Monto: \${$paymentData['amount']}\n";
        echo "   Estado: {$paymentData['status']}\n";
        if ($fixture) {
            echo "   Fixture: #{$fixture->id}\n";
        }
        echo "\n";

        $createdPayments++;
    }

    DB::commit();

    echo "\n=== RESUMEN ===\n";
    echo "✅ Se crearon {$createdPayments} pagos a árbitros\n";
    echo "✅ Liga: {$league->name}\n";
    echo "✅ Árbitros: {$referees->count()}\n\n";

    echo "=== ESTADÍSTICAS DE PAGOS ===\n";
    $totalPending = Expense::where('league_id', $league->id)->where('payment_status', 'pending')->count();
    $totalApproved = Expense::where('league_id', $league->id)->where('payment_status', 'approved')->count();
    $totalReady = Expense::where('league_id', $league->id)->where('payment_status', 'ready_for_payment')->count();
    $totalConfirmed = Expense::where('league_id', $league->id)->where('payment_status', 'confirmed')->count();
    $totalAmount = Expense::where('league_id', $league->id)->sum('amount');

    echo "   ⏳ Pendientes de aprobación: {$totalPending}\n";
    echo "   ✅ Aprobados: {$totalApproved}\n";
    echo "   💳 Listos para pagar: {$totalReady}\n";
    echo "   ✓ Confirmados/Pagados: {$totalConfirmed}\n";
    echo "   💰 Monto total de pagos: \$" . number_format($totalAmount, 2) . "\n\n";

    echo "=== PRÓXIMOS PASOS ===\n";
    echo "1. Accede a: http://flowfast-saas.test/payments/referees\n";
    echo "2. Verás la lista de pagos a árbitros con diferentes estados\n";
    echo "3. Puedes aprobar pagos pendientes\n";
    echo "4. Marca pagos como 'listos para pagar'\n";
    echo "5. Realiza pagos con Stripe usando la tarjeta de prueba: 4242 4242 4242 4242\n\n";

    echo "✅ Script completado exitosamente!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
