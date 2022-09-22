<?php


namespace App\Contracts\Games;

use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;

interface CommandInvocation
{
    /**
     * Get the invocation id.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the command name.
     *
     * @return string
     */
    public function name();

    /**
     * The command initiator.
     *
     * @return User|null
     */
    public function initiator();

    /**
     * The message.
     *
     * @return Message|null
     */
    public function message();

    /**
     * Get all of the command's parameters.
     *
     * @return array
     */
    public function parameters();

    /**
     * Get a specific parameter.
     *
     * @param string|null $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function parameter($name = null, $default = null);
}
