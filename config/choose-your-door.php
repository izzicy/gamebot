<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Channels For Play
    |--------------------------------------------------------------------------
    |
    | List of channels that are allowed to play the Choose Your Door game.
    |
    */

    'allowed_channels' => explode(',', env('CHOOSE_YOUR_DOOR_ALLOWED_CHANNELS', '')),

    /*
    |--------------------------------------------------------------------------
    | Game Time Interval
    |--------------------------------------------------------------------------
    |
    | After which time interval a new game should be started.
    |
    */

    'game_interval' => 3600 * 8,

    /*
    |--------------------------------------------------------------------------
    | Game Reactions
    |--------------------------------------------------------------------------
    |
    | List of choose your game reactions.
    |
    */

    'reactions' => [
        "1️⃣",
        "2️⃣",
        "3️⃣",
        "4️⃣",
        "5️⃣",
        "6️⃣",
        "7️⃣",
        "8️⃣",
        "9️⃣",
        "🔟",
        "🇭",
    ],

    /*
    |--------------------------------------------------------------------------
    | Win Emojis
    |--------------------------------------------------------------------------
    |
    | List of win emojis.
    |
    */

    'win_emojis' => [
        storage_path('choose-your-door/emoji/sunglasses.png'),
        storage_path('choose-your-door/emoji/laughing.png'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Lose Emojis
    |--------------------------------------------------------------------------
    |
    | List of lose emojis.
    |
    */

    'lose_emojis' => [
        storage_path('choose-your-door/emoji/angry.png'),
        storage_path('choose-your-door/emoji/crying.png'),
        storage_path('choose-your-door/emoji/scream.png'),
    ],

];
