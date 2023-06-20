<?php

namespace App\Games\ChooseYourDoor\Phrases;

use App\Games\ChooseYourDoor\Contracts\PhraseFactory as PhraseFactoryContract;
use App\Games\ChooseYourDoor\Contracts\PhraseGenerator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class PhraseFactory implements PhraseFactoryContract
{
    /**
     * Create a phrase factory.
     *
     * @param Container $con
     */
    public function __construct(
        protected Container $con,
    )
    {

    }

    /**
     * @inheritdoc
     */
    public function createPhraseGenerator(): PhraseGenerator
    {
        try {
            $winGroupedPhrases = __('choose-your-door.win_phrases');
            $loseGroupedPhrases = __('choose-your-door.lose_phrases');
            $cheaterPhrases = __('choose-your-door.cheater_callouts');

            $winGroup = Arr::random(array_keys($winGroupedPhrases));
            $loseGroup = isset($loseGroupedPhrases[$winGroup]) ? $winGroup : Arr::random(array_keys($loseGroupedPhrases));

            return $this->con->make(GeneralPhraseGenerator::class, [
                'winPhrases' => new GroupPrioritizedPhraseCollection($winGroupedPhrases, $winGroup),
                'losePhrases' => new GroupPrioritizedPhraseCollection($loseGroupedPhrases, $loseGroup),
                'cheaterPhrases' => new SimplePhraseCollection($cheaterPhrases),
            ]);
        } catch (\Throwable $th) {
            print("\n\n");
            print($th->getTraceAsString());
            print("\n\n");
        }

    }
}
