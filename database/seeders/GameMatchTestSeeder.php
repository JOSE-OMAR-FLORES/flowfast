<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GameMatch;

class GameMatchTestSeeder extends Seeder
{
    public function run()
    {
        // Obtener equipos existentes
        $teams = DB::table('teams')->select('id')->limit(2)->get();
        if ($teams->count() < 2) {
            echo "Se necesitan al menos 2 equipos para crear un partido.\n";
            return;
        }

        // Primero crear un round si no existe
        $round = DB::table('rounds')->first();
        if (!$round) {
            DB::table('rounds')->insert([
                'id' => 1,
                'season_id' => 1,
                'round_number' => 1,
                'name' => 'Jornada 1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $roundId = 1;
        } else {
            $roundId = $round->id;
        }

        // Usar inserción directa para evitar problemas con fillable
        DB::table('game_matches')->updateOrInsert(
            ['id' => 1],
            [
                'season_id' => 1,
                'round_id' => $roundId,
                'home_team_id' => $teams[0]->id,
                'away_team_id' => $teams[1]->id,
                'scheduled_at' => now()->addDay(),
                'status' => 'scheduled',
                'home_score' => 0,
                'away_score' => 0,
                'referee_id' => null,
                'venue' => 'Estadio de Prueba',
                'notes' => 'Partido de prueba generado por seeder.',
                'events' => '[]',
                'started_at' => null,
                'finished_at' => null,
                'duration_minutes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
