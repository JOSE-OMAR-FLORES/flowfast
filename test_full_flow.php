<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sport;
use App\Models\League;
use App\Models\Venue;
use App\Models\Team;
use App\Models\Season;
use App\Models\Player;
use App\Models\GameMatch;
use App\Models\User;

echo "\n=== 🧪 VERIFICACIÓN COMPLETA DE LA APLICACIÓN ===\n\n";

// 1. Verificar Deportes
echo "1️⃣ DEPORTES:\n";
$sports = Sport::all();
if ($sports->isEmpty()) {
    echo "   ⚠️  No hay deportes creados. Creando deportes de prueba...\n";
    $futbol = Sport::create([
        'name' => 'Fútbol',
        'players_per_team' => 11,
        'max_players_per_team' => 18,
        'description' => 'Deporte más popular del mundo'
    ]);
    $basquet = Sport::create([
        'name' => 'Básquetbol',
        'players_per_team' => 5,
        'max_players_per_team' => 12,
        'description' => 'Deporte de canasta'
    ]);
    echo "   ✅ Creados: Fútbol, Básquetbol\n";
} else {
    foreach ($sports as $sport) {
        echo "   ✅ {$sport->name} ({$sport->players_per_team} jugadores)\n";
    }
}

// 2. Verificar Venues
echo "\n2️⃣ VENUES (Canchas):\n";
$venues = Venue::all();
if ($venues->isEmpty()) {
    echo "   ⚠️  No hay venues. Creando venues de prueba...\n";
    Venue::create([
        'name' => 'Cancha Principal',
        'address' => 'Av. Principal 123',
        'capacity' => 1000,
        'has_lighting' => true
    ]);
    Venue::create([
        'name' => 'Cancha Norte',
        'address' => 'Zona Norte s/n',
        'capacity' => 500,
        'has_lighting' => false
    ]);
    echo "   ✅ Creados: Cancha Principal, Cancha Norte\n";
} else {
    foreach ($venues as $venue) {
        echo "   ✅ {$venue->name} (Capacidad: {$venue->capacity})\n";
    }
}

// 3. Verificar Ligas
echo "\n3️⃣ LIGAS:\n";
$leagues = League::with('sport')->get();
if ($leagues->isEmpty()) {
    echo "   ⚠️  No hay ligas creadas\n";
    echo "   📝 Crea una liga desde: /admin/leagues/create\n";
} else {
    foreach ($leagues as $league) {
        echo "   ✅ {$league->name} ({$league->sport->name})\n";
        
        // Verificar temporadas
        $seasons = Season::where('league_id', $league->id)->get();
        echo "      └─ Temporadas: {$seasons->count()}\n";
        
        // Verificar equipos por cada temporada
        foreach ($seasons as $season) {
            $teams = Team::where('season_id', $season->id)->count();
            echo "         └─ {$season->name}: {$teams} equipos\n";
        }
    }
}

// 4. Verificar Temporadas Activas
echo "\n4️⃣ TEMPORADAS ACTIVAS:\n";
$activeSeasons = Season::where('status', 'active')->with('league')->get();
if ($activeSeasons->isEmpty()) {
    echo "   ⚠️  No hay temporadas activas\n";
    echo "   📝 Activa una temporada desde: /admin/seasons\n";
} else {
    foreach ($activeSeasons as $season) {
        echo "   ✅ {$season->name} - {$season->league->name}\n";
        
        // Verificar fixtures
        $fixtures = GameMatch::where('season_id', $season->id)->count();
        echo "      └─ Partidos: {$fixtures}\n";
    }
}

// 5. Verificar Jugadores
echo "\n5️⃣ JUGADORES:\n";
$players = Player::with('team')->get();
if ($players->isEmpty()) {
    echo "   ⚠️  No hay jugadores registrados\n";
    echo "   📝 Opciones:\n";
    echo "      - Crear manualmente: /admin/players/create\n";
    echo "      - Importar CSV: /admin/players/import\n";
} else {
    $byTeam = $players->groupBy('team_id');
    foreach ($byTeam as $teamId => $teamPlayers) {
        $team = Team::find($teamId);
        if ($team) {
            echo "   ✅ {$team->name}: {$teamPlayers->count()} jugadores\n";
        }
    }
}

