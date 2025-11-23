<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Season;
use App\Models\Coach;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = Season::all();

        if ($seasons->isEmpty()) {
            $this->command->error('No se encontraron temporadas. Ejecuta primero SeasonSeeder.');
            return;
        }

        $coaches = Coach::all();

        // Obtener la primera temporada (Fútbol - Primavera 2024)
        $footballSeason = $seasons->first();

                // Equipos para la temporada de Fútbol
        Team::create([
            'season_id' => $footballSeason->id,
            'coach_id' => $coaches->first()?->id,
            'name' => 'Los Tigres FC',
            'primary_color' => '#FF6B00',
            'secondary_color' => '#FFFFFF',
            'registration_paid' => true,
            'registration_paid_at' => now(),
        ]);

        Team::create([
            'season_id' => $footballSeason->id,
            'coach_id' => null,
            'name' => 'Águilas Doradas',
            'primary_color' => '#FFD700',
            'secondary_color' => '#000000',
            'registration_paid' => true,
            'registration_paid_at' => now(),
        ]);

        Team::create([
            'season_id' => $footballSeason->id,
            'coach_id' => null,
            'name' => 'Leones del Sur',
            'primary_color' => '#8B4513',
            'secondary_color' => '#FFF8DC',
            'registration_paid' => false,
        ]);

        Team::create([
            'season_id' => $footballSeason->id,
            'coach_id' => null,
            'name' => 'Pumas United',
            'primary_color' => '#1E90FF',
            'secondary_color' => '#FFFFFF',
            'registration_paid' => true,
            'registration_paid_at' => now(),
        ]);

        // Equipos para la segunda temporada (Baloncesto)
        if ($seasons->count() > 1) {
            $basketballSeason = $seasons->skip(1)->first();

            Team::create([
                'season_id' => $basketballSeason->id,
                'coach_id' => null,
                'name' => 'Warriors Basketball',
                'primary_color' => '#1D428A',
                'secondary_color' => '#FFC72C',
                'registration_paid' => true,
                'registration_paid_at' => now(),
            ]);

            Team::create([
                'season_id' => $basketballSeason->id,
                'coach_id' => null,
                'name' => 'Phoenix Hoops',
                'primary_color' => '#E56020',
                'secondary_color' => '#1D1160',
                'registration_paid' => false,
            ]);
        }

        $this->command->info('✓ 6 equipos creados exitosamente.');
    }
}
