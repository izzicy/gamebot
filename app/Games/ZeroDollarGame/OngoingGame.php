<?php

namespace App\Games\ZeroDollarGame;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Games\ZeroDollarGame\Models\Game;
use App\Games\ZeroDollarGame\Models\Pixel;
use App\Routines\Concerns\HasId;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;
use Discord\WebSockets\Event;
use Intervention\Image\ImageManagerStatic;
use Spatie\Color\Exceptions\InvalidColorValue;
use Spatie\Color\Factory;
use Spatie\Color\Hex;
use Spatie\Color\Hsl;

class OngoingGame implements Routine
{
    use HasId;

    /**
     * An ongoing game instance.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param Game $game
     * @param GameDrawer $gameDrawer
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
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
        $this->drawAndSendClearGame();

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
        if ($message->channel_id !== $this->game->channel_id) {
            return;
        }

        $lines = explode("\n", $message->content);

        foreach ($lines as $line) {
            if (preg_match('/(paint|color|colour|pixel) *(?P<arguments>.*)/i', $line, $matches)) {
                $this->handlePaintCommand($message->author, $matches['arguments']);
            }
        }
    }

    /**
     * Draw and send the clear game.
     *
     * @return ExtendedPromiseInterface
     */
    protected function drawAndSendClearGame()
    {
        $channel = $this->discord->getChannel($this->game->channel_id);
        $image = ImageManagerStatic::make($this->gameDrawer->draw($this->game));
        $path = tempnam(sys_get_temp_dir(), '') . '.png';
        $image->save($path);

        return $channel->sendMessage(
            MessageBuilder::new()
                ->addFile($path)
        );
    }

    /**
     * Handle the paint command.
     *
     * @param User $user
     * @param string $command
     * @return void
     */
    protected function handlePaintCommand($user, $command)
    {
        if (str_contains($command, 'and') || str_contains($command, ',')) {
            $parts = preg_split('/(\W+and\W+)| *, */i', $command);

            foreach ($parts as $part) {
                $this->handlePaintCommand($user, $command);
            }

            return;
        }

        $this->handleRangePaintCommand($user, $command);
        $this->handleSinglePaintCommand($user, $command);
    }

    /**
     * Handle a range paint command.
     *
     * @param User $user
     * @param string $command
     * @return void
     */
    protected function handleRangePaintCommand($user, $command)
    {
        if (preg_match('/(?P<x1>\d+) +(?P<y1>\d+) +to +(?P<x2>\d+) +(?P<y2>\d+)( +(?P<modifier>dark|light)? *(?P<choice>[a-z0-9 #]+))?/i', $command, $matches)) {
            $x1 = $matches['x1'];
            $y1 = $matches['y1'];
            $x2 = $matches['x2'];
            $y2 = $matches['y2'];
            $modifier = $matches['modifier'] ?? null;
            $choice = $matches['choice'] ?? null;

            foreach (range($x1, $x2) as $x) {
                foreach (range($y1, $y2) as $y) {
                    $gatheredPixels[] = [$x, $y];
                }
            }

            if ($choice) {
                $this->paintPixels($user, $modifier, $gatheredPixels, $choice);
            }
        }
    }

    /**
     * Handle a single paint command.
     *
     * @param User $user
     * @param string $command
     * @return void
     */
    protected function handleSinglePaintCommand($user, $command)
    {
        if (preg_match('/(?P<x>\d+) +(?P<y>\d+)( +(?P<modifier>dark|light)? *(?P<choice>[a-z0-9 #]+))?/i', $command, $matches)) {
            $x = $matches['x'];
            $y = $matches['y'];
            $modifier = $matches['modifier'] ?? null;
            $choice = $matches['choice'] ?? null;

            $gatheredPixels[] = [$x, $y];

            if ($choice) {
                $this->paintPixels($user, $modifier, $gatheredPixels, $choice);
            }
        }
    }

    /**
     * Paint the given pixels.
     *
     * @param User $user
     * @param string $modifier
     * @param array[]int[] $pixels
     * @param string $choice
     * @return void
     */
    protected function paintPixels($user, $modifier, $pixels, $choice)
    {
        foreach ($pixels as $pixel) {
            $this->paintPixel($user, $modifier, $pixel[0], $pixel[1], $choice);
        }
    }

    /**
     * Paint the given pixel.
     *
     * @param User $user
     * @param string $modifier
     * @param string|int $x
     * @param string|int $y
     * @param string $choice
     * @return void
     */
    protected function paintPixel($user, $modifier, $x, $y, $choice)
    {
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
