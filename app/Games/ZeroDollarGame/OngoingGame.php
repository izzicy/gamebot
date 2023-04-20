<?php

namespace App\Games\ZeroDollarGame;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Games\ZeroDollarGame\Models\Game;
use App\Games\ZeroDollarGame\Models\Pixel;
use App\Routines\Concerns\HasId;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Attachment;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\User;
use Intervention\Image\ImageManagerStatic;
use Spatie\Color\Exceptions\InvalidColorValue;
use Spatie\Color\Factory;
use Spatie\Color\Hex;
use Spatie\Color\Hsl;
use Spatie\Color\Rgb;

class OngoingGame implements Routine
{
    use HasId;

    /**
     * An ongoing game instance.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param InteractionDispatcher $dispatcher
     * @param Game $game
     * @param GameDrawer $gameDrawer
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected InteractionDispatcher $dispatcher,
        protected Game $game,
        protected GameDrawer $gameDrawer,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['zero_dollar_game', 'subroutine', 'channel:' . $this->game->channel_id];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->sendGame(true);
        $this->sendGame();
        $this->createCommands();

        $this->dispatcher->on('paint', [$this, 'onCommand']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->dispatcher->removeListener('paint', [$this, 'onCommand']);

        $this->repository->destroyByTags(['zero_dollar_game', 'subroutine']);
    }

    /**
     * On message callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onCommand(Interaction $interaction)
    {
        if ($interaction->channel_id !== $this->game->channel_id) {
            return;
        }

        $rectangle = $interaction->data->options->offsetGet('rectangle');
        $pixel = $interaction->data->options->offsetGet('pixel');
        $pixelart = $interaction->data->options->offsetGet('pixelart');

        if ($rectangle) {
            $this->paintRectangle(
                $interaction->user,
                $rectangle->options->offsetGet('bottom_left_x')->value - 1,
                $rectangle->options->offsetGet('bottom_left_y')->value - 1,
                $rectangle->options->offsetGet('top_right_x')->value - 1,
                $rectangle->options->offsetGet('top_right_y')->value - 1,
                $rectangle->options->offsetGet('color')->value,
            );
        }

        if ($pixel) {
            $this->paintPixel(
                $interaction->user,
                $pixel->options->offsetGet('x')->value - 1,
                $pixel->options->offsetGet('y')->value - 1,
                $pixel->options->offsetGet('color')->value,
            );
        }

        if ($pixelart) {
            $attachment = $interaction->data->resolved->attachments->first();

            $this->paintImage(
                $interaction->user,
                $pixelart->options->offsetGet('x')->value - 1,
                $pixelart->options->offsetGet('y')->value - 1,
                $attachment,
            );
        }

        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->setContent('Your paint is my command!')
        );

        $this->sendGame(true);
        $this->sendGame();
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

    /**
     * Draw and send the clear game.
     *
     * @return ExtendedPromiseInterface
     */
    protected function sendGame($includeGrid = false)
    {
        $channel = $this->discord->getChannel($this->game->channel_id);
        $image = ImageManagerStatic::make($this->gameDrawer->draw($this->game, $includeGrid));
        $path = tempnam(sys_get_temp_dir(), '') . '.png';
        $image->save($path);

        return $channel->sendMessage(
            MessageBuilder::new()
                ->addFile($path)
        );
    }

    /**
     * Handle a image paint command.
     *
     * @param User $user
     * @param int $baseX
     * @param int $baseY
     * @param Attachment
     * @return void
     */
    protected function paintImage($user, $baseX, $baseY, $attachment)
    {
        if ( ! $attachment->content_type === 'image/jpg' && ! $attachment->content_type === 'image/png') {
            return;
        }

        $image = ImageManagerStatic::make($attachment->url);
        $withWhite = true;
        $height = $image->getHeight();

        foreach (range(0, $image->getWidth() - 1) as $x) {
            foreach (range(0, $image->getHeight() - 1) as $y) {
                $colour = $image->pickColor($x, $y);

                $canBePainted = collect($colour)->last() == 1;

                if (false) {
                    $canBePainted = $canBePainted && $colour != [255, 255, 255, 1];
                }

                $colourString = (string) (new Rgb($colour[0], $colour[1], $colour[2]))->toHex();

                if ($canBePainted) {
                    $this->paintPixel($user, $baseX + $x, $baseY + $height - $y - 1, $colourString);
                }
            }
        }
    }

    /**
     * Handle a range paint command.
     *
     * @param User $user
     * @param string $command
     * @return void
     */
    protected function paintRectangle($user, $x1, $y1, $x2, $y2, $color)
    {
        foreach (range($x1, $x2) as $x) {
            foreach (range($y1, $y2) as $y) {
                $gatheredPixels[] = [$x, $y];
            }
        }

        if ($x1 < 0 || $x1 >= $this->game->width) {
            return;
        }

        if ($y1 < 0 || $y1 >= $this->game->height) {
            return;
        }

        if ($x2 < 0 || $x2 >= $this->game->width) {
            return;
        }

        if ($y2 < 0 || $y2 >= $this->game->height) {
            return;
        }

        $this->paintPixels($user, $gatheredPixels, $color);
    }

    /**
     * Paint the given pixels.
     *
     * @param User $user
     * @param array[]int[] $pixels
     * @param string $color
     * @return void
     */
    protected function paintPixels($user, $pixels, $color)
    {
        foreach ($pixels as $pixel) {
            $this->paintPixel($user, $pixel[0], $pixel[1], $color);
        }
    }

    /**
     * Paint the given pixel.
     *
     * @param User $user
     * @param string|int $x
     * @param string|int $y
     * @param string $color
     * @return void
     */
    protected function paintPixel($user, $x, $y, $color)
    {
        if ($x < 0 || $x >= $this->game->width) {
            return;
        }

        if ($y < 0 || $y >= $this->game->height) {
            return;
        }

        preg_match('/(?P<modifier>dark|light)? *(?P<choice>[a-z0-9 #]+)/i', $color, $matches);

        $modifier = $matches['modifier'] ?? null;
        $choice = $matches['choice'];

        $normalizedChoice = preg_replace('/[^a-z0-9]/', '', strtolower($choice));
        $colour = config('zero-dollar-game.aliases.' . $normalizedChoice, function() use ($choice) {
            try {
                if (preg_match('/^([0-9a-f]{6}|[0-9a-f]{3})$/i', $choice)) {
                    return (string) Factory::fromString('#' . $choice)->toHex();
                }

                return (string) Factory::fromString($choice)->toHex();
            }
            catch (InvalidColorValue $e) {
                return null;
            }
        });

        if ($colour) {
            if ($modifier) {
                $colour = $this->adjustBrightness($colour, $modifier === 'dark' ? -20 : 20);
            }

            $rgb = Hex::fromString($colour)->toRgb();

            Pixel::query()->create([
                'game_id' => $this->game->getKey(),
                'user_id' => $user->id,
                'posx' => (int) $x,
                'posy' => (int) $y,
                'r' => $rgb->red(),
                'g' => $rgb->green(),
                'b' => $rgb->blue(),
            ]);
        }
    }

    /**
     * Adjust the color brightness
     *
     * @param string $hexCode
     * @param int $adjustPercent
     * @return string
     */
    protected function adjustBrightness($hexCode, $adjustPercent) {
        $hsl = Hex::fromString($hexCode)->toHsl();

        $lightness = max(0, min(100, $hsl->lightness() + $adjustPercent));

        return (string) (new Hsl($hsl->hue(), $hsl->saturation(), $lightness))->toHex();
    }
}
