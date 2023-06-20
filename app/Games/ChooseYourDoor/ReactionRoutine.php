<?php

namespace App\Games\ChooseYourDoor;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Games\ChooseYourDoor\Contracts\PhraseFactory;
use App\Games\ChooseYourDoor\Models\Game;
use App\Routines\Concerns\HasId;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Channel\Reaction;
use Discord\Parts\User\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use React\Promise\PromiseInterface;

use function React\Promise\all;

class ReactionRoutine implements Routine
{
    use HasId;

    /**
     * The correct choices.
     *
     * @var array
     */
    protected $correctChoices;

    /**
     * An ongoing game instance.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param PhraseFactory $phraseFactory
     * @param ResultsImageCreator $resultsImageCreator
     * @param Game $game
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected PhraseFactory $phraseFactory,
        protected ResultsImageCreator $resultsImageCreator,
        protected Game $game,
    )
    {
        $choices = Collection::range(0, $game->last_door_count - 1);

        $this->correctChoices = $choices->random(rand(1, $game->last_door_count))->all();
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['choose_your_door', 'reaction', 'subroutine', 'channel:' . $this->game->channel_id];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $promise = $this->createReactionCollectorPromise();

        $promise = $promise->then(function($reactions) {
            $filePath = $this->resultsImageCreator->create($reactions, $this->correctChoices, $this->game->last_door_count);
            $message = $this->createMessage($reactions);

            return $this->discord->getChannel($this->game->channel_id)->sendMessage(
                MessageBuilder::new()
                    ->setAllowedMentions(['users' => [123]])
                    ->addFile($filePath)
                    ->setContent($message)
            );
        });

        return $promise->then(
            fn () => $this->repository->destroy($this),
            fn () => $this->repository->destroy($this),
        );
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
    }

    /**
     * Create the message.
     *
     * @param ReactionCollector $reactions
     * @return string
     */
    protected function createMessage(ReactionCollector $reactions)
    {
        $generator = $this->phraseFactory->createPhraseGenerator();
        $usersByReactions = $reactions->getUsersByReactions();
        $phrases = [];
        $phrasesByReaction = [];
        $shuffledReactions = Arr::shuffleAssoc($usersByReactions);

        foreach ($shuffledReactions as $reaction => $users) {
            $usersnames = collect($users)->map(fn ($user) => "<@$user->id>")->values()->all();
            $isWon = in_array($reaction, $this->correctChoices);
            $isCheater = $reaction === 'cheater';

            if ($isCheater) {
                $phrasesByReaction[$reaction] = $generator->make($usersnames, 'CHEATER');
            } else if ($isWon) {
                $phrasesByReaction[$reaction] = $generator->make($usersnames, 'WIN');
            } else {
                $phrasesByReaction[$reaction] = $generator->make($usersnames, 'LOSE');
            }
        }

        foreach ($usersByReactions as $reaction => $users) {
            $phrases[] = $phrasesByReaction[$reaction];
        }

        return implode("\n", $phrases);
    }

    /**
     * Create the reaction collector promise.
     *
     * @return PromiseInterface
     */
    protected function createReactionCollectorPromise()
    {
        $channel = $this->discord->getChannel(
            $this->game->channel_id
        );

        $promise = $channel->messages->fetch(
            $this->game->last_message_id
        )->then(fn ($message) => [$message, new ReactionCollector]);

        $emojis = array_slice(config('choose-your-door.reactions'), 0, $this->game->last_door_count);

        foreach ($emojis as $key => $emoji) {
            $promise = $promise->then(function ($data) use ($emoji) {
                /** @var Message $message */
                list ($message, $collector) = $data;

                return all([$message, $collector, $message->reactions->fetch($emoji)]);
            })->then(function($data) {
                /** @var Message $message */
                /** @var Reaction $reaction */
                list ($message, $collector, $reaction) = $data;

                return all([$message, $collector, $reaction->getAllUsers()]);
            })->then(function($data) use ($key) {
                /** @var Message $message */
                /** @var ReactionCollector $collector */
                /** @var User[] $users */
                list ($message, $collector, $users) = $data;

                foreach ($users as $user) {
                    if ($user->bot) continue;

                    $collector->addReaction($user, $key);
                }

                return [$message, $collector];
            });
        }

        return $promise->then(fn ($data) => $data[1]);
    }
}
