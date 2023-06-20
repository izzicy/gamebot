<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseCollection;
use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use App\Games\ChooseYourDoor\Phrases\Concerns\CreatesItemsInSeries;
use Illuminate\Support\Arr;

class GeneralPhraseGenerator implements PhraseGenerator
{
    use CreatesItemsInSeries;

    /**
     * Construct a general phrase generator.
     *
     * @param PhraseCollection $winPhrases
     * @param PhraseCollection $losePhrases
     * @param PhraseCollection $cheaterPhrases
     */
    public function __construct(
        protected PhraseCollection $winPhrases,
        protected PhraseCollection $losePhrases,
        protected PhraseCollection $cheaterPhrases,
    )
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function make($usernames, $state): string
    {
        $parameters = $this->composeParameters($usernames, $state);

        if ($state === 'CHEATER') {
            return trans_choice($this->cheaterPhrases->next(), count($usernames), $parameters);
        }

        if ($state === 'WIN') {
            return trans_choice($this->winPhrases->next(), count($usernames), $parameters);
        }

        if ($state === 'LOSE') {
            return trans_choice($this->losePhrases->next(), count($usernames), $parameters);
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
