<?php

namespace App\Games\WhoAmI;

use App\Contracts\Discord\CommandSetup;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use React\Promise\Promise;

class WhoAmICommandSetup implements CommandSetup
{
    /**
     * @inheritdoc
     */
    public function __invoke(Discord $discord): Promise
    {
        return $discord->application->commands->save(
            $discord->application->commands->create(CommandBuilder::new()
                ->setName('whoami')
                ->setDescription('Tells who you are.')
                ->toArray()
            )
        );
    }
}
