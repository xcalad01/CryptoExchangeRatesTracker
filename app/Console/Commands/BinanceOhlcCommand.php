<?php

namespace App\Console\Commands;

use App\Modules\Binance;
use Illuminate\Console\Command;

class BinanceOhlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binance:ohlc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'query binance ohlc data';

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
        $binance = new Binance();
        $binance->run_task();
    }
}
