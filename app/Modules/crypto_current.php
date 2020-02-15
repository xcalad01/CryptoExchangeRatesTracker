<?php

require __DIR__ . "/CryptoCurrent.php";

use App\Modules\CryptoCurrent\CryptoCurrent;

$crypto_current = new CryptoCurrent();
$crypto_current->run_task();
