<?php

namespace App\Console\Commands;

use App\Modules\CoinbasePro;
use Illuminate\Console\Command;

class CoinBaseProOhlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coinbasepro:ohlc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'query ohlc data from coinbase pro';

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
        $coinbase = new CoinbasePro();
        $coinbase->run_task();
    }
}
