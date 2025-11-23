<?php

namespace App\Console\Commands\Financial;

use Illuminate\Console\Command;
use App\Models\Fixture;
use App\Jobs\GenerateMatchFeesJob;
use Carbon\Carbon;

class GenerateMatchFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financial:generate-match-fees {--fixture_id=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate match fees for finished fixtures';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏆 Generating match fees...');

        $query = Fixture::where('status', 'finished');

        // Filtrar por fixture específico si se proporciona
        if ($this->option('fixture_id')) {
            $query->where('id', $this->option('fixture_id'));
        }

        // Filtrar por fecha si se proporciona
        if ($this->option('date')) {
            $date = Carbon::parse($this->option('date'));
            $query->whereDate('match_date', $date);
        } else {
            // Por defecto, solo partidos de los últimos 7 días
            $query->where('match_date', '>=', Carbon::now()->subDays(7));
        }

        $fixtures = $query->get();

        if ($fixtures->isEmpty()) {
            $this->warn('No finished fixtures found matching criteria.');
            return 0;
        }

        $bar = $this->output->createProgressBar($fixtures->count());
        $bar->start();

        $generated = 0;
        foreach ($fixtures as $fixture) {
            try {
                GenerateMatchFeesJob::dispatch($fixture);
                $generated++;
            } catch (\Exception $e) {
                $this->error("\nError processing fixture {$fixture->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Successfully dispatched {$generated} match fee generation jobs.");

        return 0;
    }
}
