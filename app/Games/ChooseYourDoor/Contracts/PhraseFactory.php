<?php

namespace App\Games\ChooseYourDoor\Contracts;

interface PhraseFactory
{
    /**
     * Create a phrase generator.
     *
     * @return PhraseGenerator
     */
    public function createPhraseGenerator(): PhraseGenerator;
}
