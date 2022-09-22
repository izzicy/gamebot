<?php

namespace App\Contracts\Games;

interface CommandResolvement
{
    /**
     * Get the command invocation.
     *
     * @return CommandInvocation
     */
    public function invocation(): CommandInvocation;

    /**
     * Get the mutations of this command resolvement.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mutations();
}
