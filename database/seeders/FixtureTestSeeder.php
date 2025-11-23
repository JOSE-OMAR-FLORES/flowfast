<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixtureTestSeeder extends Seeder
{
    public function run()
    {
        // Obtener equipos existentes
        $teams = DB::table('teams')->select('id')->limit(4)->get();
        if ($teams->count() < 2) {
            echo "Se necesitan al menos 2 equipos para crear fixtures.\n";
            return;
        }

        // Crear fixtures de prueba
        for ($i = 1; $i <= 5; $i++) {
            $homeTeamIndex = ($i - 1) % $teams->count();
            $awayTeamIndex = ($i) % $teams->count();
            
            if ($homeTeamIndex === $awayTeamIndex) {
                $awayTeamIndex = ($awayTeamIndex + 1) % $teams->count();
            }

            DB::table('fixtures')->updateOrInsert(
                ['id' => $i],
                [
                    'season_id' => 1,
                    'home_team_id' => $teams[$homeTeamIndex]->id,
                    'away_team_id' => $teams[$awayTeamIndex]->id,
                    'venue_id' => 1,
                    'round_number' => 1,
                    'match_number' => $i,
                    'match_date' => now()->addDays($i)->format('Y-m-d'),
                    'match_time' => '15:00',
                    'status' => 'scheduled',
                    'home_score' => 0,
                    'away_score' => 0,
                    'referee_id' => null,
                    'notes' => "Fixture de prueba #{$i}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        echo "Fixtures de prueba creados (IDs: 1-5)\n";
    }
}