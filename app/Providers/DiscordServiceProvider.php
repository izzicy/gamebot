<?php

namespace App\Providers;

use Discord\Discord;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
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
        $this->app->singleton(Discord::class, function() {
            return new Discord([
                'token' => config('discord.token'),
            ]);
        });
    }
}
