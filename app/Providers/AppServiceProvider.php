<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Fixture;
use App\Observers\FixtureObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar Observer para Fixtures
        Fixture::observe(FixtureObserver::class);
    }
}
