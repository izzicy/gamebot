<?php

namespace App\Games\WhoAmI;

use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;

class WhoAmIRoutine implements Routine
{
    use HasId;

    /**
     * Construct whoami routine.
     *
     * @param Discord $discord
     * @param InteractionDispatcher $dispatcher
     */
    public function __construct(
        protected Discord $discord,
        protected InteractionDispatcher $dispatcher,
    )
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['main', 'whoami'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->dispatcher->register('whoami');
        $this->dispatcher->on('whoami', [$this, 'onCommand']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->dispatcher->removeListener('whoami', [$this, 'onCommand']);
    }

    /**
     * On message callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onCommand(Interaction $interaction)
    {
        $user = $interaction->user;

        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->setContent("Your name is $user.")
                ->setAllowedMentions(['users' => [123]])
        );
    }
}
