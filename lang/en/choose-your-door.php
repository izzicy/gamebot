<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Choose Your Door Prompt
    |--------------------------------------------------------------------------
    |
    */

    'prompt' => [
        'Choose a door! Any door!',
        'Choose your door!',
        'Take your pick!',
        'A door can be chosen!',
        'Pick a door!',
        'Make your choice!',
        'Choose wisely!',
        'Time to choose!',
        'Make your choice!',
    ],

    /*
    |--------------------------------------------------------------------------
    | Win phrases
    |--------------------------------------------------------------------------
    |
    | A list of grouped win phrases.
    |
    */

    'win_phrases' => [
        'default' => [
            '{1} :usernames won a lifetime supply of pizza and danced with joy, declaring every day "Pizza Day" for the rest of their life.
            |[2,*] :usernames won a lifetime supply of pizza and danced with joy, declaring every day "Pizza Day" for the rest of their lives.',
            'Winning the lottery allowed :usernames to retire to a private island, where they spent their days sipping cocktails and soaking up the sun.',
            ':usernames won a trip to space and returned to Earth with a newfound appreciation for the universe and their place in it.',
            ':usernames won a giant golden statue of their pet cat, which they proudly displayed in the town square.',
            ':usernames won a million dollars and used the money to build a giant rollercoaster in their backyard, inviting the whole neighborhood to ride it.',
            'Winning a year\'s worth of free movie tickets allowed :usernames to finally watch every movie on their bucket list.',
            ':usernames won a lifetime supply of ice cream and spent the next week in a sugar-induced haze, trying every flavor under the sun.',
            'Winning a luxury yacht allowed :usernames to sail the seas in style, hosting epic parties and living like royalty.',
            ':usernames won a mansion in the Hollywood Hills, where they hobnobbed with celebrities and threw lavish parties every weekend.',

            // Thing / places

            '{1} :usernames stumbled upon :thing :place and was instantly imbued with superhuman powers!
            |[2,*] :usernames stumbled upon :thing :place and were instantly imbued with superhuman powers!',
            '{1} After months of searching, :usernames finally discovered :thing :place, and their mind were blown wide open with newfound knowledge!
            |[2,*] After months of searching, :usernames finally discovered :thing :place, and their minds were blown wide open with newfound knowledge!',
            '{1} When :usernames found themself :place and found :thing, they were overcome with joy and started dancing like madmen!
            |[2,*] When :usernames found themselves :place and found :thing, they were overcome with joy and started dancing like madmen!',
            ':usernames stumbled upon :thing :place and were suddenly transported to a magical land filled with unicorns and rainbows!',
            ':usernames discovered :thing :place and gained the ability to play any instrument, leading to a wild jam session that lasted for days!',
            'When out looking for :thing :place, :usernames instead found the lost works of Archimedes!',
            'During their time at :place, :usernames stumbled upon :thing!',
        ],
        'castle_heroics' => [

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Lose phrases
    |--------------------------------------------------------------------------
    |
    | A list of grouped lose phrases.
    |
    */

    'lose_phrases' => [
        'default' => [
            ':usernames lost their hair in a freak accident involving a rogue hair dryer and a swarm of bees, and had to wear wigs for weeks.',
            ':usernames got struck by lightning and developed superpowers, but found that their newfound abilities only caused chaos and destruction.',
            '{1} :usernames was trapped in an elevator for hours, subsisting only on stale crackers and warm water.
            |[2,*] :usernames were trapped in an elevator for hours, subsisting only on stale crackers and warm water.',
            '{1} :usernames accidentally boarded a plane to a remote island where they were forced to fend for themself in the wilderness for weeks.
            |[2,*] :usernames accidentally boarded a plane to a remote island where they were forced to fend for themselves in the wilderness for weeks.',
            ':usernames accidentally stumbled into a portal to a parallel universe, where everything was upside down and nothing made sense.',
            ':usernames accidentally opened a cursed sarcophagus, unleashing a mummy that haunted them for weeks and causing them to have the worst luck imaginable.',
            '{1} :usernames was transported to a dimension where everything was made out of candy, but soon discovered that their sweet surroundings were actually quite dangerous.
            |[2,*] :usernames were transported to a dimension where everything was made out of candy, but soon discovered that their sweet surroundings were actually quite dangerous.',
            '{1} :usernames was cursed with a musical number that they couldn\'t stop singing, making it impossible to communicate with others without breaking into song.
            |[2,*] :usernames were cursed with a musical number that they couldn\'t stop singing, making it impossible to communicate with others without breaking into song.',
            '{1} :usernames was cursed by a vengeful witch and turned into a garden gnome, unable to move or communicate with the outside world.
            |[2,*] :usernames were cursed by a vengeful witch and turned into garden gnomes, unable to move or communicate with the outside world.',
            '{1} :usernames was struck by a freak lightning storm that caused them to shrink down to the size of ants, facing danger at every turn as they navigated through a world full of giant creatures.
            |[2,*] :usernames were struck by a freak lightning storm that caused them to shrink down to the size of ants, facing danger at every turn as they navigated through a world full of giant creatures.',

            // Thing / Places

            'After :usernames discovered :thing :place, they were plagued by a series of bizarre accidents and unexplained phenomena, all of which seemed to be connected to their discovery!',
            ':usernames found :thing :place and uncovered a deadly booby trap that threatened to unleash a swarm of venomous snakes upon them!',
            'After :usernames found :thing :place, they unwittingly stumbled into a war zone, where they were caught in the crossfire between rival factions!',
            ':usernames found :thing :place, and it turned out to be a cursed object that brought all of their worst fears and nightmares to life!',
            ':usernames found :thing while they were :place and it was terrible!',
            'During their trip :place, :usernames found :thing. How outrageous!',
            'After being lost :place, :usernames stumbled upon :thing.',
        ],

        'castle_horoics' => [

        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Win Substitutes
    |--------------------------------------------------------------------------
    |
    | A list of phrases that can be substituted.
    |
    */

    'win_substitutes' => [
        'thing' => [
            '99 bottles of beer',
            'the giant mushroom',
            'a large apple',
            'a cute cat',
            'a pristine $100 bill',
            'an enourmous gold coin',
            'a tastefull red potion',
            'a joyfull zebra',
            'a mouth-watering chocolate bar',
            'a high quality safety vest',
            'the sandwhich',
        ],
        'place' => [
            'at home',
            'at the highway',
            'at the white house',
            'at the laboratory',
            'at the local laundromat',
            'at the local supermarket',
            'at Mount Everest',
            'in Amsterdam',
            'in an amusement park',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Lose Substitutes
    |--------------------------------------------------------------------------
    |
    | A list of phrases that can be substituted.
    |
    */

    'lose_substitutes' => [
        'thing' => [
            'a dumb drawing of a car',
            'an evil cat',
            'a crumpled up $0 bill',
            'the hideous copper coin',
            'an unappealing wig',
            'some shady plant',
            'a cracked coffee cup',
            'some sack of dull marbles',
            'a bottle of oderless perfume',
            'a funny-looking ventilation duct',
        ],

        'place' => [
            'at the evil laboratory',
            'at an active vulcano',
            'in the deepest depths of the ocean',
            'at the place in the forest',
            'in the evil supermarket',
            'at some structurally failing bridge',
            'at some place in the universe',
            'at some place far from home',
            'in Ohio',
            'at the moon',
            'at the Foobar',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cheater Callouts
    |--------------------------------------------------------------------------
    |
    | List of cheater callouts.
    |
    */

    'cheater_callouts' => [
        ':usernames cheated at this game!',
        ':usernames tried to cheat at this game!',
        ':usernames thought they were clever!',
        ':usernames made an attempt to circumvent the rules of the game!',
    ],
];
