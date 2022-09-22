<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandInvocator;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;

class RegexInvocator implements CommandInvocator
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $commandName;

    /**
     * The regular expression.
     *
     * @var string[]
     */
    protected $regex;

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
     * @param string[] $regex
     * @param string|null $channelId
     */
    public function __construct($commandName, $regex, $channelId = null)
    {
        $this->commandName = $commandName;
        $this->regex = $regex;
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
                array_walk($this->regex, function($regex) use ($line, $invocations, $user, $message) {
                    if (preg_match($regex, $line, $matches)) {
                        $invocations->push(
                            new ArrayInvocation($this->commandName, $matches, $user, $message)
                        );
                    }
                });
            }
        );

        return $invocations;
    }
}
