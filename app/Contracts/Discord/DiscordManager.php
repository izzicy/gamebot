<?php

namespace App\Contracts\Discord;

use React\Promise\PromiseInterface;

interface DiscordManager
{
    /**
     * Get the Discord instance.
     *
     * @param string|null $name
     * @return PromiseInterface
     *
     * @throws DiscordNotFoundException
     */
    public function get($name = null);

    /**
     * Run all bots.
     *
     * @return void
     */
    public function run();
}
