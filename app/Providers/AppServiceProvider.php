<?php

namespace App\Providers;

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
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set queue configuration for transcription jobs
        $this->app['queue']->after(function () {
            $this->app['db']->disconnect();
        });

        // Set longer timeout for queue workers
        $this->app['queue']->looping(function () {
            set_time_limit(1200); // 20 minutes
        });
    }
}
