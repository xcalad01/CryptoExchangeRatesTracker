<?php

namespace App\Console\Commands;

use App\Modules\Kraken;
use Illuminate\Console\Command;

class KrakenOhlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kraken:ohlc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'query ohlc data from kraken exchange';

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
        $kraken = new Kraken();
        $kraken->run_task();
    }
}
