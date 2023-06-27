<?php

namespace App\Commands;

use Discord\Discord;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CommandSetupCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'command-setup {class}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Setup the given command.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        $this->line('Setting up command...');

        $discord->on('ready', function() use ($discord) {
            (new ($this->argument('class')))($discord)->then(function() use ($discord) {
                $discord->close();
                $this->info('Command setup!');
            });
        });

        $discord->run();
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
