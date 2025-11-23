<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = [
            [
                'name' => 'Fútbol',
                'slug' => 'futbol',
                'players_per_team' => 11,
                'match_duration' => 90,
                'scoring_system' => json_encode([
                    'win' => 3,
                    'draw' => 1,
                    'loss' => 0
                ])
            ],
            [
                'name' => 'Básquetbol',
                'slug' => 'basquetbol',
                'players_per_team' => 5,
                'match_duration' => 48,
                'scoring_system' => json_encode([
                    'win' => 2,
                    'loss' => 0
                ])
            ],
            [
                'name' => 'Voleibol',
                'slug' => 'voleibol',
                'players_per_team' => 6,
                'match_duration' => 120,
                'scoring_system' => json_encode([
                    'win' => 3,
                    'loss' => 0
                ])
            ],
            [
                'name' => 'Fútbol Sala',
                'slug' => 'futbol-sala',
                'players_per_team' => 5,
                'match_duration' => 40,
                'scoring_system' => json_encode([
                    'win' => 3,
                    'draw' => 1,
                    'loss' => 0
                ])
            ],
            [
                'name' => 'Tenis',
                'slug' => 'tenis',
                'players_per_team' => 1,
                'match_duration' => 180,
                'scoring_system' => json_encode([
                    'win' => 2,
                    'loss' => 0
                ])
            ]
        ];

        foreach ($sports as $sport) {
            DB::table('sports')->insert([
                'name' => $sport['name'],
                'slug' => $sport['slug'],
                'players_per_team' => $sport['players_per_team'],
                'match_duration' => $sport['match_duration'],
                'scoring_system' => $sport['scoring_system'],
                'created_at' => now()
            ]);
        }
    }
}
