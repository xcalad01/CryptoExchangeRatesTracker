<?php

namespace App\Console\Commands;

use App\Modules\AddCrypto;
use Illuminate\Console\Command;

class AddCryptoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds cryptocurrency representations to DB';

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
        $add_crypto = new AddCrypto();
        $add_crypto->run_task();
    }
}
