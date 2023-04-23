<?php

namespace App\Contracts\Schedule;

use React\Promise\CancellablePromiseInterface;

interface Scheduler
{
    /**
     * Schedule a promise to resolve at a given time.
     *
     * @param string $time
     * @return CancellablePromiseInterface
     */
    public function atTime($time): CancellablePromiseInterface;
}
