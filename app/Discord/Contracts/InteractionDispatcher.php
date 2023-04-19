<?php

namespace App\Discord\Contracts;

use Discord\Parts\Interactions\Command\Command;
use Evenement\EventEmitterInterface;

interface InteractionDispatcher extends EventEmitterInterface
{
    /**
     * Register the command.
     *
     * @param Command $command
     * @return void
     */
    public function register(Command $command);
}
