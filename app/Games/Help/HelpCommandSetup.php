<?php

namespace App\Games\Help;

use App\Contracts\Discord\CommandSetup;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use React\Promise\Promise;

class HelpCommandSetup implements CommandSetup
{
    /**
     * @inheritdoc
     */
    public function __invoke(Discord $discord): Promise
    {
        return $discord->application->commands->save(
            $discord->application->commands->create(
                CommandBuilder::new()
                    ->setName('help')
                    ->setDescription('This will show you anything I can help you with.')
                    ->toArray()
            )
        );
    }
}
