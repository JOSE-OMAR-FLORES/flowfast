<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el perfil de administrador
        $admin = Admin::create([
            'first_name' => 'Super',
            'last_name' => 'Administrador',
            'phone' => '+1234567890',
            'company_name' => 'FlowFast SaaS',
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addYear(),
        ]);

        // Crear el usuario del sistema
        User::create([
            'email' => 'admin@flowfast.com',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'userable_id' => $admin->id,
            'userable_type' => Admin::class,
            'email_verified_at' => now(),
        ]);
    }
}
