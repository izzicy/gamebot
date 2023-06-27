<?php

namespace App\Games\RockPaperScissors;

use App\Contracts\Discord\CommandSetup;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Choice;
use Discord\Parts\Interactions\Command\Option;
use React\Promise\Promise;

class RockPaperScissorsCommandSetup implements CommandSetup
{
    /**
     * @inheritdoc
     */
    public function __invoke(Discord $discord): Promise
    {
        $choice = (new Option($discord))
            ->setType(Option::STRING)
            ->setName('choice')
            ->setDescription('Choose either rock, paper, or scissors.')
            ->addChoice((new Choice($discord))->setName('rock')->setValue('rock'))
            ->addChoice((new Choice($discord))->setName('paper')->setValue('paper'))
            ->addChoice((new Choice($discord))->setName('scissors')->setValue('scissors'))
            ->setRequired(true);

        $user = (new Option($discord))
            ->setType(Option::USER)
            ->setName('user')
            ->setDescription('Select an user to challenge or leave empty to play with the bot.');

        return $discord->application->commands->save(
            $discord->application->commands->create(CommandBuilder::new()
                ->setName('rps')
                ->setDescription('Play a game of Rock Paper Scissors.')
                ->addOption($choice)
                ->addOption($user)
                ->toArray()
            )
        );
    }
}
