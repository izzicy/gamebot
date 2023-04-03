<?php

namespace App\Routines\Concerns;

use Discord\Discord;

trait WantsDiscordConcern
{
    /** @var Discord */
    protected $discord;

    /**
     * Pass along the Discord instance.
     *
     * @param Discord
     * @return $this
     */
    public function withDiscord(Discord $discord)
    {
        $this->discord = $discord;

        return $this;
    }
}
