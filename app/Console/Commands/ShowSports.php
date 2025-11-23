<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sport;

class ShowSports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'show:sports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all available sports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sports = Sport::select('id', 'name', 'slug')->get();
        
        $this->info('Deportes disponibles:');
        
        foreach($sports as $sport) {
            $this->line("ID: {$sport->id} - {$sport->name} ({$sport->slug})");
        }
    }
}
