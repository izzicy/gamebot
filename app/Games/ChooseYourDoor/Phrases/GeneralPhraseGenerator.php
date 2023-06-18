<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use App\Games\ChooseYourDoor\Phrases\Concerns\CreatesItemsInSeries;
use Illuminate\Support\Arr;

class GeneralPhraseGenerator implements PhraseGenerator
{
    use CreatesItemsInSeries;

    /**
     * @inheritdoc
     */
    public function make($usernames, $state): string
    {
        $parameters = $this->composeParameters($usernames, $state);

        if ($state === 'CHEATER') {
            $cheaterPhrases = __('choose-your-door.cheater_callouts');

            return trans_choice(Arr::random($cheaterPhrases), count($usernames), $parameters);
        }

        if ($state === 'WIN') {
            $winPhrases = __('choose-your-door.win_phrases');

            return trans_choice(Arr::random($winPhrases), count($usernames), $parameters);
        }

        if ($state === 'LOSE') {
            $losePhrases = __('choose-your-door.lose_phrases');

            return trans_choice(Arr::random($losePhrases), count($usernames), $parameters);
        }

        return '';
    }

    /**
     * Compose the parameters.
     *
     * @param string[] $usernames
     * @param string $state
     * @return array
     */
    protected function composeParameters($usernames, $state)
    {
        $usernamesSeries = $this->createItemsInSeries($usernames);

        if ($state === 'CHEATER') {
            return ['usernames' => $usernamesSeries];
        }

        $parameters = [
            'usernames' => $usernamesSeries,
        ];

        if ($state === 'WIN') {
            foreach (__('choose-your-door.win_substitutes') as $type => $substitutions) {
                $substitution = Arr::random($substitutions);

                $parameters[$type] = $substitution;
            }
        }

        if ($state === 'LOSE') {
            foreach (__('choose-your-door.lose_substitutes') as $type => $substitutions) {
                $substitution = Arr::random($substitutions);

                $parameters[$type] = $substitution;
            }
        }

        return $parameters;
    }
}
