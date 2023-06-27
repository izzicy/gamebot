<?php

namespace App\Games\ZeroDollarGame;

use App\Contracts\Discord\CommandSetup;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Option;
use React\Promise\Promise;

class ZeroDollarGameCommandSetup implements CommandSetup
{
    /**
     * @inheritdoc
     */
    public function __invoke(Discord $discord): Promise
    {
        $rectangleOption = $this->createRectangleOption($discord);
        $pixelOption = $this->createPixelOption($discord);
        $pixelArtOption = $this->createPixelArtOption($discord);

        return $discord->application->commands->save(
            $discord->application->commands->create(CommandBuilder::new()
                ->setName('paint')
                ->setType(1)
                ->setDescription('Paint on the board!')
                ->addOption($rectangleOption)
                ->addOption($pixelOption)
                ->addOption($pixelArtOption)
                ->toArray()
            )
        );
    }

    /**
     * Create a rectangle option.
     *
     * @param Discord $discord
     * @return Option
     */
    protected function createRectangleOption(Discord $discord)
    {
        $rectangleOption = (new Option($discord))
            ->setName('rectangle')
            ->setDescription('Paint a rectangle.')
            ->setType(Option::SUB_COMMAND);

        $topLeftX = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('bottom_left_x')
            ->setMinValue(0)
            ->setDescription('The top left x coordinate.')
            ->setRequired(true);

        $topLeftY = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('bottom_left_y')
            ->setMinValue(0)
            ->setDescription('The top left y coordinate.')
            ->setRequired(true);

        $bottomRightX = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('top_right_x')
            ->setMinValue(0)
            ->setDescription('The bottom right x coordinate.')
            ->setRequired(true);

        $bottomRightY = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('top_right_y')
            ->setMinValue(0)
            ->setDescription('The bottom right y coordinate.')
            ->setRequired(true);

        $color = (new Option($discord))
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
     * @param Discord $discord
     * @return Option
     */
    protected function createPixelOption(Discord $discord)
    {
        $pixelOption = (new Option($discord))
            ->setName('pixel')
            ->setDescription('Paint a single pixel.')
            ->setType(Option::SUB_COMMAND);

        $x = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('x')
            ->setMinValue(0)
            ->setDescription('The x coordinate.')
            ->setRequired(true);

        $y = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('y')
            ->setMinValue(0)
            ->setDescription('The y coordinate.')
            ->setRequired(true);

        $color = (new Option($discord))
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
     * @param Discord $discord
     * @return Option
     */
    protected function createPixelArtOption(Discord $discord)
    {
        $pixelOption = (new Option($discord))
            ->setName('pixelart')
            ->setDescription('Paint your pixel art.')
            ->setType(Option::SUB_COMMAND);

        $x = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('x')
            ->setMinValue(0)
            ->setDescription('The x coordinate.')
            ->setRequired(true);

        $y = (new Option($discord))
            ->setType(Option::NUMBER)
            ->setName('y')
            ->setMinValue(0)
            ->setDescription('The y coordinate.')
            ->setRequired(true);

        $image = (new Option($discord))
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
