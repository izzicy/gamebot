<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use App\Games\ChooseYourDoor\Phrases\Concerns\CreatesItemsInSeries;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class GeneralPhraseGenerator implements PhraseGenerator
{
    use CreatesItemsInSeries;

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

            return trans_choice(Arr::random($cheaterPhrases), count($usernames), ['usernames' => $usernamesSeries]);
        }

        $generator = Arr::random($this->generators);

        return $generator->make($usernames, $state);
    }
}
