<?php

namespace App\Games\ZeroDollarGame;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Games\ZeroDollarGame\Models\Game;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Command\Option;
use Discord\WebSockets\Event;
use Illuminate\Database\Eloquent\Collection;

class ZeroDollarGameRoutine implements Routine
{
    use HasId;

    /**
     * All active games.
     *
     * @var Collection
     */
    protected $games;

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
        $this->games = Game::query()->get();
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['zero_dollar_game', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        foreach ($this->games as $game) {
            $this->repository->create(OngoingGame::class, [
                'game' => $game,
            ])->initialize();
        }

        $this->createCommands();

        $this->discord->on(Event::MESSAGE_CREATE, [$this, 'onMessage']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->discord->removeListener(Event::MESSAGE_CREATE, [$this, 'onMessage']);

        $this->repository->destroyByTags(['zero_dollar_game', 'subroutine']);
    }

    /**
     * On message callback.
     *
     * @param Message $message
     * @return void
     */
    public function onMessage(Message $message)
    {
        if ( ! in_array($message->channel_id, config('zero-dollar-game.allowed_channels'))) {
            return;
        }

        if (
            preg_match('/(initialize|start|begin) zero dollar game/i', $message->content)
        ) {
            if ($this->repository->tagsExists(['zero_dollar_game', 'channel:' . $message->channel_id])) {
                $message->channel->sendMessage("I'm already playing a Zero Dollar Game in this channel!");
            } else {
                $game = Game::query()->create([
                    'channel_id' => $message->channel_id,
                    'width' => 90,
                    'height' => 90,
                ]);

                $this->repository->create(OngoingGame::class, [
                    'game' => $game,
                    'createdRecently' => true,
                ])->initialize();
            }
        }

        if (str_contains($message->content, 'emergency_shutdown')) {
            $message->channel->sendMessage('Game has stopped.');

            $this->repository->destroyByTags(['zero_dollar_game', 'subroutine']);
        }
    }

    /**
     * Create the commands.
     *
     * @return void
     */
    protected function createCommands()
    {
        $rectangleOption = $this->createRectangleOption();
        $pixelOption = $this->createPixelOption();
        $pixelArtOption = $this->createPixelArtOption();

        $this->discord->application->commands->save(
            $this->discord->application->commands->create(CommandBuilder::new()
                ->setName('paint')
                ->setType(1)
                ->setDescription('Paint on the board!')
                ->addOption($rectangleOption)
                ->addOption($pixelOption)
                ->addOption($pixelArtOption)
                ->toArray()
            )
        )->then([$this->dispatcher, 'register']);
    }

    /**
     * Create a rectangle option.
     *
     * @return Option
     */
    protected function createRectangleOption()
    {
        $rectangleOption = (new Option($this->discord))
            ->setName('rectangle')
            ->setDescription('Paint a rectangle.')
            ->setType(Option::SUB_COMMAND);

        $topLeftX = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('bottom_left_x')
            ->setMinValue(0)
            ->setDescription('The top left x coordinate.')
            ->setRequired(true);

        $topLeftY = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('bottom_left_y')
            ->setMinValue(0)
            ->setDescription('The top left y coordinate.')
            ->setRequired(true);

        $bottomRightX = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('top_right_x')
            ->setMinValue(0)
            ->setDescription('The bottom right x coordinate.')
            ->setRequired(true);

        $bottomRightY = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('top_right_y')
            ->setMinValue(0)
            ->setDescription('The bottom right y coordinate.')
            ->setRequired(true);

        $color = (new Option($this->discord))
            ->setType(Option::STRING)
            ->setName('color')
            ->setDescription('The color name.')
            ->setRequired(true);

        $rectangleOption->addOption($topLeftX);
        $rectangleOption->addOption($topLeftY);
        $rectangleOption->addOption($bottomRightX);
        $rectangleOption->addOption($bottomRightY);
        $rectangleOption->addOption($color);

        return $rectangleOption;
    }

    /**
     * Create a pixel option.
     *
     * @return Option
     */
    protected function createPixelOption()
    {
        $pixelOption = (new Option($this->discord))
            ->setName('pixel')
            ->setDescription('Paint a single pixel.')
            ->setType(Option::SUB_COMMAND);

        $x = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('x')
            ->setMinValue(0)
            ->setDescription('The x coordinate.')
            ->setRequired(true);

        $y = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('y')
            ->setMinValue(0)
            ->setDescription('The y coordinate.')
            ->setRequired(true);

        $color = (new Option($this->discord))
            ->setType(Option::STRING)
            ->setName('color')
            ->setDescription('The color name.')
            ->setRequired(true);

        $pixelOption->addOption($x);
        $pixelOption->addOption($y);
        $pixelOption->addOption($color);

        return $pixelOption;
    }

    /**
     * Create a pixel art option.
     *
     * @return Option
     */
    protected function createPixelArtOption()
    {
        $pixelOption = (new Option($this->discord))
            ->setName('pixelart')
            ->setDescription('Paint your pixel art.')
            ->setType(Option::SUB_COMMAND);

        $x = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('x')
            ->setMinValue(0)
            ->setDescription('The x coordinate.')
            ->setRequired(true);

        $y = (new Option($this->discord))
            ->setType(Option::NUMBER)
            ->setName('y')
            ->setMinValue(0)
            ->setDescription('The y coordinate.')
            ->setRequired(true);

        $image = (new Option($this->discord))
            ->setType(Option::ATTACHMENT)
            ->setName('image')
            ->setDescription('The image to insert.')
            ->setRequired(true);

        $pixelOption->addOption($x);
        $pixelOption->addOption($y);
        $pixelOption->addOption($image);

        return $pixelOption;
    }
}
