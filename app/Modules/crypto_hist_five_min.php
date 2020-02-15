<?php
require __DIR__ . "/CryptoHistFive.php";

use App\Modules\CryptoHistFive\CryptoHistFive;

$crypto_hist_five = new CryptoHistFive();
$crypto_hist_five->run_task();
