<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateAssignedLeagues extends Command
{
    protected $signature = 'user:update-leagues {userId} {leagueIds}';
    protected $description = 'Actualizar el campo assigned_leagues de un usuario';

    public function handle()
    {
        $userId = $this->argument('userId');
        $leagueIds = $this->argument('leagueIds');

        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado.");
            return 1;
        }

        $user->assigned_leagues = $leagueIds;
        $user->save();

        $this->info("Campo assigned_leagues actualizado correctamente para el usuario {$userId}.");
        $this->info("Valor asignado: {$leagueIds}");
        
        return 0;
    }
}
