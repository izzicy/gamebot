<?php

namespace App\Contracts\Routines;

use Discord\Discord;

interface WantsDiscord
{
    /**
     * Pass along the Discord instance.
     *
     * @param Discord
     * @return $this
     */
    public function withDiscord(Discord $discord);
}
