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
    public function register(Command $command)
    {
        $this->discord->listenCommand($command->name, fn (...$args) => $this->emit($command->name, $args));
    }
}
