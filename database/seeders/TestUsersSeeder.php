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

class TestUsersSeeder extends Seeder
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
        ], [
            'description' => 'Deporte rey mundial',
            'rules' => 'Reglas básicas del fútbol'
        ]);

        // Usuario Admin
        $adminUser = User::create([
            'email' => 'admin@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);

        Admin::create([
            'user_id' => $adminUser->id,
            'name' => 'Administrador Principal',
            'phone' => '+1234567890',
        ]);

        // Usuario League Manager
        $managerUser = User::create([
            'email' => 'manager@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'league_manager',
            'email_verified_at' => now(),
        ]);

        LeagueManager::create([
            'user_id' => $managerUser->id,
            'name' => 'Carlos Manager',
            'phone' => '+1234567891',
        ]);

        // Usuario Coach
        $coachUser = User::create([
            'email' => 'coach@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'coach',
            'email_verified_at' => now(),
        ]);

        Coach::create([
            'user_id' => $coachUser->id,
            'name' => 'Juan Entrenador',
            'phone' => '+1234567892',
        ]);

        // Usuario Player
        $playerUser = User::create([
            'email' => 'player@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'player',
            'email_verified_at' => now(),
        ]);

        Player::create([
            'user_id' => $playerUser->id,
            'name' => 'Miguel Jugador',
            'phone' => '+1234567893',
            'position' => 'Delantero',
            'jersey_number' => 10,
        ]);

        // Usuario Referee
        $refereeUser = User::create([
            'email' => 'referee@flowfast.com',
            'password' => Hash::make('password'),
            'user_type' => 'referee',
            'email_verified_at' => now(),
        ]);

        Referee::create([
            'user_id' => $refereeUser->id,
            'name' => 'Roberto Árbitro',
            'phone' => '+1234567894',
            'certification_level' => 'Nacional',
            'license_number' => 'REF001',
        ]);

        $this->command->info('Usuarios de prueba creados exitosamente:');
        $this->command->info('Admin: admin@flowfast.com / password');
        $this->command->info('Manager: manager@flowfast.com / password');
        $this->command->info('Coach: coach@flowfast.com / password');
        $this->command->info('Player: player@flowfast.com / password');
        $this->command->info('Referee: referee@flowfast.com / password');
    }
}Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
