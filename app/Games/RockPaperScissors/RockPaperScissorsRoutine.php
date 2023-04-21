<?php

namespace App\Games\RockPaperScissors;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Routines\Concerns\HasId;
use App\Utils\StringCoder;
use Discord\Builders\CommandBuilder;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Choice;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
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
     * @param StringCoder $stringCoder
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected InteractionDispatcher $dispatcher,
        protected StringCoder $stringCoder,
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
        $this->discord->on(Event::INTERACTION_CREATE, [$this, 'onInteraction']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->dispatcher->removeListener('rps', [$this, 'onCommand']);
        $this->discord->removeListener(Event::INTERACTION_CREATE, [$this, 'onInteraction']);
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
            $this->respondWithUserPick($interaction, $choice);
        }
    }

    /**
     * On interaction callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onInteraction(Interaction $interaction)
    {
        if ($this->stringCoder->is($interaction->data->custom_id, 'rps')) {
            $data = $this->stringCoder->decode($interaction->data->custom_id);
            $player1Id = $data['c'];
            $player2Id = $data['e'];

            if ($player2Id !==  $interaction->user->id) {
                $interaction->respondWithMessage(
                    MessageBuilder::new()
                        ->setContent('This is not your game to play!'),
                    true
                );

                return;
            }

            $player1Pick = $data['p'];
            $player2Pick = $data['y'];
            $player1Label = $this->getLabel($player1Pick);
            $player2Label = $this->getLabel($player2Pick);
            $player1Emoji = $this->getEmoji($player1Pick);
            $player2Emoji = $this->getEmoji($player2Pick);

            $player1Text = "<@{$player1Id}> picked ***$player1Label***. $player1Emoji";
            $player2Text = "<@{$player2Id}> picked ***$player2Label***. $player2Emoji";
            $conclusion = "It's a draw!";

            $winner = $this->determineWinner($player1Id, $player2Id, $player1Pick, $player2Pick);;

            if ($winner === $player1Id) {
                $conclusion = "<@{$player1Id}> wins! \u{1F389}";
            }

            if ($winner === $player2Id) {
                $conclusion = "<@{$player2Id}> wins! \u{1F389}";
            }

            $action = $this->createPlayerRespondActionRow($player1Id, $player2Id, $player1Pick, true);

            $interaction->message->edit(
                MessageBuilder::new()
                    ->setContent('Thanks for playing!')
                    ->addComponent($action)
            );

            $interaction->respondWithMessage(
                MessageBuilder::new()
                    ->setContent(implode("\n", [$player1Text, $player2Text, $conclusion]))
            );
        }
    }

    /**
     * Respond with an user pick.
     *
     * @param Interaction $interaction
     * @param string $choice
     * @return void
     */
    protected function respondWithUserPick(Interaction $interaction, $choice)
    {
        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->setContent('Challenge has been send!'),
            true
        )->then(function() use ($interaction, $choice) {
            $challenger = $interaction->user;
            $challenged = $interaction->data->resolved->users->first();

            $action = $this->createPlayerRespondActionRow($challenger->id, $challenged->id, $choice);

            $interaction->channel->sendMessage(
                MessageBuilder::new()
                    ->setContent("$challenger challenged $challenged to a Rock Paper Scissors battle!\n$challenged, take your pick!")
                    ->addComponent($action)
            );
        });
    }

    /**
     * Create a player respond action row.
     *
     * @param string $challengerId
     * @param string $challengedId
     * @param string $challengerChoice
     * @param boolean $disabled
     * @return ActionRow
     */
    protected function createPlayerRespondActionRow($challengerId, $challengedId, $challengerChoice, $disabled = false)
    {
        $data = [
            'c' => $challengerId,
            'e' => $challengedId,
            'p' => $challengerChoice,
            't' => time(),
        ];

        $action = ActionRow::new();

        $rockButton = Button::new(
                Button::STYLE_PRIMARY,
                $this->stringCoder->encode('rps', array_merge($data, ['y' => 'rock']))
            )
            ->setDisabled($disabled)
            ->setLabel('Rock')
            ->setEmoji(
                $this->getEmoji('rock')
            );

        $paperButton = Button::new(
                Button::STYLE_PRIMARY,
                $this->stringCoder->encode('rps', array_merge($data, ['y' => 'paper']))
            )
            ->setDisabled($disabled)
            ->setLabel('Paper')
            ->setEmoji(
                $this->getEmoji('paper')
            );

        $scissorsButton = Button::new(
                Button::STYLE_PRIMARY,
                $this->stringCoder->encode('rps', array_merge($data, ['y' => 'scissors']))
            )
            ->setDisabled($disabled)
            ->setLabel('Scissors')
            ->setEmoji(
                $this->getEmoji('scissors')
            );

        return $action
            ->addComponent($rockButton)
            ->addComponent($paperButton)
            ->addComponent($scissorsButton);
    }

    /**
     * Respond with a bot pick.
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
