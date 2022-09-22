<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandHandler;

class AggregateHandler implements CommandHandler
{
    /**
     * The handlers aggregate.
     *
     * @var \Illuminate\Support\Collection|CommandHandler[]
     */
    protected $handlers;

    /**
     * Create a new aggregate handler.
     *
     * @param \Illuminate\Support\Collection|CommandHandler[] $handlers
     */
    public function __construct($handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @inheritdoc
     */
    public function handle($invocations)
    {
        $resolvements = collect();

        foreach ($this->handlers as $handler) {
            $results = $handler->handle($invocations);

            foreach ($results as $result) {
                $resolvements->push($result);
            }
        }

        return $resolvements;
    }
}
