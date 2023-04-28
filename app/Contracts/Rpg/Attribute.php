<?php

namespace App\Contracts\Rpg;

interface Attribute
{
    /**
     * Unique attribute identifier.
     *
     * @return string
     */
    public function id();

    /**
     * Get the attribute name.
     *
     * @return string
     */
    public function name();

    /**
     * Get the attribute value.
     *
     * @return mixed
     */
    public function value();

    /**
     * Set the attribute value.
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value);
}
