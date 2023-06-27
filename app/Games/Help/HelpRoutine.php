<?php

namespace App\Games\Help;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Discord\Contracts\InteractionDispatcher;
use App\Routines\Concerns\HasId;
use App\Utils\StringCoder;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;

class HelpRoutine implements Routine
{
    use HasId;

    /**
     * Construct the zero dollar game routine.
     *
     * @param Discord $discord
     * @param Repository $repository
     * @param InteractionDispatcher $dispatcher
     * @param StringCoder $stringCoder
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
        protected InteractionDispatcher $dispatcher,
        protected StringCoder $stringCoder,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['help', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->dispatcher->register('help');
        $this->dispatcher->on('help', [$this, 'onCommand']);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        $this->dispatcher->removeListener('help', [$this, 'onCommand']);
    }

    /**
     * On message callback.
     *
     * @param Interaction $interaction
     * @return void
     */
    public function onCommand(Interaction $interaction)
    {
        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->addEmbed(
                    new Embed($this->discord, [
                        'type' => 'rich',
                        'title' => 'Whimsical dream operator - inhabitant of the kitty server!',
                        'description' => "Greetings! I can do stuff for you!\n\nHere are some of my commands:\n\n`/rps`: Play a game of rock paper scissors with me or a friend!\n`/whoami`: I tell you who you are, in case you forgot your own name!\n`/paint`: You can use this on the <#835802931801620500> channel!\n`/cat_clicker`: Click on a cat or something.\n\nHere's a picture of a cat!",
                        'color' => 0x0048ff,
                        'thumbnail' => [
                            'url' => 'https://cdn.discordapp.com/avatars/730864711113113601/d945e5683668fceba4d8055fc52203b5.png?size=1024',
                            'height' => 0,
                            'width' => 0
                        ],
                        'image' => [
                            'url' => collect([
                                'https://media.discordapp.net/attachments/811274984121958441/1121118936767934536/20230621_124613.jpg?width=501&height=669',
                                'https://media.discordapp.net/attachments/811274984121958441/1108500887774711969/20230304_101324.jpg?width=892&height=669',
                                'https://media.discordapp.net/attachments/811274984121958441/1108500945064693850/20221025_140031.jpg?width=892&height=669',
                                'https://media.discordapp.net/attachments/811274984121958441/1108501042242527362/20220829_012616.jpg?width=892&height=669',
                                'https://media.discordapp.net/attachments/811274984121958441/1108501097695432714/20220830_135309.jpg?width=501&height=669'
                            ])->random()
                        ],
                    ]),
                )
        );
    }
}
