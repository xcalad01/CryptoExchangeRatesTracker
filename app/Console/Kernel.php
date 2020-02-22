<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CryptoHistFiveMinCommand::class,
        Commands\CryptoCurrentCommand::class,
        Commands\FiatCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('crypto:hist_five')->everyFiveMinutes()->appendOutputTo('~/tasks_output');
        $schedule->command('crypto:current')->everyMinute()->appendOutputTo('~/tasks_output');
        $schedule->command('fiat:current')->daily()->at('16:05')->appendOutputTo('~/tasks_output');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
