<?php

namespace App\Games\Reminders;

use App\Contracts\Routines\Routine;
use App\Contracts\Routines\WantsDiscord;
use App\Contracts\Routines\WantsRepository;
use App\Routines\Concerns\HasId;
use App\Routines\Concerns\WantsDiscordConcern;
use App\Routines\Concerns\WantsRepositoryConcern;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Discord\WebSockets\Event;

class AddReminderRoutine implements Routine, WantsRepository, WantsDiscord
{
    use HasId, WantsRepositoryConcern, WantsDiscordConcern;

    /**
     * Construct a new add reminder routine.
     *
     * @param Channel $channel
     * @param User $user
     */
    public function __construct(
        protected Channel $channel,
        protected User $user
    )
    { }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['reminder', 'subroutine', 'user:' . $this->user->id];
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
        $user = $message->author;

        if ($user instanceof Member) {
            $user = $user->user;
        }

        if ($user->id !== $this->user->id || $message->channel_id !== $this->channel->id) {
            return;
        }

        if (str_contains($message->content, 'bot say hi')) {
            $message->channel->sendMessage('Hi!');
        }

        if (str_contains($message->content, 'exit mode')) {
            $message->channel->sendMessage('Exiting saying hi mode!');

            $this->repository->destroyByTags($this->tags());
        }
    }
}
