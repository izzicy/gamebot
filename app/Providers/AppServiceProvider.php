<?php

namespace App\Providers;

use Illuminate\Support\Arr;
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
        Arr::macro('shuffleAssoc', function($list) {
            if (!is_array($list)) return $list;

            $keys = array_keys($list);
            shuffle($keys);
            $random = array();
            foreach ($keys as $key) {
              $random[$key] = $list[$key];
            }
            return $random;
        });
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
