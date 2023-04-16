<?php

namespace App\Games\ChooseYourDoor\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'choose_your_door';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
