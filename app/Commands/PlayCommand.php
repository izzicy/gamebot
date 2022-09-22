<?php

namespace App\Commands;

use App\Contracts\Discord\DiscordManager;
use App\Games\Program\ProgramSession;
use App\Games\Reminders\RemindersSession;
use App\Games\Test\TestSession;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class PlayCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'play';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Play the game.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(TestSession::class);
        app(ProgramSession::class);
        app(RemindersSession::class);
        app(DiscordManager::class)->run();
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
