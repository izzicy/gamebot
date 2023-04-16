<?php

namespace App\Contracts\Routines;

use React\Promise\PromiseInterface;

interface Routine
{
    /**
     * Get the unique routine identifier.
     *
     * @return string
     */
    public function id();

    /**
     * Intialize the routine when Discord is active.
     *
     * @return PromiseInterface|void
     */
    public function initialize();

    /**
     * The tags that describe this routine.
     *
     * @return string[]
     */
    public function tags();

    /**
     * Destroy the routine.
     *
     * @return PromiseInterface|void
     */
    public function destroy();
}
