<?php

require __DIR__ . "./AddExchange.php";

use App\Modules\AddFiat\AddFiat;

$add_fiat = new AddFiat();
$add_fiat->run_task();

