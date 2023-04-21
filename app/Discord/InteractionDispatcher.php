<?php

namespace App\Discord;

use App\Discord\Contracts\InteractionDispatcher as InteractionDispatcherContract;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
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
        $this->discord->listenCommand($command->name, fn (...$args) => $this->handleCommand($command, $args[0], $args));
    }

    /**
     * Handle the command.
     *
     * @param Command $command
     * @param Interaction $interaction
     * @param mixed[] $args
     * @return void
     */
    protected function handleCommand(Command $command, Interaction $interaction, $args)
    {
        $inDevelopment = config('app.env') === 'development';
        $isTestaccount = config('testing.account') === $interaction->user->id;

        if (
            ($inDevelopment && $isTestaccount)
            || ( ! $inDevelopment && ! $isTestaccount)
        ) {
            $this->emit($command->name, $args);
        }
    }
}
