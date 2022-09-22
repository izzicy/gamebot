<?php

namespace App\Games\Reminders;

use App\Contracts\Discord\DiscordManager;
use App\Contracts\Games\CommandInvocation;
use App\Contracts\Games\GameSession;
use App\Util\Games\Commands\HandlerFactory;
use App\Util\Games\Commands\InvocatorFactory;
use App\Util\HasId;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class RemindersSession implements GameSession
{
    use HasId;

    /**
     * The Discord manager.
     *
     * @var DiscordManager
     */
    protected $manager;

    /**
     * The discord instance.
     *
     * @var Discord
     */
    protected $discord;

    /** @var InvocatorFactory */
    protected $invocatorFactory;

    /** @var HandlerFactory */
    protected $handlerFactory;

    /**
     * The stored reminders.
     *
     * @var array
     */
    protected $reminders = [];

    /**
     * @inheritdoc
     */
    public function __construct(
        DiscordManager $manager,
        InvocatorFactory $invocatorFactory,
        HandlerFactory $handlerFactory
    )
    {
        $this->manager = $manager;
        $this->invocatorFactory = $invocatorFactory;
        $this->handlerFactory = $handlerFactory;

        $this->manager->get()->then([$this, 'run']);
    }

    /**
     * Run the session.
     *
     * @param Discord $discord
     * @return void
     */
    public function run(Discord $discord)
    {
        $this->discord = $discord;

        $discord->on(Event::MESSAGE_CREATE, [$this, 'onMessage']);
    }

    /**
     * Run the message callback.
     *
     * @param Message $message
     * @return void
     */
    public function onMessage(Message $message)
    {
        $invocaters = collect();
        $invocaters[] = $this->invocatorFactory->regex('addreminder', [
            '/^Hey bot,?( can you)? remind( me)?( to)?\s*(?P<reminder>.+)$/',
            '/^Hey bot,?( set|add)?( an)? reminder:? \s*(?P<reminder>.+)$/',
        ]);
        $invocaters[] = $this->invocatorFactory->regex('reminders', '/^Hey bot,? what are my reminders\??$/i');
        $handlers = collect();
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $user = $invocation->initiator();
            $message = $invocation->message();

            if ($user && $message && $invocation->name() === 'addreminder') {
                $this->reminders[$user->id][] = $invocation->parameter('reminder');
                $message->channel->sendMessage('A reminder has been set.');
            }

            return collect();
        });
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $user = $invocation->initiator();
            $message = $invocation->message();

            if ($user && $message && $invocation->name() === 'reminders') {
                $reminders = $this->reminders[$user->id] ?? null;

                if ( ! $reminders) {
                    $message->channel->sendMessage('You have no reminder set.');
                } else {
                    $message->channel->sendMessage('Here are your reminders:' . PHP_EOL . collect($reminders)->map(fn ($reminder) => '- ' . $reminder)->join(PHP_EOL));
                }
            }

            return collect();
        });
        $invocator = $this->invocatorFactory->aggregate($invocaters);
        $invocations = $invocator->createFromMessage($message);
        $handler = $this->handlerFactory->aggregate($handlers);
        $handler->handle($invocations);

    }
}
