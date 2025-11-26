<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Standing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MultiSportTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando datos de prueba para múltiples deportes...');

        // Obtener el admin principal (admin_id = 1)
        $adminId = 1;

        // Datos de ligas y equipos por deporte
        $sportsData = [
            'basquetbol' => [
                'league_name' => 'Liga de Básquetbol Tehuacán',
                'teams' => [
                    'Lakers Tehuacán',
                    'Bulls de la Sierra',
                    'Celtics del Centro',
                    'Heat Tropical',
                    'Warriors del Valle',
                    'Spurs Poblanos',
                ],
            ],
            'voleibol' => [
                'league_name' => 'Liga de Voleibol Tehuacán',
                'teams' => [
                    'Aztecas Volley',
                    'Águilas de Playa',
                    'Tigres del Net',
                    'Lobos Volley',
                    'Halcones Arena',
                    'Pumas del Bloqueo',
                ],
            ],
            'beisbol' => [
                'league_name' => 'Liga de Béisbol Tehuacán',
                'teams' => [
                    'Diablos Rojos Tehuacán',
                    'Tigres del Diamante',
                    'Leones de Puebla',
                    'Pericos Poblanos',
                    'Guerreros del Bate',
                    'Sultanes del Valle',
                ],
            ],
        ];

        foreach ($sportsData as $sportSlug => $data) {
            $sport = Sport::where('slug', $sportSlug)->first();
            
            if (!$sport) {
                $this->command->warn("Deporte {$sportSlug} no encontrado, saltando...");
                continue;
            }

            $this->command->info("Creando liga de {$sport->name}...");

            // Crear la liga
            $league = League::create([
                'name' => $data['league_name'],
                'slug' => Str::slug($data['league_name']),
                'sport_id' => $sport->id,
                'admin_id' => $adminId,
                'description' => "Liga profesional de {$sport->name} en la región de Tehuacán. Competencia de alto nivel con los mejores equipos de la zona.",
                'is_public' => true,
                'registration_fee' => rand(500, 1500),
                'match_fee' => rand(100, 300),
                'referee_payment' => rand(150, 400),
            ]);

            $this->command->info("  ✓ Liga creada: {$league->name}");

            // Crear la temporada
            $season = Season::create([
                'league_id' => $league->id,
                'name' => 'Temporada 2025',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->addMonths(4),
                'status' => 'active',
                'game_days' => json_encode(['saturday', 'sunday']),
                'daily_matches' => 4,
                'match_times' => json_encode(['09:00', '11:00', '13:00', '15:00']),
            ]);

            $this->command->info("  ✓ Temporada creada: {$season->name}");

            // Crear los equipos y standings
            foreach ($data['teams'] as $index => $teamName) {
                $team = Team::create([
                    'name' => $teamName,
                    'slug' => Str::slug($teamName),
                    'season_id' => $season->id,
                ]);

                // Crear standing con datos aleatorios de prueba
                $played = rand(3, 8);
                $won = rand(0, $played);
                $lost = $played - $won;
                $drawn = 0;
                
                // Para deportes que permiten empates (fútbol)
                if ($sport->allowsDraws()) {
                    $drawn = rand(0, min(2, $played - $won));
                    $lost = $played - $won - $drawn;
                }

                // Puntos según el sistema de puntuación del deporte
                $scoringSystem = $sport->scoring_system ?? ['win' => 2, 'loss' => 0];
                $points = ($won * ($scoringSystem['win'] ?? 2)) + ($drawn * ($scoringSystem['draw'] ?? 0));

                // Goles/Puntos/Carreras a favor y en contra
                $goalsFor = rand(10, 50) * ($sport->slug === 'basquetbol' ? 2 : 1);
                $goalsAgainst = rand(8, 45) * ($sport->slug === 'basquetbol' ? 2 : 1);

                Standing::create([
                    'season_id' => $season->id,
                    'team_id' => $team->id,
                    'played' => $played,
                    'won' => $won,
                    'drawn' => $drawn,
                    'lost' => $lost,
                    'goals_for' => $goalsFor,
                    'goals_against' => $goalsAgainst,
                    'goal_difference' => $goalsFor - $goalsAgainst,
                    'points' => $points,
                    'position' => $index + 1,
                    'form' => $this->generateRandomForm($played),
                ]);

                $this->command->info("    - Equipo: {$teamName}");
            }
        }

        $this->command->info('');
        $this->command->info('✅ Datos de prueba multi-deporte creados exitosamente!');
        $this->command->info('');
        $this->command->info('Puedes probar las ligas en:');
        
        $leagues = League::whereHas('sport', function($q) {
            $q->whereIn('slug', ['basquetbol', 'voleibol', 'beisbol']);
        })->get();
        
        foreach ($leagues as $league) {
            $this->command->info("  - /league/{$league->slug}");
        }
    }

    /**
     * Generar forma aleatoria (últimos 5 resultados)
     */
    private function generateRandomForm(int $played): string
    {
        $results = ['W', 'L', 'D'];
        $form = '';
        
        for ($i = 0; $i < min(5, $played); $i++) {
            $form .= $results[array_rand($results)];
        }
        
        return $form;
    }
}
