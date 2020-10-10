<?php

namespace App\Console\Commands;

use App\Modules\Gemini;
use Illuminate\Console\Command;

class GeminiOhlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gemini:ohlc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'query ohlc data from gemini exchange';

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
        $gemini = new Gemini();
        $gemini->run_task();
    }
}
