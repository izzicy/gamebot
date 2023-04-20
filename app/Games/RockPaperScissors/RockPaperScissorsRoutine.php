<?php

namespace App\Games\RockPaperScissors;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Choice;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Faker\Provider\ar_EG\Internet;
use Illuminate\Support\Arr;

class RockPaperScissorsRoutine implements Routine
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
        $choice = (new Option($this->discord))
            ->setType(Option::STRING)
            ->setName('choice')
            ->setDescription('Choose either rock, paper, or scissors.')
            ->addChoice((new Choice($this->discord))->setName('rock')->setValue('rock'))
            ->addChoice((new Choice($this->discord))->setName('paper')->setValue('paper'))
            ->addChoice((new Choice($this->discord))->setName('scissors')->setValue('scissors'))
            ->setRequired(true);

        $user = (new Option($this->discord))
            ->setType(Option::USER)
            ->setName('user')
            ->setDescription('Select an user to challenge or leave empty to play with the bot.');

        $this->discord->application->commands->save(
            $this->discord->application->commands->create(CommandBuilder::new()
                ->setName('rps')
                ->setDescription('Play a game of Rock Paper Scissors.')
                ->addOption($choice)
                ->addOption($user)
                ->toArray()
            )
        )->then([$this->dispatcher, 'register']);

        $this->dispatcher->on('rps', [$this, 'onCommand']);
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
        $user = $interaction->data->resolved?->users?->first();
        $choice = $interaction->data->options->offsetGet('choice')->value;

        if ( ! $user) {
            $this->respondWithBotPick($interaction, $choice);
        } else {
            $interaction->respondWithMessage(
                MessageBuilder::new()
                    ->setContent('This feature is yet to be implemented...'),
                true
            );
        }
    }

    /**
     * Response with a bot pick.
     *
     * @param Interaction $interaction
     * @param string $choice
     * @return void
     */
    protected function respondWithBotPick(Interaction $interaction, $choice)
    {
        $botPick = Arr::random(['rock', 'paper', 'scissors']);
        $botLabel = $this->getLabel($botPick);
        $botEmoji = $this->getEmoji($botPick);

        $userLabel = $this->getLabel($choice);
        $userEmoji = $this->getEmoji($choice);

        $userText = "Your choice was ***$userLabel***. $userEmoji";
        $botText = "I picked ***$botLabel***. $botEmoji";
        $conclusion = "It's a draw!";

        $winner = $this->determineWinner('USER', 'BOT', $choice, $botPick);

        if ($winner === 'BOT') {
            $conclusion = "I win! \u{1F389}";
        }

        if ($winner === 'USER') {
            $conclusion = "You win! \u{1F389}";
        }

        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->setContent(implode("\n", [$userText, $botText, $conclusion]))
        );
    }

    /**
     * Determine a winner.
     *
     * @param string $player1
     * @param string $player2
     * @param string $player1Choice
     * @param string $player2Choice
     * @return string
     */
    protected function determineWinner($player1, $player2, $player1Choice, $player2Choice)
    {
        if ($player1Choice === $player2Choice) {
            return 'DRAW';
        }

        if ($player1Choice === 'paper') {
            return $player2Choice === 'rock' ? $player1 : $player2;
        }

        if ($player1Choice === 'rock') {
            return $player2Choice === 'scissors' ? $player1 : $player2;
        }

        if ($player1Choice === 'scissors') {
            return $player2Choice === 'paper' ? $player1 : $player2;
        }
    }

    /**
     * Get the emoji associated with this choice.
     *
     * @param string $choice
     * @return string
     */
    protected function getEmoji($choice)
    {
        return [
            'rock' => "\u{1FAA8}",
            'paper' => "\u{1F4C4}",
            'scissors' => "\u{2702}",
        ][$choice];
    }

    /**
     * Get the label associated with this choice.
     *
     * @param string $choice
     * @return string
     */
    protected function getLabel($choice)
    {
        return [
            'rock' => "Rock",
            'paper' => "Paper",
            'scissors' => "Scissors",
        ][$choice];
    }
}
