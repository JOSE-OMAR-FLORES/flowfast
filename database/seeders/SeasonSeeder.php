<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leagues = League::all();

        if ($leagues->isEmpty()) {
            $this->command->error('No se encontraron ligas. Ejecuta primero LeagueSeeder.');
            return;
        }

        // Temporada 1: Liga de Fútbol - Temporada Primavera 2024
        Season::create([
            'league_id' => $leagues->where('slug', 'liga-futbol-amateur')->first()?->id ?? $leagues->first()->id,
            'name' => 'Temporada Primavera 2024',
            'format' => 'round_robin',
            'round_robin_type' => 'double',
            'start_date' => '2024-03-01',
            'end_date' => '2024-06-30',
            'game_days' => ['wednesday', 'saturday'],
            'daily_matches' => 3,
            'match_times' => ['18:00', '19:30', '21:00'],
            'status' => 'active',
        ]);

        // Temporada 2: Liga de Baloncesto - Temporada Verano 2024
        Season::create([
            'league_id' => $leagues->where('slug', 'liga-baloncesto-regional')->first()?->id ?? $leagues->skip(1)->first()?->id ?? $leagues->first()->id,
            'name' => 'Temporada Verano 2024',
            'format' => 'league',
            'round_robin_type' => null,
            'start_date' => '2024-07-01',
            'end_date' => '2024-09-30',
            'game_days' => ['friday', 'sunday'],
            'daily_matches' => 4,
            'match_times' => ['16:00', '17:45', '19:30', '21:15'],
            'status' => 'upcoming',
        ]);

        // Temporada 3: Liga de Voleibol - Temporada Apertura 2024
        Season::create([
            'league_id' => $leagues->where('slug', 'voleibol-playa')->first()?->id ?? $leagues->skip(2)->first()?->id ?? $leagues->first()->id,
            'name' => 'Temporada Apertura 2024',
            'format' => 'playoff',
            'round_robin_type' => null,
            'start_date' => '2024-10-01',
            'end_date' => '2024-12-15',
            'game_days' => ['thursday', 'saturday'],
            'daily_matches' => 2,
            'match_times' => ['18:30', '20:00'],
            'status' => 'draft',
        ]);

        $this->command->info('✓ 3 temporadas creadas exitosamente.');
    }
}
