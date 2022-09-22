<?php

namespace App\Contracts\Games;

interface Porgressor
{
    /**
     * Proceed the game.
     *
     * @return \Illuminate\Support\Collection
     */
    public function progress();
}
