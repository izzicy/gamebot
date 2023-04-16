<?php

namespace App\Games\ChooseYourDoor\Contracts;

interface PhraseGenerator
{
  /**
   * Create a phrase.
   *
   * @param string[] $usernames
   * @param string $state WON/LOST/CHEATER
   * @return string
   */
  public function make($usernames, $state): string;
}
