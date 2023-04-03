<?php

namespace App\Contracts\Routines;

interface WantsRepository
{
    /**
     * Pass along the given repository to this instance.
     *
     * @param Repository $repository
     * @return $this
     */
    public function withRepository(Repository $repository);
}
