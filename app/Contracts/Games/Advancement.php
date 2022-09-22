<?php

namespace App\Contracts\Games;

interface Advancement
{
    /**
     * Get the advancement id.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the advancement name.
     *
     * @return string
     */
    public function name();

    /**
     * Get the mutations of this command resolvement.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mutations();
}
