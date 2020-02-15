<?php

require __DIR__ . "./AddExchange.php";

use App\Modules\AddExchange\AddExchange;

$add_exchange = new AddExchange();
$add_exchange->run_task();
