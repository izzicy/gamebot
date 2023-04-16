<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use Illuminate\Support\Arr;

class FixedPhraseGenerator implements PhraseGenerator
{
    /**
     * @inheritdoc
     */
    public function make($usernames, $state): string
    {
        $usernamesSeries = $this->createItemsInSeries($usernames);
        $winPhrases = __('choose-your-door.win_fixed_phrases');
        $losePhrases = __('choose-your-door.lose_fixed_phrases');

        if ($state === 'WIN') {
            return __(Arr::random($winPhrases), ['usernames' => $usernamesSeries]);
        }

        if ($state === 'LOSE') {
            return __(Arr::random($losePhrases), ['usernames' => $usernamesSeries]);
        }

        return '';
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
