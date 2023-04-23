<?php

namespace App\Utils\Schedule;

use App\Contracts\Schedule\Scheduler;
use Illuminate\Support\Carbon;
use React\Promise\CancellablePromiseInterface;

use function React\Promise\Timer\sleep;

class ReactPHPScheduler implements Scheduler
{
    /**
     * @inheritdoc
     */
    public function atTime($time): CancellablePromiseInterface
    {
        $now = Carbon::now();
        $time = Carbon::parse($time);

        if ($time->lt($now)) {
            $time->addDay();
        }

        return sleep($now->diffInSeconds($time, true));
    }
}
