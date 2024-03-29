<?php

namespace App\Routines;

use App\Contracts\Routines\Routine;
use App\Contracts\Routines\WantsRepository;
use App\Games\CatClicker\CatClickerRoutine;
use App\Games\ChooseYourDoor\ChooseYourDoorRoutine;
use App\Games\Diagnostics\DiagnosticsRoutine;
use App\Games\Help\HelpRoutine;
use App\Games\Reminders\ReminderRoutine;
use App\Games\RockPaperScissors\RockPaperScissorsRoutine;
use App\Games\Snowflake\SnowflakeRoutine;
use App\Games\ZeroDollarGame\ZeroDollarGameRoutine;
use App\Games\WhoAmI\WhoAmIRoutine;
use App\Routines\Concerns\HasId;
use App\Routines\Concerns\WantsRepositoryConcern;

class MainRoutine implements Routine, WantsRepository
{
    use HasId, WantsRepositoryConcern;

    /**
     * All main routines.
     *
     * @var Routine[]
     */
    protected $routines = [];

    /**
     * @inheritdoc
     */
    public function tags()
    {
        return ['main'];
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $this->routines = [];
        $this->startRoutine(ReminderRoutine::class);
        $this->startRoutine(WhoAmIRoutine::class);
        $this->startRoutine(ZeroDollarGameRoutine::class);
        $this->startRoutine(DiagnosticsRoutine::class);
        $this->startRoutine(RockPaperScissorsRoutine::class);
        $this->startRoutine(ChooseYourDoorRoutine::class);
        $this->startRoutine(SnowflakeRoutine::class);
        $this->startRoutine(CatClickerRoutine::class);
        $this->startRoutine(HelpRoutine::class);
        // $this->startRoutine(ResolveRoutine::class);
    }

    /**
     * @inheritdoc
     */
    public function destroy()
    {
        foreach ($this->routines as $routine) {
            $routine->destroy();
        }
    }

    /**
     * Start the given routine.
     *
     * @param string $class
     * @param array $parameters
     * @return void
     */
    protected function startRoutine($class, $parameters = [])
    {
        $routine = $this->repository->create($class, $parameters);

        $this->routines[] = $routine;

        $routine->initialize();
    }
}
