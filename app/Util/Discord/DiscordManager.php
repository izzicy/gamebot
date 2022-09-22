<?php

namespace App\Util\Discord;

use App\Contracts\Discord\DiscordManager as DiscordManagerContract;
use Discord\Discord;
use Discord\Helpers\Deferred;
use React\Promise\PromiseInterface;

class DiscordManager implements DiscordManagerContract
{
    /**
     * The Discord promise.
     *
     * @var PromiseInterface
     */
    protected $promise;

    /**
     * The Discord instance.
     *
     * @var Discord
     */
    protected $discord;

    /**
     * Construct a new Discord manager.
     */
    public function __construct()
    {
        $discord = new Discord([
            'token' => config('discord.token'),
        ]);

        $deferred = new Deferred();

        $discord->on('ready', function() use ($deferred, $discord) {
            $deferred->resolve($discord);
        });

        $this->promise = $deferred->promise();
        $this->discord = $discord;
    }

    /**
     * @inheritdoc
     */
    public function get($name = null)
    {
        return $this->promise;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->discord->run();
    }
}
