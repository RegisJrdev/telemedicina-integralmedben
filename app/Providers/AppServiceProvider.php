<?php

namespace App\Providers;

use App\Events\PatientCreated;
use App\Listeners\SyncPatientToCentral;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Vite::prefetch(concurrency: 3);

        Event::listen(PatientCreated::class, SyncPatientToCentral::class);
    }
}
