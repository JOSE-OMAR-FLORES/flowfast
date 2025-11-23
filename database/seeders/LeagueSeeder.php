<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;
use App\Models\Sport;
use App\Models\Admin;
use Illuminate\Support\Str;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = Sport::all();
        $admin = Admin::first();

        if (!$admin) {
            $this->command->warn('No hay administradores en la base de datos. Ejecuta primero los seeders de usuarios.');
            return;
        }

        $leagues = [
            [
                'name' => 'Liga Premier de Fútbol',
                'description' => 'Liga profesional de fútbol con los mejores equipos de la región',
                'sport_id' => $sports->where('name', 'Fútbol')->first()?->id ?? $sports->first()->id,
                'registration_fee' => 500.00,
                'match_fee_per_team' => 50.00,
                'penalty_fee' => 100.00,
                'referee_payment' => 75.00,
                'status' => 'active',
            ],
            [
                'name' => 'Liga Nacional de Baloncesto',
                'description' => 'Competencia de baloncesto amateur y semi-profesional',
                'sport_id' => $sports->where('name', 'Baloncesto')->first()?->id ?? $sports->first()->id,
                'registration_fee' => 450.00,
                'match_fee_per_team' => 45.00,
                'penalty_fee' => 80.00,
                'referee_payment' => 60.00,
                'status' => 'active',
            ],
            [
                'name' => 'Liga Juvenil de Voleibol',
                'description' => 'Liga para equipos juveniles de voleibol',
                'sport_id' => $sports->where('name', 'Voleibol')->first()?->id ?? $sports->first()->id,
                'registration_fee' => 300.00,
                'match_fee_per_team' => 30.00,
                'penalty_fee' => 50.00,
                'referee_payment' => 40.00,
                'status' => 'active',
            ],
            [
                'name' => 'Copa Abierta de Tenis',
                'description' => 'Torneo abierto de tenis individual y dobles',
                'sport_id' => $sports->where('name', 'Tenis')->first()?->id ?? $sports->first()->id,
                'registration_fee' => 200.00,
                'match_fee_per_team' => 0.00,
                'penalty_fee' => 25.00,
                'referee_payment' => 50.00,
                'status' => 'draft',
            ],
        ];

        foreach ($leagues as $leagueData) {
            League::create([
                'name' => $leagueData['name'],
                'slug' => Str::slug($leagueData['name']),
                'sport_id' => $leagueData['sport_id'],
                'admin_id' => $admin->id,
                'description' => $leagueData['description'],
                'registration_fee' => $leagueData['registration_fee'],
                'match_fee_per_team' => $leagueData['match_fee_per_team'],
                'penalty_fee' => $leagueData['penalty_fee'],
                'referee_payment' => $leagueData['referee_payment'],
                'status' => $leagueData['status'],
            ]);
        }

        $this->command->info('✅ Ligas creadas exitosamente!');
    }
}
