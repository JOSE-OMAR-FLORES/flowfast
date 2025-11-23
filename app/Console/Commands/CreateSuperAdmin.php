<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=admin@flowfast.com} {--password=FlowFast2025!}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un usuario Super Administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');

        // Verificar si ya existe
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Ya existe un usuario con el email: {$email}");
            return 1;
        }

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
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
            'user_type' => 'admin',
            'userable_id' => $admin->id,
            'userable_type' => Admin::class,
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Super Administrador creado exitosamente!");
        $this->line("");
        $this->line("📧 Email: {$email}");
        $this->line("🔑 Password: {$password}");
        $this->line("");
        $this->warn("⚠️  Cambia la contraseña después del primer inicio de sesión");

        return 0;
    }
}
