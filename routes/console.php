<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\MarkOverdueIncomesJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar Jobs Financieros
Schedule::job(new MarkOverdueIncomesJob())
    ->daily()
    ->at('00:00')
    ->name('mark-overdue-incomes')
    ->withoutOverlapping();
