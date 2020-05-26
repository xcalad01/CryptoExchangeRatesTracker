<?php

/**
 *
 * init_db_crypto_task.php
 *
 * Init db with crypto data
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */

namespace App\Modules;


$curl = curl_init();
$timestamp = 1488153284;

$coins = array(
    "btc",
    "trx",
    "xmr",
    "bch",
    "ltc",
    "eos",
    "eth",
    "xrp",
    "ada",
    "xlm",
    "xtz",
    "neo",
    "dash",
    "etc",
    "zec",
    "xem",
    "doge",
    "qtum",
    "btg",
    "zrx",
    "usdt",
);

print_r("starting\n");

try {
    while ($timestamp < 1588550400){
        print_r(date("%Y-%m-%d\n", $timestamp));
        $start = $timestamp;
        $end = $start + 86399;
        foreach ($coins as $coin){
            $url = "http://167.86.75.179:8001/api/crypto/historical/asset/value/{$coin}/usd/{$start}/{$end}/range/dry";
            print_r($url."\n");
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));

            curl_exec($curl);


        }
        $timestamp += 86400;
    }
}
catch (\Exception $e){
    print_r($e->getMessage());
}


print_r("ending\n");
