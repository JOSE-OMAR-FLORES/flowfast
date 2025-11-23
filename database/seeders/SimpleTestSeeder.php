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

class SimpleTestSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Crear deporte de prueba
        Sport::firstOrCreate([
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

        $this->command->info('Usuarios básicos creados exitosamente:');
        $this->command->info('Admin: admin@flowfast.com / password');
        $this->command->info('Manager: manager@flowfast.com / password');
    }
}