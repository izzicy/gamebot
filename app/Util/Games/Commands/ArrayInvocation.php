<?php

namespace App\Util\Games\Commands;

use App\Contracts\Games\CommandInvocation;
use App\Util\HasId;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;

class ArrayInvocation implements CommandInvocation
{
    use HasId;

    /**
     * The command's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The initiator.
     *
     * @var User|null
     */
    protected $initiator;

    /**
     * The message.
     *
     * @var Message|null
     */
    protected $message;

    /**
     * Construct an array invocation.
     *
     * @param string $name
     * @param array $parameters
     * @param User|null $initiator
     * @param Message|null $message
     */
    public function __construct($name, $parameters, $initiator, $message)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->initiator = $initiator;
        $this->message = $message;
    }

    /**
     * @inheritdoc
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function initiator()
    {
        return $this->initiator;
    }

    /**
     * @inheritdoc
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function parameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function parameter($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->parameters;
        }

        return $this->parameters[$name] ?? $default;
    }
}
