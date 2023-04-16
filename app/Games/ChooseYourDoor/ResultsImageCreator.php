<?php

namespace App\Games\ChooseYourDoor;

use Illuminate\Support\Collection;
use Intervention\Image\ImageManagerStatic as Image;

class ResultsImageCreator
{
    /**
     * Create the response image.
     *
     * @param ReactionCollector $reactions
     * @param array $correctChoices
     * @param int $numberOfDoors
     * @return string
     */
    public function create($reactions, $correctChoices, $numberOfDoors)
    {
        $background = Image::make(storage_path('choose-your-door/background.png'));
        $doorImage = Image::make(storage_path('choose-your-door/door.png'));
        $offsetY = 74;

        $backgroundHeight = $background->getHeight();
        $backgroundWidth = $background->getWidth();

        $ratio = $doorImage->getWidth() / $doorImage->getHeight();

        $doorHeight = round($backgroundHeight * 0.5);
        $doorWidth = round($ratio * $doorHeight);
        $doorImage->resize($doorWidth, $doorHeight);

        $userPerDoor = $reactions->getUsersByReactions();

        for ($i = 0; $i < $numberOfDoors; $i += 1) {
            $doorX = round($backgroundWidth / ($numberOfDoors + 1) * ($i + 1) - ($doorWidth / 2));
            $doorY = round($backgroundHeight - $doorHeight - $offsetY);

            $background->insert(
                $doorImage,
                'top-left',
                $doorX,
                $doorY
            );

            $this->insertEmoji($background, $i, $correctChoices, $doorX, $doorY, $doorWidth, $doorHeight);
            $this->insertUserAvatars(
                $background,
                collect($userPerDoor[$i] ?? null),
                $doorX,
                $doorY,
                $doorWidth,
                $doorHeight
            );
        }

        $path = tempnam(sys_get_temp_dir(), 'image') . '.png';

        $background->save($path);

        return $path;
    }

    /**
     * Insert a emoji.
     *
     * @param \Intervention\Image\Image $background
     * @param int|string $i
     * @param array $correctChoices
     * @param int $doorX
     * @param int $doorY
     * @param int $doorWidth
     * @param int $doorHeight
     * @return void
     */
    protected function insertEmoji($background, $i, $correctChoices, $doorX, $doorY, $doorWidth, $doorHeight)
    {
        $emoji = Image::make(
            $this->doorIndexIsWinning($i, $correctChoices)
            ? $this->getWinEmoji()
            : $this->getLoseEmoji()
        );

        $emojiWidth = $doorWidth * 0.5;
        $emojiX = round($doorX + ($emojiWidth / 2));
        $emojiY = round($doorY - $emojiWidth - $doorHeight * 0.1);

        $emoji->resize($emojiWidth, $emojiWidth);

        $background->insert(
            $emoji,
            'top-left',
            $emojiX,
            $emojiY
        );
    }

    /**
     * Insert the user avatars.
     *
     * @param \Intervention\Image\Image $background
     * @param Collection $users
     * @param int $doorX
     * @param int $doorY
     * @param int $doorWidth
     * @param int $doorHeight
     * @return void
     */
    protected function insertUserAvatars($background, $users, $doorX, $doorY, $doorWidth, $doorHeight)
    {
        $profileMask = Image::make(storage_path('choose-your-door/profile.png'));
        $userSize = floor($doorWidth * 0.333);

        $profileMask->resize($userSize, $userSize);

        foreach ($users as $index => $user) {
            $userImage = Image::make($user->avatar);

            $x = $index % 3;
            $y = floor($index / 3);

            $userImage->resize($userSize, $userSize);
            $userImage->mask($profileMask);
            $userX = round($doorX + $userSize * $x);
            $userY = round($doorY + $doorHeight + $userSize * $y);

            $background->insert(
                $userImage,
                'top-left',
                $userX,
                $userY
            );
        }
    }

    /**
     * Check if the given door index is winning.
     *
     * @param int|string $index
     * @param array $correctChoices
     * @return bool
     */
    protected function doorIndexIsWinning($index, $correctChoices)
    {
        return in_array($index, $correctChoices);
    }

    /**
     * Get a win emoji file path.
     *
     * @return string
     */
    protected function getWinEmoji()
    {
        $emojis = config('choose-your-door.win_emojis');

        return collect($emojis)->random();
    }

    /**
     * Get a win emoji file path.
     *
     * @return string
     */
    protected function getLoseEmoji()
    {
        $emojis = config('choose-your-door.lose_emojis');

        return collect($emojis)->random();
    }
}