// 6. Verificar Partidos
echo "\n6️⃣ PARTIDOS:\n";
$matches = GameMatch::with(['homeTeam', 'awayTeam', 'season'])->get();
if ($matches->isEmpty()) {
    echo "   ⚠️  No hay partidos generados\n";
    echo "   📝 Genera fixtures desde: /admin/fixtures/generate\n";
} else {
    $byStatus = $matches->groupBy('status');
    foreach ($byStatus as $status => $statusMatches) {
        $statusLabel = [
            'scheduled' => 'Programados',
            'live' => 'En Vivo',
            'in_progress' => 'En Progreso',
            'finished' => 'Finalizados',
            'postponed' => 'Pospuestos',
            'cancelled' => 'Cancelados'
        ][$status] ?? $status;
        
        echo "   📊 {$statusLabel}: {$statusMatches->count()}\n";
    }
}

// 7. Verificar Usuarios
echo "\n7️⃣ USUARIOS:\n";
$users = User::all();
$byRole = $users->groupBy('user_type');
foreach ($byRole as $role => $roleUsers) {
    $roleLabel = [
        'admin' => 'Administradores',
        'league_manager' => 'Managers de Liga',
        'referee' => 'Árbitros',
        'coach' => 'Entrenadores',
        'player' => 'Jugadores'
    ][$role] ?? $role;
    
    echo "   👤 {$roleLabel}: {$roleUsers->count()}\n";
}

// Verificar admin
$admin = User::where('user_type', 'admin')->first();
if ($admin) {
    echo "   ✅ Admin disponible: {$admin->email}\n";
} else {
    echo "   ⚠️  No hay usuario admin\n";
    echo "   📝 Ejecuta: php create_admin_temp.php\n";
}

// RESUMEN FINAL
echo "\n\n=== 📊 RESUMEN ===\n";
echo "✅ Deportes: " . Sport::count() . "\n";
echo "✅ Venues: " . Venue::count() . "\n";
echo "✅ Ligas: " . League::count() . "\n";
echo "✅ Equipos: " . Team::count() . "\n";
echo "✅ Temporadas: " . Season::count() . "\n";
echo "✅ Jugadores: " . Player::count() . "\n";
echo "✅ Partidos: " . GameMatch::count() . "\n";
echo "✅ Usuarios: " . User::count() . "\n";

echo "\n=== 🎯 SIGUIENTE PASO ===\n";

if (Sport::count() == 0) {
    echo "1️⃣ Ejecutar este script de nuevo (ya creó los deportes)\n";
} elseif (League::count() == 0) {
    echo "1️⃣ Crear una liga: http://localhost/flowfast-saas/public/admin/leagues/create\n";
} elseif (Team::count() < 4) {
    echo "1️⃣ Crear al menos 4 equipos para poder generar fixtures\n";
    echo "   URL: http://localhost/flowfast-saas/public/admin/teams/create\n";
} elseif (Season::count() == 0) {
    echo "1️⃣ Crear una temporada: http://localhost/flowfast-saas/public/admin/seasons/create\n";
} elseif (Player::count() == 0) {
    echo "1️⃣ Importar jugadores: http://localhost/flowfast-saas/public/admin/players/import\n";
    echo "   O crear manualmente: http://localhost/flowfast-saas/public/admin/players/create\n";
} elseif (GameMatch::count() == 0) {
    echo "1️⃣ Generar fixtures: http://localhost/flowfast-saas/public/admin/fixtures/generate\n";
} else {
    echo "🎉 ¡Todo listo! Puedes probar:\n";
    echo "   ⚽ Gestionar partido en vivo: http://localhost/flowfast-saas/public/admin/fixtures\n";
    echo "   👥 Ver jugadores: http://localhost/flowfast-saas/public/admin/players\n";
    echo "   🏆 Ver tabla: http://localhost/flowfast-saas/public/admin/standings\n";
}

echo "\n\n";
