<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\LeagueManager;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Referee;
use App\Models\Sport;
use Illuminate\Support\Facades\Hash;

class TestUsersSeederNew extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Crear deporte de prueba
        $sport = Sport::firstOrCreate([
            'name' => 'Fútbol',
            'slug' => 'futbol'
        ]);

        // Crear Admin primero
        $admin = Admin::create([
            'first_name' => 'Administrador',
            'last_name' => 'Principal',
            'phone' => '+1234567890',
        ]);

        User::create([
            'email' => 'admin@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'userable_id' => $admin->id,
            'userable_type' => Admin::class,
            'email_verified_at' => now(),
        ]);

        // Crear League Manager
        $manager = LeagueManager::create([
            'first_name' => 'Carlos',
            'last_name' => 'Manager',
            'phone' => '+1234567891',
            'admin_id' => $admin->id,
        ]);

        User::create([
            'email' => 'manager@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'league_manager',
            'userable_id' => $manager->id,
            'userable_type' => LeagueManager::class,
            'email_verified_at' => now(),
        ]);

        // Crear Coach
        $coach = Coach::create([
            'first_name' => 'Juan',
            'last_name' => 'Entrenador',
            'phone' => '+1234567892',
            'admin_id' => $admin->id,
        ]);

        User::create([
            'email' => 'coach@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'coach',
            'userable_id' => $coach->id,
            'userable_type' => Coach::class,
            'email_verified_at' => now(),
        ]);

        // Crear Player
        $player = Player::create([
            'first_name' => 'Miguel',
            'last_name' => 'Jugador',
            'phone' => '+1234567893',
            'position' => 'Delantero',
            'jersey_number' => 10,
            'admin_id' => $admin->id,
        ]);

        User::create([
            'email' => 'player@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'player',
            'userable_id' => $player->id,
            'userable_type' => Player::class,
            'email_verified_at' => now(),
        ]);

        // Crear Referee
        $referee = Referee::create([
            'first_name' => 'Roberto',
            'last_name' => 'Árbitro',
            'phone' => '+1234567894',
            'certification_level' => 'Nacional',
            'license_number' => 'REF001',
            'admin_id' => $admin->id,
        ]);

        User::create([
            'email' => 'referee@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'referee',
            'userable_id' => $referee->id,
            'userable_type' => Referee::class,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Usuarios de prueba creados exitosamente:');
        $this->command->info('Admin: admin@flowfast.com / password');
        $this->command->info('Manager: manager@flowfast.com / password');
        $this->command->info('Coach: coach@flowfast.com / password');
        $this->command->info('Player: player@flowfast.com / password');
        $this->command->info('Referee: referee@flowfast.com / password');
    }
}