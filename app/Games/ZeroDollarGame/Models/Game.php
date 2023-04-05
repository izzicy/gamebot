<?php

namespace App\Games\ZeroDollarGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'zero_dollar_game';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relationship with pixels.
     *
     * @return HasMany
     */
    public function pixels()
    {
        return $this->hasMany(Pixel::class);
    }
}
