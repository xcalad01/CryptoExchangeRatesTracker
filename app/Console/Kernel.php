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
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
	    $schedule->command('crypto:hist_five')->cron('*/1 * * * *')->sendOutputTo('/var/log/bakalarka/crypto_hist_five.log');
        $schedule->command('bitfinex:ohlc')->cron('*/1 * * * *')->sendOutputTo('/var/log/bakalarka/bitfinex_ohlc.log');
        $schedule->command('kraken:ohlc')->cron('*/1 * * * *')->sendOutputTo('/var/log/bakalarka/kraken_ohlc.log');
        $schedule->command('coinbasepro:ohlc')->cron('*/1 * * * *')->sendOutputTo('/var/log/bakalarka/coinbasepro.log');
        #$schedule->command('crypto:current')->everyMinute();
        $schedule->command('fiat:current')->dailyAt('16:05')->sendOutputTo('/var/log/bakalarka/fiat_current.log');
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
