<?php

namespace App\Console\Commands;

use App\Modules\BitFinex;
use Illuminate\Console\Command;

class BitfinexOhlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitfinex:ohlc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query ohlc data from bitfinex api';

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
        sleep(5);
        $bitfinex = new BitFinex();
        $bitfinex->run_task();
    }
}
