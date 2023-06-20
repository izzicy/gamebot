<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ChooseYourDoorServiceProvider extends ServiceProvider
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
        $this->app->singleton(\App\Games\ChooseYourDoor\Contracts\PhraseFactory::class, \App\Games\ChooseYourDoor\Phrases\PhraseFactory::class);
    }
}
