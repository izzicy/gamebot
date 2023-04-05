<?php

namespace App\Games\ZeroDollarGame;

use App\Games\ZeroDollarGame\Models\Game;
use Intervention\Image\ImageManagerStatic;

class GameDrawer
{
    /**
     * Draw the zdg.
     *
     * @param Game $game
     * @param bool $withGrid
     * @return resource
     */
    public function draw($game, $withGrid = false)
    {
        $width = $game->width;
        $height = $game->height;
        $pixelSize = 10;
        $canvas = ImageManagerStatic::canvas($width * $pixelSize, $height * $pixelSize, '#ffffff');

        $game->recentPixels()->get()->each(function($pixel) use ($pixelSize, $canvas, $game) {
            $x = $pixel->posx;
            $y = $game->height - $pixel->posy - 1;

            $canvas->insert(
                ImageManagerStatic::canvas($pixelSize, $pixelSize, $pixel->getRgb()),
                'top-left',
                $x * $pixelSize,
                $y * $pixelSize
            );
        });

        $cacheFilepath = $this->cacheFile($game);

        $canvas->save($cacheFilepath);

        if ($withGrid) {
            $canvas = $this->applyGrid($game, $canvas);
        }

        return $canvas->getCore();
    }

    /**
     * Draw the zdg.
     *
     * @param Game $game
     * @param \Intervention\Image\Image $canvas
     * @return \Intervention\Image\Image
     */
    protected function applyGrid($game, $canvas)
    {
        $width = $game->width;
        $height = $game->height;
        $pixelSize = 10;

        foreach (range(1, $width - 1) as $x) {
            $canvas->rectangle($x * $pixelSize, 0, $x * $pixelSize, $height * $pixelSize, function ($draw) {
                $draw->background('rgba(0, 0, 0, 0)');
                $draw->border(1, 'rgba(0, 0, 0, 0.07)');
            });
        }

        foreach (range(1, $height - 1) as $y) {
            $canvas->rectangle(0, $y * $pixelSize, $width * $pixelSize, $y * $pixelSize, function ($draw) {
                $draw->background('rgba(0, 0, 0, 0)');
                $draw->border(1, 'rgba(0, 0, 0, 0.07)');
            });
        }

        $outerCanvas = ImageManagerStatic::canvas(($width + 2) * $pixelSize, ($height + 2) * $pixelSize, '#36393F');

        foreach (range(0, $width - 1) as $x) {
            $outerCanvas->text($x + 1, round($pixelSize * 1.5) + $x * $pixelSize, round($pixelSize / 2), function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });

            $outerCanvas->text($x + 1, round($pixelSize * 1.5) + $x * $pixelSize, round($pixelSize * 1.5) + $height * $pixelSize, function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });
        }

        foreach (range(0, $height - 1) as $y) {
            $outerCanvas->text($height - $y, round($pixelSize / 2), round($pixelSize * 1.5) + $y * $pixelSize, function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });

            $outerCanvas->text($height - $y, round($pixelSize * 1.5) + $width * $pixelSize, round($pixelSize * 1.5) + $y * $pixelSize, function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });
        }

        $outerCanvas->insert($canvas, 'top-left', $pixelSize, $pixelSize);

        return $outerCanvas;
    }

    /**
     * Get the cache file.
     *
     * @param Game $game
     * @return string
     */
    protected function cacheFile($game)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . sha1($game->getKey() . 'zdg-cache-file') . '.jpg';
    }
}
