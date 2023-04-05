<?php

namespace App\Games\ZeroDollarGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    /**
     * Relationship with all recent pixels.
     *
     * @return HasMany
     */
    public function recentPixels()
    {
        return $this->hasMany(Pixel::class)->joinSub(
            Pixel::query()
                ->select('id')
                ->selectRaw('MAX(`created_at`)')
                ->groupBy('posx')
                ->groupBy('posy')
                ->groupBy('id')
                ->getQuery(),
            'inner_pixels',
            'inner_pixels.id',
            'pixels.id',
        );
    }
}
