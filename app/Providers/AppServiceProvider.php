<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Contracts\Routines\Repository::class, \App\Routines\Repository::class);
        $this->app->singleton(\App\Contracts\Schedule\Scheduler::class, \App\Utils\Schedule\ReactPHPScheduler::class);
    }
}
