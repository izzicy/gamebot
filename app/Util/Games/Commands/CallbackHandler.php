<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandHandler;

class CallbackHandler implements CommandHandler
{
    /**
     * The callback.
     *
     * @var \Closure
     */
    protected $callback;

    /**
     * Create a new callback handler.
     *
     * @param \Closure $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function handle($invocations)
    {
        $resolvements = collect();

        foreach ($invocations as $invocation) {
            $resolvements = ($this->callback)($invocation);
        }

        return $resolvements;
    }
}
