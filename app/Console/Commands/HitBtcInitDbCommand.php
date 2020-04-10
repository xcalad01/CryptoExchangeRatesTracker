<?php

namespace App\Console\Commands;

use App\Modules\HitBtc;
use Illuminate\Console\Command;

class HitBtcInitDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitbtc:init_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'query for historical data from latest historical day available to actual day';

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
        $hitbtc->run_init_db_task();
    }
}
