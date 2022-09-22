<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandInvocator;
use Discord\Parts\Channel\Message;

class AggregateInvocator implements CommandInvocator
{
    /**
     * The invocators aggregate.
     *
     * @var \Illuminate\Support\Collection|CommandInvocator[]
     */
    protected $invocators;

    /**
     * Create a new aggregate handler.
     *
     * @param \Illuminate\Support\Collection|CommandInvocator[] $invocators
     */
    public function __construct($invocators)
    {
        $this->invocators = $invocators;
    }

    /**
     * @inheritdoc
     */
    public function createFromMessage(Message $message)
    {
        $invocations = collect();

        foreach ($this->invocators as $invocator) {
            $results = $invocator->createFromMessage($message);

            foreach ($results as $result) {
                $invocations->push($result);
            }
        }

        return $invocations;
    }
}
