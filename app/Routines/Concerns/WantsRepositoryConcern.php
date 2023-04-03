<?php

namespace App\Routines\Concerns;

use App\Contracts\Routines\Repository;

trait WantsRepositoryConcern
{
    /** @var Repository */
    protected $repository;

    /**
     * Pass along the given repository to this instance.
     *
     * @param Repository $repository
     * @return $this
     */
    public function withRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }
}
