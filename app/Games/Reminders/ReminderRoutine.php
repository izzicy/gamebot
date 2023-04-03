<?php

namespace App\Games\Reminders;

use App\Contracts\Routines\Routine;
use App\Contracts\Routines\WantsDiscord;
use App\Contracts\Routines\WantsRepository;
use App\Routines\Concerns\HasId;
use App\Routines\Concerns\WantsDiscordConcern;
use App\Routines\Concerns\WantsRepositoryConcern;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class ReminderRoutine implements Routine, WantsRepository, WantsDiscord
{
    use HasId, WantsRepositoryConcern, WantsDiscordConcern;

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['reminder', 'main'];
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

        $this->repository->destroyByTags(['reminder', 'subroutine']);
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

        if (str_contains($message->content, 'enter saying hi mode')) {
            if ($this->repository->tagsExists(['reminder', 'user:' . $user->id])) {
                $message->channel->sendMessage('I\'m already in saying hi mode!');
            } else {
                $this->repository->create(AddReminderRoutine::class, [
                    'channel' => $message->channel,
                    'user' => $user,
                ])->initialize();
                $message->channel->sendMessage('Starting saying hi mode!');
            }
        }
    }
}
