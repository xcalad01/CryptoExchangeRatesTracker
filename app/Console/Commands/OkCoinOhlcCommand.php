<?php

namespace App\Console\Commands;

use App\Modules\OkCoin;
use Illuminate\Console\Command;

class OkCoinOhlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'okcoin:ohlc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'query ohlc data from okcoin exchange';

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
        $okcoin = new OkCoin();
        $okcoin->run_task();
    }
}
