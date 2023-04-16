<?php

namespace App\Games\ChooseYourDoor;

use Discord\Parts\User\User;

class ReactionCollector
{
    /**
     * The users by reactions.
     *
     * @var array
     */
    protected $usersByReactions = [];

    /**
     * List of reacted user ids.
     *
     * @var array
     */
    protected $reactedUsers = [];

    /**
     * List of cheaters.
     *
     * @var array
     */
    protected $cheaters = [];

    /**
     * Add the given user reaction.
     *
     * @param User $user
     * @param integer $reaction
     * @return $this
     */
    public function addReaction(User $user, $reaction)
    {
        if (in_array($user->id, $this->reactedUsers)) {
            $this->cheaters[] = $user->id;

            return $this;
        }

        $this->usersByReactions[$reaction][] = $user;
        $this->reactedUsers[] = $user->id;

        return $this;
    }

    /**
     * Whether a user is a cheater or not.
     *
     * @param User $user
     * @return boolean
     */
    public function isCheater($user)
    {
        return in_array($user->id, $this->cheaters);
    }

    /**
     * Get the users by reactions.
     *
     * @return array
     */
    public function getUsersByReactions()
    {
        $reactions = [];

        foreach ($this->usersByReactions as $reaction => $users) {
            foreach ($users as $user) {
                if ($this->isCheater($user)) {
                    $reactions['cheater'][] = $user;
                } else {
                    $reactions[$reaction][] = $user;
                }
            }
        }

        ksort($reactions);

        return $reactions;
    }
}
