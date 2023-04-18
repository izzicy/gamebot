<?php

namespace App\Games\WhoAmI;

use App\Contracts\Routines\Routine;
use App\Contracts\Routines\WantsDiscord;
use App\Routines\Concerns\HasId;
use App\Routines\Concerns\WantsDiscordConcern;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class WhoAmIRoutine implements Routine, WantsDiscord
{
    use HasId, WantsDiscordConcern;

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['main', 'whoami'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->discord->on(Event::MESSAGE_CREATE, [$this, 'onMessage']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->discord->removeListener(Event::MESSAGE_CREATE, [$this, 'onMessage']);
    }

    /**
     * On message callback.
     *
     * @param Message $message
     * @return void
     */
    public function onMessage(Message $message)
    {
        if (
            preg_match('/^(Say my name.?)|(Who am I\??)|(What am I called\??)|(whoami)|test123$/i', $message->content)
        ) {
            $message->channel->sendMessage(
                MessageBuilder::new()
                    ->setContent('Your name is ' . $message->author->username . '.')
                    ->setReplyTo($message)
            );
        }
    }
}
