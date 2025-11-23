<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== 🔍 VERIFICACIÓN DE BASE DE DATOS ===\n\n";

// 1. Mostrar configuración actual
echo "📋 Configuración:\n";
echo "  DB_CONNECTION: " . config('database.default') . "\n";
echo "  DB_HOST: " . config('database.connections.mysql.host') . "\n";
echo "  DB_PORT: " . config('database.connections.mysql.port') . "\n";
echo "  DB_DATABASE: " . config('database.connections.mysql.database') . "\n";
echo "  DB_USERNAME: " . config('database.connections.mysql.username') . "\n\n";

// 2. Intentar consulta
echo "🔌 Probando conexión...\n";
try {
    $dbName = DB::connection()->getDatabaseName();
    echo "  ✅ Conectado a: {$dbName}\n\n";
    
    // 3. Verificar tabla leagues
    echo "📊 Verificando tabla 'leagues'...\n";
    $count = DB::table('leagues')->count();
    echo "  ✅ Tabla existe. Total registros: {$count}\n\n";
    
    // 4. Verificar columna is_public
    $columns = DB::select("SHOW COLUMNS FROM leagues WHERE Field = 'is_public'");
    if (count($columns) > 0) {
        echo "  ✅ Columna 'is_public' existe\n";
        echo "     Tipo: " . $columns[0]->Type . "\n";
        echo "     Default: " . ($columns[0]->Default ?? 'NULL') . "\n\n";
    } else {
        echo "  ⚠️  Columna 'is_public' NO existe\n\n";
    }
    
    // 5. Consulta que usa el componente Home
    echo "🏠 Probando consulta del componente Home...\n";
    $leagues = DB::table('leagues')
        ->where('is_public', 1)
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get();
    echo "  ✅ Consulta exitosa. Ligas públicas: " . $leagues->count() . "\n\n";
    
} catch (\Exception $e) {
    echo "  ❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "📝 Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
}

echo "=== ✅ VERIFICACIÓN COMPLETA ===\n\n";
