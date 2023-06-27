<?php

namespace App\Discord;

use App\Discord\Contracts\InteractionDispatcher as InteractionDispatcherContract;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command;
use Evenement\EventEmitter;

class InteractionDispatcher extends EventEmitter implements InteractionDispatcherContract
{
    /**
     * Create a new event emitter instance.
     *
     * @param Discord $discord
     * @return void
     */
    public function __construct(
        protected Discord $discord,
    )
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function register($command)
    {
        $commandName = is_string($command) ? $command : $command->name;

        $this->discord->listenCommand($commandName, fn (...$args) => $this->emit($commandName, $args));
    }
}
