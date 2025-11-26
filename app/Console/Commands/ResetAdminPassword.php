<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:reset-password {email?} {password?}';

    /**
     * The console command description.
     */
    protected $description = 'Reset admin password or create admin if not exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@flowfast.com';
        $password = $this->argument('password') ?? 'Admin123!';

        $user = User::where('email', $email)->first();

        if ($user) {
            // Resetear contraseña
            $user->password = Hash::make($password);
            $user->save();
            
            $this->info("✅ Contraseña reseteada exitosamente!");
            $this->info("   Email: {$email}");
            $this->info("   Nueva contraseña: {$password}");
            $this->info("   Tipo: {$user->user_type}");
        } else {
            // Crear nuevo admin
            $admin = \App\Models\Admin::create([
                'name' => 'Admin',
                'phone' => '0000000000',
            ]);

            $user = User::create([
                'email' => $email,
                'password' => Hash::make($password),
                'user_type' => 'admin',
                'userable_id' => $admin->id,
                'userable_type' => \App\Models\Admin::class,
            ]);

            $this->info("✅ Usuario admin creado exitosamente!");
            $this->info("   Email: {$email}");
            $this->info("   Contraseña: {$password}");
        }

        // Mostrar todos los admins
        $this->newLine();
        $this->info("📋 Usuarios admin en el sistema:");
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            $this->line("   - ID: {$admin->id} | Email: {$admin->email}");
        }

        return Command::SUCCESS;
    }
}
