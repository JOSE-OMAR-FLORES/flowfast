<?php

namespace App\Console\Commands\Financial;

use Illuminate\Console\Command;
use App\Jobs\MarkOverdueIncomesJob;

class MarkOverdueIncomes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financial:mark-overdue-incomes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark incomes as overdue based on due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('⏰ Marking overdue incomes...');

        try {
            // Ejecutar el job directamente (sincrónico para ver el resultado)
            $job = new MarkOverdueIncomesJob();
            $job->handle();

            $this->info('✅ Overdue incomes marked successfully.');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error marking overdue incomes: ' . $e->getMessage());
            return 1;
        }
    }
}
