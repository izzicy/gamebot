<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use App\Games\ChooseYourDoor\Phrases\Concerns\CreatesItemsInSeries;
use Illuminate\Support\Arr;

class FixedPhraseGenerator implements PhraseGenerator
{
    use CreatesItemsInSeries;

    /**
     * @inheritdoc
     */
    public function make($usernames, $state): string
    {
        $usernamesSeries = $this->createItemsInSeries($usernames);
        $winPhrases = __('choose-your-door.win_fixed_phrases');
        $losePhrases = __('choose-your-door.lose_fixed_phrases');

        if ($state === 'WIN') {
            return trans_choice(Arr::random($winPhrases), count($usernames), ['usernames' => $usernamesSeries]);
        }

        if ($state === 'LOSE') {
            return trans_choice(Arr::random($losePhrases), count($usernames), ['usernames' => $usernamesSeries]);
        }

        return '';
    }
}
