<?php

namespace App\Games\Test;

use App\Contracts\Discord\DiscordManager;
use App\Contracts\Games\CommandInvocation;
use App\Contracts\Games\GameSession;
use App\Util\Games\Commands\HandlerFactory;
use App\Util\Games\Commands\InvocatorFactory;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Illuminate\Support\Str;

class TestSession implements GameSession
{
    /**
     * The id.
     *
     * @var string
     */
    protected $id;

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
     * @inheritdoc
     */
    public function __construct(
        DiscordManager $manager,
        InvocatorFactory $invocatorFactory,
        HandlerFactory $handlerFactory
    )
    {
        $this->id = (string) Str::uuid();
        $this->manager = $manager;
        $this->invocatorFactory = $invocatorFactory;
        $this->handlerFactory = $handlerFactory;

        $this->manager->get()->then([$this, 'run']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
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
        $invocaters[] = $this->invocatorFactory->regex('inspire', '/^Inspire me bot!$/i');
        $invocaters[] = $this->invocatorFactory->regex('repeatme', '/^Repeat after me:\s*(?P<message>.*)/i');
        $invocaters[] = $this->invocatorFactory->regex('heybot', '/^Hey bot,? question,?\s*(?P<question>.*)?$/i');
        $invocaters[] = $this->invocatorFactory->regex('saymyname', '/^(Say my name.?)|(Who am I\??)|(What am I called\??)$/i');
        $handlers = collect();
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $message = $invocation->message();

            if ($message && $invocation->name() === 'inspire') {
                $message->channel->sendMessage('Simplicity is the ultimate sophistication.');
            }

            return collect();
        });
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $message = $invocation->message();

            if ($message && $invocation->name() === 'repeatme') {
                $message->channel->sendMessage($invocation->parameter('message'));
            }

            return collect();
        });
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $message = $invocation->message();

            if ($message && $invocation->name() === 'heybot') {
                $message->channel->sendMessage(
                    collect([
                        "I don't know.",
                        "Why would I care?",
                        "That isn't important.",
                        "Please refer to a human for that question.",
                        "I'd love to help you, however I'm busy right now.",
                        "That's outside of my understanding.",
                        "Sure. I suppose.",
                        "Can you explain that again, but in one's and zero's?",
                        "Errr, solve it with blockchain?",
                        "Sorry, what did you say?",
                    ])->random()
                );
            }

            return collect();
        });
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $initiator = $invocation->initiator();
            $message = $invocation->message();

            if ($initiator && $message && $invocation->name() === 'saymyname') {
                $message->channel->sendMessage('You name is ' . $initiator->username . '.');
            }

            return collect();
        });
        $invocator = $this->invocatorFactory->aggregate($invocaters);
        $invocations = $invocator->createFromMessage($message);
        $handler = $this->handlerFactory->aggregate($handlers);
        $handler->handle($invocations);

    }
}
