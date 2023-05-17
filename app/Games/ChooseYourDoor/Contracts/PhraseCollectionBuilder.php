<?php

namespace App\Games\ChooseYourDoor\Contracts;

interface PhraseCollectionBuilder {
    /**
     * Add a phrase with the given weight.
     *
     * @param string $phrase
     * @param float $weight
     * @return void
     */
    public function add($phrase, $weight);

    /**
     * Remote a phrase.
     *
     * @param string $phrase
     * @return void
     */
    public function remove($phrase);

    /**
     * Create a phrase collection.
     *
     * @return void
     */
    public function make();
}
