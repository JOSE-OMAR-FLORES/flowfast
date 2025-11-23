<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MarkOverdueIncomesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $today = Carbon::today();

            // Buscar ingresos pendientes o pagados por equipo que ya vencieron
            $overdueIncomes = Income::whereIn('payment_status', ['pending', 'paid_by_team'])
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->get();

            $count = 0;
            foreach ($overdueIncomes as $income) {
                try {
                    $income->markAsOverdue();
                    $count++;
                    Log::info("Income {$income->id} marked as overdue");
                } catch (\Exception $e) {
                    Log::error("Error marking income {$income->id} as overdue: " . $e->getMessage());
                }
            }

            Log::info("Marked {$count} incomes as overdue");

        } catch (\Exception $e) {
            Log::error("Error in MarkOverdueIncomesJob: " . $e->getMessage());
            throw $e;
        }
    }
}
