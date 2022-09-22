<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandHandler;

class HandlerFactory
{
    /**
     * Create a new callback handler.
     *
     * @param \Closure $callback
     * @return CommandHandler
     */
    public function callback($callback)
    {
        return new CallbackHandler($callback);
    }

    /**
     * Create an aggragte handler.
     *
     * @param \Illuminate\Support\Collection|CommandHandler[] $handlers
     * @return CommandHandler
     */
    public function aggregate($handlers)
    {
        return new AggregateHandler($handlers);
    }
}
