<?php

namespace App\Games\ZeroDollarGame;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Games\ZeroDollarGame\Models\Game;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Command\Option;
use Discord\WebSockets\Event;
use Illuminate\Database\Eloquent\Collection;

class ZeroDollarGameRoutine implements Routine
{
    use HasId;

    /**
     * All active games.
     *
     * @var Collection
     */
    protected $games;

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
        $this->games = Game::query()->get();
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['zero_dollar_game', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        foreach ($this->games as $game) {
            $this->repository->create(OngoingGame::class, [
                'game' => $game,
            ])->initialize();
        }

        $this->dispatcher->register('paint');
        $this->discord->on(Event::MESSAGE_CREATE, [$this, 'onMessage']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->discord->removeListener(Event::MESSAGE_CREATE, [$this, 'onMessage']);

        $this->repository->destroyByTags(['zero_dollar_game', 'subroutine']);
    }

    /**
     * On message callback.
     *
     * @param Message $message
     * @return void
     */
    public function onMessage(Message $message)
    {
        if ( ! in_array($message->channel_id, config('zero-dollar-game.allowed_channels'))) {
            return;
        }

        if (
            preg_match('/(initialize|start|begin) zero dollar game/i', $message->content)
        ) {
            if ($this->repository->tagsExists(['zero_dollar_game', 'channel:' . $message->channel_id])) {
                $message->channel->sendMessage("I'm already playing a Zero Dollar Game in this channel!");
            } else {
                $game = Game::query()->create([
                    'channel_id' => $message->channel_id,
                    'width' => 90,
                    'height' => 90,
                ]);

                $this->repository->create(OngoingGame::class, [
                    'game' => $game,
                    'createdRecently' => true,
                ])->initialize();
            }
        }

        if (str_contains($message->content, 'emergency_shutdown')) {
            $message->channel->sendMessage('Game has stopped.');

            $this->repository->destroyByTags(['zero_dollar_game', 'subroutine']);
        }
    }
}
