<?php

namespace App\Contracts\Games;

use Discord\Parts\Channel\Message;

interface CommandInvocator
{
    /**
     * Create all invocations from the given message.
     *
     * @param Message $message
     * @return \Illuminate\Support\Collection
     */
    public function createFromMessage(Message $message);
}
