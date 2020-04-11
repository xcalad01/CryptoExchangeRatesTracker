<?php

namespace App\Console\Commands;

use App\Modules\AddFiat;
use Illuminate\Console\Command;

class FiatHistoricalInitDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiat:init_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $fiat_hist = new AddFiat();
        $fiat_hist->run_init_db_task();
    }
}
