<?php

namespace App\Games\Snowflake;

use App\Contracts\Discord\CommandSetup;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Option;
use React\Promise\Promise;

class SnowflakeCommandSetup implements CommandSetup
{
    /**
     * @inheritdoc
     */
    public function __invoke(Discord $discord): Promise
    {
        $snowflake = (new Option($discord))
            ->setType(Option::STRING)
            ->setRequired(true)
            ->setName('snowflake')
            ->setMaxLength(20)
            ->setDescription('The snowflake from which timestamp must be extracted.');

        return $discord->application->commands->save(
            $discord->application->commands->create(CommandBuilder::new()
                ->setName('snowflake')
                ->setDescription('Read the time from a Discord snowflake.')
                ->addOption($snowflake)
                ->toArray()
            )
        );
    }
}
