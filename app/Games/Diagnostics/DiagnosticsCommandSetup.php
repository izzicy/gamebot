<?php

namespace App\Games\Diagnostics;

use App\Contracts\Discord\CommandSetup;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use React\Promise\Promise;

class DiagnosticsCommandSetup implements CommandSetup
{
    /**
     * @inheritdoc
     */
    public function __invoke(Discord $discord): Promise
    {
        return $discord->application->commands->save(
            $discord->application->commands->create(CommandBuilder::new()
                ->setName('health')
                ->setDescription('Display the bot\'s soft/hardware diagnostics.')
                ->toArray()
            )
        );
    }
}
