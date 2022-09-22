<?php

namespace App\Contracts\Games;

interface Mutation
{
    /**
     * Get the mutation id.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the mutation name.
     *
     * @return string
     */
    public function name();

    /**
     * Get all of the command's parameters.
     *
     * @return array
     */
    public function parameters();

    /**
     * Get a specific parameter.
     *
     * @param string|null $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function parameter($name = null, $default = null);
}
