<?php

namespace App\Games\ChooseYourDoor\Phrases\Concerns;

use Illuminate\Support\Arr;

trait CreatesItemsInSeries
{
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
