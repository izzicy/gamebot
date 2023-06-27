<?php

namespace App\Games\Diagnostics;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

class DiagnosticsRoutine implements Routine
{
    use HasId;

    /**
     * Construct the zero dollar game routine.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param InteractionDispatcher $dispatcher
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
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
        return ['diagnostics', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->dispatcher->register('health');
        $this->dispatcher->on('health', [$this, 'onCommand']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->dispatcher->on('health', [$this, 'onCommand']);
    }

    /**
     * On command callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onCommand(Interaction $interaction)
    {
        if ( ! in_array($interaction->user->id, config('discord.admins'))) {
            $interaction->respondWithMessage(
                MessageBuilder::new()
                    ->setContent("You don't have sufficient privileges.")
            );

            return;
        }

        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->setContent(
                    shell_exec('vcgencmd measure_temp') ?? '*Operation failed.*'
                ),
            true
        );
    }
}
