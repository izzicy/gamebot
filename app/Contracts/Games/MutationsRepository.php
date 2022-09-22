<?php

namespace App\Contracts\Games;

interface MutationsRepository
{
    /**
     * Get all mutation by the given name.
     *
     * @param string $name
     * @return \Illuminate\Support\Collection
     */
    public function getByName($name);

    /**
     * Get all mutations.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all();
}
