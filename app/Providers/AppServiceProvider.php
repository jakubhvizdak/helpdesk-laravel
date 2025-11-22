<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\TaskEditLogObserver;
use App\Models\TaskEditLog;

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
        TaskEditLog::observe(TaskEditLogObserver::class);
    }
}
