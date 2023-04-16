<?php

namespace App\Games\ChooseYourDoor;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Games\ChooseYourDoor\Models\Game;
use App\Routines\Concerns\HasId;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

use function React\Promise\Timer\sleep;

class ChooseYourDoorRoutine implements Routine
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
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
    )
    {
        $this->games = Game::query()->get();
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['choose_your_door', 'main'];
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

        $this->discord->on(Event::MESSAGE_CREATE, [$this, 'onMessage']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->discord->removeListener(Event::MESSAGE_CREATE, [$this, 'onMessage']);

        $this->repository->destroyByTags(['choose_your_door', 'subroutine']);
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
            preg_match('/(initialize|start|begin) choose your door/i', $message->content)
        ) {
            if ($this->repository->tagsExists(['zero_dollar_game', 'channel:' . $message->channel_id])) {
                $message->channel->sendMessage("I'm already playing a Choose Your Door game in this channel!");
            } else {
                $game = Game::query()->create([
                    'channel_id' => $message->channel_id,
                ]);

                $this->repository->create(OngoingGame::class, [
                    'game' => $game,
                ])->initialize();
            }
        }
    }
}
