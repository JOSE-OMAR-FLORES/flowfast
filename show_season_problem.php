<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Season;

echo "\n=== CONFIGURACIÓN ACTUAL DE TEMPORADAS ===\n\n";

$seasons = Season::with('league')->get();

foreach($seasons as $season) {
    echo "🏆 {$season->name} (ID: {$season->id})\n";
    echo "   Liga: {$season->league->name}\n";
    echo str_repeat('-', 70) . "\n";
    
    echo "\n   📅 game_days (tal como está en BD):\n";
    echo "      " . json_encode($season->game_days) . "\n";
    
    echo "\n   ⏰ match_times (tal como está en BD):\n";
    echo "      " . json_encode($season->match_times) . "\n";
    
    echo "\n   🎮 daily_matches:\n";
    echo "      {$season->daily_matches}\n";
    
    echo "\n" . str_repeat('=', 70) . "\n\n";
}

echo "\n📝 PROBLEMA IDENTIFICADO:\n\n";
echo "Los game_days están guardados como palabras (wednesday, saturday)\n";
echo "pero el código de Generate.php espera NÚMEROS (0-6):\n";
echo "   0 = Domingo\n";
echo "   1 = Lunes\n";
echo "   2 = Martes\n";
echo "   3 = Miércoles\n";
echo "   4 = Jueves\n";
echo "   5 = Viernes\n";
echo "   6 = Sábado\n\n";

echo "💡 SOLUCIONES:\n\n";
echo "1. Convertir las palabras a números en el código Generate.php\n";
echo "2. O actualizar la BD para usar números en lugar de palabras\n\n";
echo "¿Qué prefieres?\n\n";
