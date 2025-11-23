<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Season;

echo "\n=== CONFIGURACIÓN DE TEMPORADAS ===\n\n";

$seasons = Season::with('league')->get();

foreach($seasons as $season) {
    echo "🏆 TEMPORADA: {$season->name} (ID: {$season->id})\n";
    echo "   Liga: {$season->league->name}\n";
    echo str_repeat('-', 70) . "\n";
    
    echo "\n   📅 DÍAS DE JUEGO (game_days):\n";
    if($season->game_days && is_array($season->game_days)) {
        echo "      Valor actual: " . json_encode($season->game_days) . "\n";
        echo "      Interpretación:\n";
        foreach($season->game_days as $day) {
            $dayNames = [
                0 => 'Domingo',
                1 => 'Lunes', 
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado'
            ];
            echo "         • {$dayNames[$day]} (día {$day})\n";
        }
    } else {
        echo "      ⚠️  No configurado\n";
    }
    
    echo "\n   ⏰ HORARIOS (match_times):\n";
    if($season->match_times && is_array($season->match_times)) {
        echo "      Valor actual: " . json_encode($season->match_times) . "\n";
        echo "      Horarios disponibles:\n";
        foreach($season->match_times as $time) {
            echo "         • {$time}\n";
        }
    } else {
        echo "      ⚠️  No configurado\n";
    }
    
    echo "\n   🎮 PARTIDOS POR DÍA (daily_matches):\n";
    echo "      {$season->daily_matches} partidos por día\n";
    
    echo "\n" . str_repeat('=', 70) . "\n\n";
}

echo "\n💡 CÓMO FUNCIONA:\n\n";
echo "1. game_days: Array de números donde 0=Domingo, 1=Lunes, ... 6=Sábado\n";
echo "   Ejemplo: [2, 4, 6] = Martes, Jueves, Sábado\n\n";
echo "2. match_times: Array de horarios en formato HH:MM\n";
echo "   Ejemplo: ['14:00', '16:00', '18:00']\n\n";
echo "3. daily_matches: Número de partidos que se juegan por día\n\n";
echo "4. El algoritmo calcula automáticamente:\n";
echo "   - Qué día de la semana toca según game_days\n";
echo "   - Qué horario usar rotando entre match_times\n\n";
