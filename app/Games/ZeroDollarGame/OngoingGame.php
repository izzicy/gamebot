<?php

namespace App\Games\ZeroDollarGame;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Games\ZeroDollarGame\Models\Game;
use App\Routines\Concerns\HasId;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class OngoingGame implements Routine
{
    use HasId;

    /**
     * An ongoing game instance.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param Game $game
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected Game $game,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['zero_dollar_game', 'subroutine', 'channel:' . $this->game->channel_id];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
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
        if (str_contains($message->content, 'test456')) {
            $message->channel->sendMessage('Pretend a ZDG is active lol');
        }
    }
}
