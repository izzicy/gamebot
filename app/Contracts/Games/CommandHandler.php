<?php


namespace App\Contracts\Games;

interface CommandHandler
{
    /**
     * Handle the given commands.
     *
     * @param \Illuminate\Support\Collection $invocations
     * @return \Illuminate\Support\Collection
     */
    public function handle($invocations);
}
