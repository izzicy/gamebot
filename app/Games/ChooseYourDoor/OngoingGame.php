<?php

namespace App\Games\ChooseYourDoor;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Contracts\Schedule\Scheduler;
use App\Games\ChooseYourDoor\Models\Game;
use App\Routines\Concerns\HasId;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Arr;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;
use function React\Promise\Timer\sleep;

class OngoingGame implements Routine
{
    use HasId;

    /**
     * An ongoing game instance.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param Game $game
     * @param PromptImageCreator $promptImageCreator
     * @param Scheduler $scheduler
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected Game $game,
        protected PromptImageCreator $promptImageCreator,
        protected Scheduler $scheduler,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['choose_your_door', 'subroutine', 'channel:' . $this->game->channel_id];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        foreach (config('choose-your-door.playtimes') as $playtime) {
            $this->runInterval($playtime);
        }
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->repository->destroyByTags(['zero_dollar_game', 'subroutine']);
    }

    /**
     * Run the interval.
     *
     * @param string $playtime
     * @return void
     */
    protected function runInterval($playtime)
    {
        if ( ! $this->game->last_message_id) {
            $this->postNewGame()->then(fn () => $this->runInterval($playtime));
        } else {
            // \React\Promise\Timer\sleep(5)
            //     ->then(fn () => $this->updateGame())
            //     ->then(fn () => $this->runInterval($playtime));

            $this->scheduler->atTime($playtime)
                ->then(fn () => $this->updateGame())
                ->then(fn () => $this->runInterval($playtime));
        }
    }

    /**
     * Update the game.
     *
     * @return PromiseInterface
     */
    protected function updateGame()
    {
        return $this->handlePreviousGame()
            ->then(
                fn () => $this->postNewGame(),
                fn () => $this->postNewGame(),
            );
    }

    /**
     * Handle the previous game.
     *
     * @return PromiseInterface
     */
    protected function handlePreviousGame()
    {
        if ($this->game->last_message_id) {
            return $this->repository->create(ReactionRoutine::class, [
                'game' => $this->game,
            ])->initialize() ?? resolve();
        }

        return resolve();
    }

    /**
     * Post a new game.
     *
     * @return PromiseInterface
     */
    protected function postNewGame()
    {
        $reactionCount = rand(2, 6);

        return $this->discord->getChannel($this->game->channel_id)
            ->sendMessage(
                MessageBuilder::new()
                    ->addFile(
                        $this->promptImageCreator->create($reactionCount)
                    )
                    ->setContent(Arr::random(__('choose-your-door.prompt')))
            )
            ->then(function(Message $message) use ($reactionCount) {
                $this->updateLastMessage($message, $reactionCount);
                return $this->addBaseReactions($message, $reactionCount);
            });

    }

    /**
     * Update the last message.
     *
     * @param Message $message
     * @param integer $reactionCount
     * @return void
     */
    protected function updateLastMessage(Message $message, int $reactionCount)
    {
        $this->game->last_message_id = $message->id;
        $this->game->last_door_count = $reactionCount;
        $this->game->save();
    }

    /**
     * Add the base reactions.
     *
     * @param Message $message
     * @param integer $reactionCount
     * @return PromiseInterface
     */
    protected function addBaseReactions(Message $message, int $reactionCount)
    {
        $emojis = array_slice(config('choose-your-door.reactions'), 0, $reactionCount);
        $lastPromise = resolve();

        foreach ($emojis as $emoji) {
            $lastPromise = $lastPromise->then(fn () => $message->react($emoji));
        }

        return $lastPromise;
    }
}
