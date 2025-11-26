<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::find(18);

echo "User ID: " . $user->id . PHP_EOL;
echo "User Type: " . $user->user_type . PHP_EOL;
echo "Userable Type: " . $user->userable_type . PHP_EOL;
echo "Userable ID: " . $user->userable_id . PHP_EOL;

if ($user->userable_type === 'App\\Models\\Coach') {
    $coach = App\Models\Coach::find($user->userable_id);
    if ($coach) {
        echo "Coach ID: " . $coach->id . PHP_EOL;
        echo "Coach Name: " . $coach->full_name . PHP_EOL;
        echo "Coach team_id: " . ($coach->team_id ?? 'NULL') . PHP_EOL;
        
        $teams = App\Models\Team::where('coach_id', $coach->id)->get();
        echo "Teams con coach_id=" . $coach->id . ": " . $teams->count() . PHP_EOL;
        foreach ($teams as $t) {
            echo "  - Team: " . $t->name . " (ID: " . $t->id . ")" . PHP_EOL;
        }
        
        // Verificar partidos
        if ($teams->count() > 0) {
            $teamIds = $teams->pluck('id');
            $matches = App\Models\GameMatch::where('status', 'scheduled')
                ->where('scheduled_at', '>', now())
                ->where(function ($q) use ($teamIds) {
                    $q->whereIn('home_team_id', $teamIds)
                      ->orWhereIn('away_team_id', $teamIds);
                })
                ->get();
            echo "Partidos scheduled futuros: " . $matches->count() . PHP_EOL;
            foreach ($matches as $m) {
                echo "  - Match ID: " . $m->id . " | round_id: " . ($m->round_id ?? 'NULL') . " | " . $m->scheduled_at . PHP_EOL;
            }
        }
    } else {
        echo "Coach no encontrado con ID: " . $user->userable_id . PHP_EOL;
    }
} else {
    echo "Usuario no es coach. userable_type = " . $user->userable_type . PHP_EOL;
}
