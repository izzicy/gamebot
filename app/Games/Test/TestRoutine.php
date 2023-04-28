<?php

namespace App\Games\Test;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Games\ZeroDollarGame\Models\Game;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Attachment;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Command\Option;
use Discord\WebSockets\Event;
use Illuminate\Database\Eloquent\Collection;

class TestRoutine implements Routine
{
    use HasId;

    protected $image01;
    protected $image02;
    protected $time;

    /**
     * Construct the zero dollar game routine.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param InteractionDispatcher $dispatcher
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected InteractionDispatcher $dispatcher,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['test', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->image01 = file_get_contents("C:\\Users\\Bennett Vollebregt\\Pictures\\target.jpg");
        $this->image02 = file_get_contents('C:\\Users\\Bennett Vollebregt\\Pictures\\izztesty.jpg');
        $this->time = microtime(true);

        $this->discord->getChannel('811689892038836247')->sendMessage(
            MessageBuilder::new()
                ->addFileFromContent('test.jpg', $this->image01)
        )->then([$this, 'updateTwo']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {

    }

    public function updateOne(Message $message)
    {
        print("\n\n\nDelta: " . microtime(true) - $this->time . "\n\n\n");
        $this->time = microtime(true);

        $message->edit(
            MessageBuilder::new()
                ->clearAttachments()
                ->addFileFromContent('test.jpg', $this->image01)
        )->then([$this, 'updateTwo']);
    }

    public function updateTwo(Message $message)
    {
        print("\n\n\nDelta: " . microtime(true) - $this->time . "\n\n\n");
        $this->time = microtime(true);

        $message->edit(
            MessageBuilder::new()
                ->clearAttachments()
                ->addFileFromContent('test.jpg', $this->image02)
        )->then([$this, 'updateOne']);
    }
}
