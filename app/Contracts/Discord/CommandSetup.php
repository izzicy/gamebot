<?php

namespace App\Contracts\Discord;

use Discord\Discord;
use React\Promise\Promise;

interface CommandSetup
{
    /**
     * Setup this command.
     *
     * @param Discord $discord
     * @return Promise
     */
    public function __invoke(Discord $discord): Promise;
}
