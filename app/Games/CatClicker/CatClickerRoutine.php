<?php

namespace App\Games\CatClicker;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Routines\Concerns\HasId;
use App\Utils\StringCoder;
use Discord\Builders\CommandBuilder;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;

class CatClickerRoutine implements Routine
{
    use HasId;

    /**
     * Busy ids.
     *
     * @var string[]
     */
    protected $busy = [];

    /**
     * Construct the zero dollar game routine.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param InteractionDispatcher $dispatcher
     * @param StringCoder $stringCoder
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected InteractionDispatcher $dispatcher,
        protected StringCoder $stringCoder,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['cat_clicker', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->discord->application->commands->save(
            $this->discord->application->commands->create(CommandBuilder::new()
                ->setName('cat_clicker')
                ->setDescription('Play the cat clicker game.')
                ->toArray()
            )
        )->then([$this->dispatcher, 'register']);

        $this->dispatcher->on('cat_clicker', [$this, 'onCommand']);
        $this->discord->on(Event::INTERACTION_CREATE, [$this, 'onInteraction']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->dispatcher->removeListener('cat_clicker', [$this, 'onCommand']);
        $this->discord->removeListener(Event::INTERACTION_CREATE, [$this, 'onInteraction']);
    }

    /**
     * On message callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onCommand(Interaction $interaction)
    {
        $interaction->acknowledgeWithResponse(true);

        $choosableCats = collect(config('cat-clicker.pictures'));
        $chosenCat = rand(0, count($choosableCats) - 1);

        $message = $this->baseMessageBuilder(0, $chosenCat);

        $interaction->channel->sendMessage(
            $message
                ->addFile($this->getImage($chosenCat))
        );
    }

    /**
     * On interaction callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onInteraction(Interaction $interaction)
    {
        $interaction->acknowledge();

        if ($this->stringCoder->is($interaction->data->custom_id, 'cclick')) {
            $data = $this->stringCoder->decode($interaction->data->custom_id);
            $id = $data['i'] ?? '';

            if ( ! empty($this->busy[$id])) {
                return;
            }

            $this->busy[$id] = true;

            $count = $data['c'];

            $choosableCats = collect(config('cat-clicker.pictures'));
            $chosenCat = $data['t'] ?? 0;
            $previousChoice = $chosenCat;

            $count++;

            if (($count % config('cat-clicker.replacement_count')) === 0) {
                $chosenCat = rand(0, count($choosableCats) - 1);
            }

            $message = $this->baseMessageBuilder($count, $chosenCat);

            if ($previousChoice != $chosenCat) {
                $message
                    ->clearAttachments()
                    ->addFile($this->getImage($chosenCat));
            }

            $interaction->message->edit($message)->then(function() use ($id) {
                unset($this->busy[$id]);
            });
        }
    }

    /**
     * Create a base message builder.
     *
     * @param int $clickCount
     * @param int $chosenCat
     * @return MessageBuilder
     */
    protected function baseMessageBuilder($clickCount, $chosenCat)
    {
        $choosableCats = collect(config('cat-clicker.pictures'));

        $action = ActionRow::new();

        $clicker = Button::new(
                Button::STYLE_PRIMARY,
                $this->stringCoder->encode('cclick', [ 'i' => substr(md5(time()), 0, 6), 'c' => $clickCount, 't' => $chosenCat ])
            )
            ->setLabel(
                $clickCount == 0
                ? "The cat hasn't been petted yet!"
                : "The cat has been petted {$clickCount} times!"
            )
            ->setEmoji("\u{1F408}");

        $comments = collect([
            'Pet the cat!',
            'Pet this cat!',
            'Click the pet cat button!',
            'C\'mon, press on the pet cat button!',
            'Press the pet cat button!',
            'Pet cat button, must be pressed!',
        ]);

        if (is_array($choosableCats[$chosenCat])) {
            $comments = collect($choosableCats[$chosenCat]['comments']);
        }

        return MessageBuilder::new()
            ->setContent($comments->random())
            ->addComponent($action->addComponent($clicker));
    }

    /**
     * Get the image.
     *
     * @param int $chosenCat
     * @return string
     */
    protected function getImage($chosenCat)
    {
        $choosableCats = collect(config('cat-clicker.pictures'));
        $chosenCatImage = $choosableCats[$chosenCat];

        if (is_array($chosenCatImage)) {
            return $chosenCatImage['image'];
        }

        return $chosenCatImage;
    }
}
