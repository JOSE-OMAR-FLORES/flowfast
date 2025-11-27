<?php

namespace App\Console\Commands;

use Database\Seeders\ProductionTestSeeder;
use Illuminate\Console\Command;

class SeedProductionTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flowfast:seed-test-data {admin_id : El ID del admin al que se asociarán los datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea datos de prueba para producción: 4 ligas, 10 equipos cada una, 5 jugadores por equipo';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $adminId = (int) $this->argument('admin_id');

        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║        FlowFast - Generador de Datos de Prueba               ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        if (!$this->confirm("¿Deseas crear datos de prueba para el admin ID: {$adminId}?", true)) {
            $this->warn('Operación cancelada.');
            return Command::SUCCESS;
        }

        $seeder = new ProductionTestSeeder();
        $seeder->setContainer(app());
        $seeder->setCommand($this);
        $seeder->run($adminId);

        return Command::SUCCESS;
    }
}
