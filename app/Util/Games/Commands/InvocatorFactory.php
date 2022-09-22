<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandInvocator;
use Closure;
use Illuminate\Support\Arr;

class InvocatorFactory
{
    /**
     * Create a callback invocation creator.
     *
     * @param string $commandName
     * @param Closure $callback
     * @param string|null $channelId
     * @return CommandInvocator
     */
    public function callback($commandName, $callback, $channelId = null)
    {
        return new CallbackInvocator($commandName, $callback, $channelId);
    }

    /**
     * Create a regex invocation creator.
     *
     * @param string $commandName
     * @param string[]|string $regex
     * @param string|null $channelId
     * @return CommandInvocator
     */
    public function regex($commandName, $regex, $channelId = null)
    {
        return new RegexInvocator($commandName, Arr::wrap($regex), $channelId);
    }

    /**
     * Create an aggragte handler.
     *
     * @param \Illuminate\Support\Collection|CommandInvocator[] $handlers
     * @return CommandInvocator
     */
    public function aggregate($handlers)
    {
        return new AggregateInvocator($handlers);
    }
}
