<?php

namespace App\Contracts\Games;

interface GameSession
{
    /**
     * Get the session id.
     *
     * @return string
     */
    public function getId();
}
