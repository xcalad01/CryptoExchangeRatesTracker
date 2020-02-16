<?php

require __DIR__ . "/AddCrypto.php";

use App\Modules\AddCrypto\AddCrypto;

$add_crypto = new AddCrypto();
$add_crypto->run_task();


