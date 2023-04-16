<?php

namespace App\Providers;

use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use App\Games\ChooseYourDoor\Phrases\GeneralPhraseGenerator;
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
        $this->app->singleton(PhraseGenerator::class, GeneralPhraseGenerator::class);
    }
}
