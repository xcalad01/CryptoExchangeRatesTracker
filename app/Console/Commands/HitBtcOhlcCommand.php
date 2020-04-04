<?php

namespace App\Console\Commands;

use App\Modules\HitBtc;
use Illuminate\Console\Command;

class HitBtcOhlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitbtc:ohlc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query ohlc data from hitbtc exchange';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $hitbtc = new HitBtc();
        $hitbtc->run_task();
    }
}
