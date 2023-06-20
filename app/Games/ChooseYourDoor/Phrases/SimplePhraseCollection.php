<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseCollection;
use Illuminate\Support\Arr;

class SimplePhraseCollection implements PhraseCollection
{
    /**
     * The current phrase pointer.
     *
     * @var integer
     */
    protected $pointer = 0;

    /**
     * List of ordered phrases.
     *
     * @var string[]
     */
    protected $orderedPhrases;

    /**
     * Construct a simple phrase collection.
     *
     * @param string[] $phrases
     */
    public function __construct($phrases)
    {
        $this->orderedPhrases = Arr::shuffle($phrases);
    }

    /**
     * @inheritdoc
     */
    public function next(): string
    {
        $phrase = $this->orderedPhrases[$this->pointer];

        $this->incrementPointer();

        return $phrase;
    }

    /**
     * Increment the phrase pointer.
     *
     * @return void
     */
    protected function incrementPointer()
    {
        $this->pointer += 1;

        if ($this->pointer >= count($this->orderedPhrases)) {
            $this->pointer = 0;
        }
    }
}
