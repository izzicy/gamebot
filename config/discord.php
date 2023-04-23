<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Discord Authorisation Token
    |--------------------------------------------------------------------------
    |
    | The authorisation tokenfor Discord.
    |
    */

    'token' => env('DISCORD_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Registered Admin User
    |--------------------------------------------------------------------------
    |
    | Users who have special privileges.
    |
    */

    'admins' => explode(',', env('ADMINS', '')),

];
