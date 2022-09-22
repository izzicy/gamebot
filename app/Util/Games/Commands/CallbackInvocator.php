<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandInvocator;
use Closure;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;

class CallbackInvocator implements CommandInvocator
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $commandName;

    /**
     * The callback.
     *
     * @var Closure
     */
    protected $callback;

    /**
     * The channel id which has to be matches.
     *
     * @var string|null
     */
    protected $channelId;

    /**
     * Create a regex invocation creator.
     *
     * @param string $commandName
     * @param Closure $callback
     * @param string|null $channelId
     */
    public function __construct($commandName, $callback, $channelId = null)
    {
        $this->commandName = $commandName;
        $this->callback = $callback;
        $this->channelId = $channelId;
    }

    /**
     * @inheritdoc
     */
    public function createFromMessage(Message $message)
    {
        $invocations = collect();

        if ($this->channelId !== null && $message->channel_id != $this->channelId) {
            return $invocations;
        }

        $user = $message->author;

        if ($user instanceof Member) {
            $user = $user->user;
        }

        $components = explode("\n", $message->content);

        array_walk(
            $components,
            function($line) use ($invocations, $user, $message) {
                $results = ($this->callback)($line, $user, $message);
                foreach ($results as $result) {
                    $invocations->push($result);
                }
            }
        );

        return $invocations;
    }
}
