<?php

namespace App\Contracts\Rpg;

interface AttributesContainer
{
    /**
     * The unique container identifier.
     *
     * @return string
     */
    public function id();

    /**
     * Get an attribute by name.
     *
     * @param string $name
     * @return Attribute
     */
    public function attr($name): Attribute;
}
