<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class GeneralPhraseGenerator implements PhraseGenerator
{
    /**
     * List of generators.
     *
     * @var PhraseGenerator[]
     */
    protected $generators = [];

    /**
     * Create a general phrase generator.
     *
     * @param Container $con
     */
    public function __construct(
        protected Container $con,
    )
    {
        $this->generators = [
            $this->con->make(ThingPhraseGenerator::class),
            $this->con->make(FixedPhraseGenerator::class),
        ];
    }

    /**
     * @inheritdoc
     */
    public function make($usernames, $state): string
    {
        if ($state === 'CHEATER') {
            $usernamesSeries = $this->createItemsInSeries($usernames);
            $cheaterPhrases = __('choose-your-door.cheater_callouts');

            return __(Arr::random($cheaterPhrases), ['usernames' => $usernamesSeries]);
        }

        $generator = Arr::random($this->generators);

        return $generator->make($usernames, $state);
    }

    /**
     * Create an items in series.
     *
     * @param string[] $nouns
     * @return string
     */
    protected function createItemsInSeries($nouns)
    {
        if (count($nouns) === 1) {
            return '**' . Arr::first($nouns) . '**';
        }

        $nounsWithoutLast = $nouns;
        $lastItem = array_pop($nounsWithoutLast);

        return '**' . implode(', ', $nounsWithoutLast) . ', and ' . $lastItem . '**';
    }
}
