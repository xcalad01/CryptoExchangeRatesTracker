<?php

require __DIR__ . "/AddFiat.php";

use App\Modules\AddFiat\AddFiat;

$add_fiat = new AddFiat();
$add_fiat->run_task();

