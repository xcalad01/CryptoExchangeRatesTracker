<?php

namespace App\Console\Commands;

use App\Modules\AddFiat;
use Illuminate\Console\Command;

class FiatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiat:current';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query for fiat exchange rates and if some exists in DB they are coverted as historical for specific time period';

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
        $add_fiat = new AddFiat();
        $add_fiat->run_task();
    }
}
