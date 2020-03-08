<?php

namespace App\Console\Commands;

use App\Fiat_historical;
use App\Modules\FiatHistorical;
use Illuminate\Console\Command;

class FiatHistoricalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiat:hist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queries for hist fiat rates';

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
        $fiat_hist = new FiatHistorical;
        $fiat_hist->run_task();
    }
}
