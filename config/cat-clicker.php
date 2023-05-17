<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Replacement Count
    |--------------------------------------------------------------------------
    |
    */

    'replacement_count' => 5,

    /*
    |--------------------------------------------------------------------------
    | Cat Pictures
    |--------------------------------------------------------------------------
    |
    | List of cat pictures.
    |
    */

    'pictures' => [
        storage_path('cat-clicker/Mirror_cats.png'),
        storage_path('cat-clicker/Roaning_sleeping_behind_me_on_the_floor.jpg'),
        storage_path('cat-clicker/20220829_012616.jpg'),
        storage_path('cat-clicker/20220830_135309.jpg'),
        storage_path('cat-clicker/20221025_140031.jpg'),
        storage_path('cat-clicker/20230304_101324.jpg'),
        storage_path('cat-clicker/20230307_121658.jpg'),
        storage_path('cat-clicker/20230509_141346.jpg'),
        storage_path('cat-clicker/big_cat.png'),
        [
            'image' => storage_path('cat-clicker/Cat_vampire.png'),
            'comments' => [
                'Be careful not to let them bite when you pet her!',
                'I want to suck your pets!',
                'Bleh bleh bleh!',
                'PET THEM BEFORE THEY ATTACK!',
            ],
        ],
        [
            'image' => storage_path('cat-clicker/nigiri_sushi.png'),
            'comments' => [
                'Come on! pet the. . . cat?',
                'Um, is this a cat?',
                'SUSHI! wait why are we petting this?',
                'I want to eat this, not pet it!',
            ],
        ],
    ],
];
