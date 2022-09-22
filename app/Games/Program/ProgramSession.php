<?php

namespace App\Games\Program;

use App\Contracts\Discord\DiscordManager;
use App\Contracts\Games\CommandInvocation;
use App\Contracts\Games\GameSession;
use App\Util\Games\Commands\ArrayInvocation;
use App\Util\Games\Commands\HandlerFactory;
use App\Util\Games\Commands\InvocatorFactory;
use App\Util\HasId;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class ProgramSession implements GameSession
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
     * The see and says.
     *
     * @var array
     */
    protected $seeAndSay = [];

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
        $invocaters[] = $this->invocatorFactory->regex('addsay', ['/^Operator,? if you see (?P<observe>.+),? then say (?P<phrase>.+)$/i',]);
        $invocaters[] = $this->invocatorFactory->callback('seeandsay', function($line, $user, $message) {
            foreach ($this->seeAndSay as $seeAndSay) {
                list($see, $say) = $seeAndSay;

                if (str_contains($line, $see)) {
                    return collect([
                        new ArrayInvocation('seeandsay', ['message' => $say], $user, $message)
                    ]);
                }
            }

            return collect();
        });
        $handlers = collect();
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $user = $invocation->initiator();
            $message = $invocation->message();

            if ($user && $message && $invocation->name() === 'addsay') {
                $this->seeAndSay[] = [$invocation->parameter('observe'), $invocation->parameter('phrase')];
                $message->channel->sendMessage('I\'m listening...');
            }

            return collect();
        });
        $handlers[] = $this->handlerFactory->callback(function(CommandInvocation $invocation) {
            $user = $invocation->initiator();
            $message = $invocation->message();

            if ($user && $message && $invocation->name() === 'seeandsay') {
                $message->channel->sendMessage($invocation->parameter('message'));
            }

            return collect();
        });
        $invocator = $this->invocatorFactory->aggregate($invocaters);
        $invocations = $invocator->createFromMessage($message);
        $handler = $this->handlerFactory->aggregate($handlers);
        $handler->handle($invocations);

    }
}
