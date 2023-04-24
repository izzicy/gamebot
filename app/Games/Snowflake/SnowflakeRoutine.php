<?php

namespace App\Games\Snowflake;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Carbon;

class SnowflakeRoutine implements Routine
{
    use HasId;

    protected const DISCORD_EPOCH = 1420070400000;

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
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['rock_paper_scissors', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $snowflake = (new Option($this->discord))
            ->setType(Option::STRING)
            ->setRequired(true)
            ->setName('snowflake')
            ->setMaxLength(20)
            ->setDescription('The snowflake from which timestamp must be extracted.');

        $this->discord->application->commands->save(
            $this->discord->application->commands->create(CommandBuilder::new()
                ->setName('snowflake')
                ->setDescription('Read the time from a Discord snowflake.')
                ->addOption($snowflake)
                ->toArray()
            )
        )->then([$this->dispatcher, 'register']);

        $this->dispatcher->on('snowflake', [$this, 'onCommand']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->dispatcher->removeListener('rps', [$this, 'onCommand']);
    }

    /**
     * On message callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onCommand(Interaction $interaction)
    {
        $snowflake = $interaction->data->options->offsetGet('snowflake')->value;

        if ( ! preg_match('/^\d+$/', $snowflake)) {
            $interaction->respondWithMessage(
                MessageBuilder::new()
                    ->setContent('The snowflake must be a positive integer.'),
                true
            );
        } else {
            $time = (((int) $snowflake) >> 22) + static::DISCORD_EPOCH;

            $carbon = Carbon::parse($time / 1000);

            $interaction->respondWithMessage(
                MessageBuilder::new()
                    ->setContent($carbon->format('Y-m-d H:i:s') . ' UTC'),
                true
            );
        }
    }
}
