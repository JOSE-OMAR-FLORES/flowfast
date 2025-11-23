<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\League;

echo "=== Verificación completa de Liga 6 ===\n\n";

// Consulta directa a la base de datos
$leagueRaw = DB::table('leagues')->where('id', 6)->first();

echo "Valores RAW de la base de datos:\n";
echo "- match_fee: {$leagueRaw->match_fee}\n";
echo "- referee_payment: {$leagueRaw->referee_payment}\n";
echo "- registration_fee: {$leagueRaw->registration_fee}\n";
echo "- penalty_fee: {$leagueRaw->penalty_fee}\n\n";

// A través del modelo
$league = League::find(6);

echo "Valores a través del Modelo:\n";
echo "- match_fee: {$league->match_fee}\n";
echo "- referee_payment: {$league->referee_payment}\n";
echo "- registration_fee: {$league->registration_fee}\n";
echo "- penalty_fee: {$league->penalty_fee}\n\n";

// Verificar tipo de dato
echo "Tipo de dato:\n";
echo "- match_fee: " . gettype($league->match_fee) . "\n";
echo "- referee_payment: " . gettype($league->referee_payment) . "\n\n";

// Intentar actualizar el valor
echo "=== Intentando actualizar match_fee a 250 ===\n";
$league->match_fee = 250.00;
$league->save();

echo "✅ Liga actualizada\n\n";

// Verificar de nuevo
$leagueUpdated = League::find(6);
echo "Nuevo valor de match_fee: {$leagueUpdated->match_fee}\n";
