<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Channels For Play
    |--------------------------------------------------------------------------
    |
    | List of channels that are allowed to play the zero dollar game.
    |
    */

    'allowed_channels' => explode(',', env('ZERO_DOLLAR_GAME_ALLOWED_CHANNELS', '')),
];
