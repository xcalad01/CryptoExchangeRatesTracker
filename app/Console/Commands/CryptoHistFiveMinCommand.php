<?php

namespace App\Console\Commands;

require __DIR__ . "/../../Modules/CryptoHistFive.php";

use Illuminate\Console\Command;
use App\Modules\CryptoHistFive\CryptoHistFive;

class CryptoHistFiveMinCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:hist_five';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query new 5 min. historical data in current time interval';

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
        $crypto_hist_five = new CryptoHistFive();
        $crypto_hist_five->run_task();
        echo "HistFIveMin";
    }
}
