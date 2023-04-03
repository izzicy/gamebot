<?php

namespace App\Contracts\Routines;

interface Repository
{
    /**
     * Create a routine.
     *
     * @param string $class
     * @param array $parameters
     * @return Routine
     */
    public function create($class, $parameters = []): Routine;

    /**
     * Reindex the given routine.
     *
     * @param Routine $routine
     * @return void
     */
    public function reindex(Routine $routine);

    /**
     * Check whether the routine with the given tags exists.
     *
     * @param string[] $tags
     * @return boolean
     */
    public function tagsExists($tags);

    /**
     * Remove all routines that match the given tags.
     *
     * @param string[] $tags
     * @return void
     */
    public function destroyByTags($tags);

    /**
     * Destory the given routine from this repository.
     *
     * @param Routine $routine
     * @return void
     */
    public function destroy(Routine $routine);
}
