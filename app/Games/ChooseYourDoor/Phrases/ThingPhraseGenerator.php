<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use Illuminate\Support\Arr;

class ThingPhraseGenerator implements PhraseGenerator
{
    /**
     * @inheritdoc
     */
    public function make($usernames, $state): string
    {
        $usernamesSeries = $this->createItemsInSeries($usernames);

        $winPhrases = __('choose-your-door.win_thing_phrases');
        $winThings = __('choose-your-door.win_things');
        $winPlaces = __('choose-your-door.win_places');

        $losePhrases = __('choose-your-door.lose_thing_phrases');
        $loseThings = __('choose-your-door.lose_things');
        $losePlaces = __('choose-your-door.lose_places');

        if ($state === 'WIN') {
            return __(Arr::random($winPhrases), [
                'usernames' => $usernamesSeries,
                'thing' => Arr::random($winThings),
                'place' => Arr::random($winPlaces),
            ]);
        }

        if ($state === 'LOSE') {
            return __(Arr::random($losePhrases), [
                'usernames' => $usernamesSeries,
                'thing' => Arr::random($loseThings),
                'place' => Arr::random($losePlaces),
            ]);
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
