<?php

namespace App\Games\ChooseYourDoor\Contracts;

interface PhraseCollection
{
    /**
     * Get the next phrase.
     *
     * @return string
     */
    public function next(): string;
}
