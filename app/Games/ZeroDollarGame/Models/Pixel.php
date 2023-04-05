<?php

namespace App\Games\ZeroDollarGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pixel extends Model
{
    const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zdg_pixels';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Set the rgb color.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return $this
     */
    public function setRgb($red, $green, $blue)
    {
        $this->r = $red;
        $this->g = $green;
        $this->b = $blue;

        return $this;
    }

    /**
     * Get the rgb color.
     *
     * @return int[]
     */
    public function getRgb()
    {
        return [$this->r, $this->g, $this->b];
    }

    /**
     * Relationship with the game.
     *
     * @return BelongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
