<?php

namespace App\Routines;

use App\Contracts\Routines\Repository;
use App\Contracts\Routines\Routine;
use App\Routines\Concerns\HasId;
use Discord\Discord;
use Discord\Helpers\Collection as HelpersCollection;
use Illuminate\Support\Facades\Log;

class ResolveRoutine implements Routine
{
    use HasId;

    /**
     * All active games.
     *
     * @var Collection
     */
    protected $games;

    /** @var HelpersCollection */
    protected $messages;

    /**
     * Construct the zero dollar game routine.
     *
     * @param Discord $discord
     * @param Repository $repository
     */
    public function __construct(
        protected Discord $discord,
        protected Repository $repository,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['test', 'main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->getHistory()->then(fn ($messages) => $this->getMessages($messages));
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
    }

    protected function getMessages(HelpersCollection $messages)
    {
        if (empty($this->messages)) {
            $this->messages = $messages;
        }

        if ($messages->count() === 0) {
            dump('finish');
            $this->finish();
        } else {
            $this->getHistory($messages->last())->then(fn ($messages) => $this->getMessages($messages));;
        }
    }

    protected function finish()
    {
        try {
            $messages = array_reverse($this->messages->toArray());
            $missing = [];

            for ($i = 1; $i <= 9756; $i += 1) {
                $missing['n' . $i] = true;
            }

            foreach ($messages as $message) {
                if (preg_match('/\d+/', $message->content, $matches)) {
                    $number = (int)$matches[0];

                    unset($missing['n' . $number]);
                }
            }

            file_put_contents(base_path('output.txt'), implode("\n", array_keys($missing)));
        }
        catch (\Throwable $e) {
            print($e->getMessage());
            Log::error($e->getMessage());
        }
    }

    protected function getHistory($after = null)
    {
        $options = [];

        if ($after) {
            $options['after'] = $after;
        }

        return $this->discord->getChannel('674721868015468580')->getMessageHistory($options);
    }
}
