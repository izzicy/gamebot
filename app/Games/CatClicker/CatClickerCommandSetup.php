<?php

namespace App\Games\CatClicker;

use App\Contracts\Discord\CommandSetup;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use React\Promise\Promise;

class CatClickerCommandSetup implements CommandSetup
{
    /**
     * @inheritdoc
     */
    public function __invoke(Discord $discord): Promise
    {
        return $discord->application->commands->save(
            $discord->application->commands->create(CommandBuilder::new()
                ->setName('cat_clicker')
                ->setDescription('Play the cat clicker game.')
                ->toArray()
            )
        );
    }
}
