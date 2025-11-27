<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\League;
use App\Models\Player;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionTestSeeder extends Seeder
{
    /**
     * Seeder para crear datos de prueba en producción.
     * Crea 4 ligas de diferentes deportes, con 10 equipos cada una y 5 jugadores por equipo.
     * 
     * Uso: php artisan db:seed --class=ProductionTestSeeder -- --admin_id=1
     * O desde código: (new ProductionTestSeeder)->run(adminId: 1)
     */

    protected int $adminId;

    // Nombres de equipos por deporte
    protected array $teamNames = [
        'futbol' => [
            'Real Madrid FC', 'FC Barcelona', 'Atlético Madrid', 'Sevilla FC', 'Valencia CF',
            'Real Betis', 'Athletic Bilbao', 'Real Sociedad', 'Villarreal CF', 'Celta de Vigo'
        ],
        'basquetbol' => [
            'Lakers', 'Celtics', 'Warriors', 'Bulls', 'Heat',
            'Nets', 'Knicks', 'Clippers', 'Mavericks', 'Suns'
        ],
        'voleibol' => [
            'Águilas VBC', 'Tigres VBC', 'Leones VBC', 'Panteras VBC', 'Lobos VBC',
            'Halcones VBC', 'Pumas VBC', 'Jaguares VBC', 'Búfalos VBC', 'Toros VBC'
        ],
        'beisbol' => [
            'Yankees', 'Dodgers', 'Red Sox', 'Cubs', 'Cardinals',
            'Giants', 'Astros', 'Braves', 'Mets', 'Phillies'
        ],
    ];

    // Nombres de jugadores (primeros nombres)
    protected array $firstNames = [
        'Carlos', 'Miguel', 'Juan', 'Pedro', 'Luis',
        'Diego', 'Andrés', 'Sergio', 'Roberto', 'Alejandro',
        'Fernando', 'Ricardo', 'Eduardo', 'Daniel', 'Pablo',
        'Jorge', 'Martín', 'Raúl', 'José', 'Manuel',
        'Héctor', 'Gabriel', 'David', 'Oscar', 'Francisco',
        'Antonio', 'Rafael', 'Javier', 'Marcos', 'Bruno',
        'Iván', 'Adrián', 'Lucas', 'Ángel', 'Hugo',
        'Emilio', 'Nicolás', 'Enrique', 'Guillermo', 'Tomás',
        'Víctor', 'Ignacio', 'Gonzalo', 'Fabián', 'Mateo',
        'Salvador', 'Alberto', 'Ramón', 'Santiago', 'Jesús'
    ];

    // Apellidos
    protected array $lastNames = [
        'García', 'Rodríguez', 'Martínez', 'López', 'González',
        'Hernández', 'Pérez', 'Sánchez', 'Ramírez', 'Torres',
        'Flores', 'Rivera', 'Gómez', 'Díaz', 'Cruz',
        'Morales', 'Reyes', 'Ortiz', 'Gutiérrez', 'Ramos',
        'Castillo', 'Vargas', 'Mendoza', 'Jiménez', 'Ruiz',
        'Aguilar', 'Medina', 'Romero', 'Herrera', 'Vega',
        'Castro', 'Delgado', 'Moreno', 'Muñoz', 'Álvarez',
        'Fernández', 'Arias', 'Navarro', 'Campos', 'Luna',
        'Rojas', 'Domínguez', 'Suárez', 'Molina', 'Santos',
        'Acosta', 'Mejía', 'Ibarra', 'Salazar', 'Núñez'
    ];

    // Posiciones por deporte
    protected array $positions = [
        'futbol' => ['goalkeeper', 'defender', 'midfielder', 'forward'],
        'basquetbol' => ['point_guard', 'shooting_guard', 'small_forward', 'power_forward', 'center'],
        'voleibol' => ['setter', 'outside_hitter', 'middle_blocker', 'opposite', 'libero'],
        'beisbol' => ['pitcher', 'catcher', 'infielder', 'outfielder', 'designated_hitter'],
    ];

    public function run(?int $adminId = null): void
    {
        // Obtener el admin_id desde el argumento o desde la línea de comandos
        if ($adminId === null) {
            $adminId = $this->command ? ($this->command->option('admin_id') ?? null) : null;
        }

        if ($adminId === null) {
            $this->command?->error('❌ Debes especificar un admin_id. Uso: php artisan db:seed --class=ProductionTestSeeder -- --admin_id=1');
            return;
        }

        $admin = Admin::find($adminId);

        if (!$admin) {
            $this->command?->error("❌ No se encontró un admin con ID: {$adminId}");
            return;
        }

        $this->adminId = $adminId;

        $this->command?->info("🚀 Iniciando creación de datos de prueba para el admin: {$admin->company_name} (ID: {$adminId})");

        // Obtener 4 deportes (excluyendo tenis ya que es individual)
        $sports = Sport::whereIn('slug', ['futbol', 'basquetbol', 'voleibol', 'beisbol'])->get();

        if ($sports->count() < 4) {
            $this->command?->error('❌ No se encontraron suficientes deportes. Asegúrate de tener: futbol, basquetbol, voleibol, beisbol');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($sports as $sport) {
                $this->createLeagueWithTeamsAndPlayers($sport);
            }

            DB::commit();
            $this->command?->info('✅ Datos de prueba creados exitosamente!');
            $this->command?->info('📊 Resumen:');
            $this->command?->info('   - 4 Ligas creadas');
            $this->command?->info('   - 4 Temporadas activas');
            $this->command?->info('   - 40 Equipos creados (10 por liga)');
            $this->command?->info('   - 200 Jugadores creados (5 por equipo)');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command?->error('❌ Error al crear datos: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function createLeagueWithTeamsAndPlayers(Sport $sport): void
    {
        $this->command?->info("📌 Creando liga de {$sport->name}...");

        // Crear la liga
        $league = League::create([
            'admin_id' => $this->adminId,
            'sport_id' => $sport->id,
            'name' => "Liga Profesional de {$sport->name}",
            'description' => "Liga de prueba para {$sport->name} creada con datos de ejemplo.",
            'status' => 'active',
        ]);

        $this->command?->info("   ✓ Liga creada: {$league->name}");

        // Crear la temporada activa
        $season = Season::create([
            'league_id' => $league->id,
            'name' => 'Temporada 2025',
            'format' => 'round_robin',
            'round_robin_type' => 'single',
            'start_date' => now(),
            'end_date' => now()->addMonths(4),
            'game_days' => ['saturday', 'sunday'],
            'match_times' => ['10:00', '12:00', '16:00', '18:00'],
            'daily_matches' => 4,
            'status' => 'active',
        ]);

        $this->command?->info("   ✓ Temporada creada: {$season->name}");

        // Obtener nombres de equipos para este deporte
        $teamNames = $this->teamNames[$sport->slug] ?? $this->generateGenericTeamNames();

        // Crear 10 equipos con 5 jugadores cada uno
        foreach ($teamNames as $index => $teamName) {
            $team = Team::create([
                'season_id' => $season->id,
                'name' => $teamName,
                'short_name' => $this->generateShortName($teamName),
                'coach_id' => null, // Sin entrenador como se solicitó
                'primary_color' => $this->getRandomColor(),
                'secondary_color' => $this->getRandomColor(),
                'status' => 'active',
            ]);

            // Crear 5 jugadores para este equipo
            $this->createPlayersForTeam($team, $league, $sport->slug);
        }

        $this->command?->info("   ✓ 10 equipos creados con 5 jugadores cada uno");
    }

    protected function createPlayersForTeam(Team $team, League $league, string $sportSlug): void
    {
        $positions = $this->positions[$sportSlug] ?? ['player'];
        $usedJerseys = [];

        for ($i = 1; $i <= 5; $i++) {
            // Generar número de camiseta único
            do {
                $jerseyNumber = rand(1, 99);
            } while (in_array($jerseyNumber, $usedJerseys));
            $usedJerseys[] = $jerseyNumber;

            // Seleccionar posición (rotar entre las disponibles)
            $position = $positions[($i - 1) % count($positions)];

            // Generar nombre aleatorio
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];

            // Generar fecha de nacimiento (entre 18 y 35 años)
            $birthDate = now()->subYears(rand(18, 35))->subDays(rand(0, 365));

            Player::create([
                'team_id' => $team->id,
                'league_id' => $league->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower($firstName . '.' . $lastName . '.' . rand(100, 999) . '@example.com'),
                'phone' => '+52 ' . rand(100, 999) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'birth_date' => $birthDate,
                'jersey_number' => $jerseyNumber,
                'position' => $position,
                'status' => 'active',
                'matches_played' => 0,
                'goals' => 0,
                'assists' => 0,
                'yellow_cards' => 0,
                'red_cards' => 0,
            ]);
        }
    }

    protected function generateShortName(string $fullName): string
    {
        $words = explode(' ', $fullName);
        if (count($words) >= 2) {
            // Tomar las primeras letras de las primeras dos palabras
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 2));
        }
        return strtoupper(substr($fullName, 0, 3));
    }

    protected function generateGenericTeamNames(): array
    {
        $names = [];
        for ($i = 1; $i <= 10; $i++) {
            $names[] = "Equipo {$i}";
        }
        return $names;
    }

    protected function getRandomColor(): string
    {
        $colors = [
            '#EF4444', '#F97316', '#F59E0B', '#EAB308', '#84CC16',
            '#22C55E', '#10B981', '#14B8A6', '#06B6D4', '#0EA5E9',
            '#3B82F6', '#6366F1', '#8B5CF6', '#A855F7', '#D946EF',
            '#EC4899', '#F43F5E', '#1F2937', '#374151', '#4B5563'
        ];
        return $colors[array_rand($colors)];
    }
}
