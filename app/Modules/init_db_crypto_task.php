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

/**
 * Start timestamp
 */
$timestamp = getenv('INIT_CRYPTO_START');

$end_timestamp = getenv('INIT_CRYPTO_END');

$api_host = getenv('API_HOST');
$api_port = getenv('API_PORT');

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

/**
 * gradually inits asset data for exchanges where it is suppported and given timestamp
 */
try {
    while ($timestamp < $end_timestamp){
        print_r(date("%Y-%m-%d\n", $timestamp));
        $start = $timestamp;
        $end = $start + 86399;
        foreach ($coins as $coin){
            $url = "http://{$api_host}:{$api_port}/api/crypto/historical/asset/value/{$coin}/usd/{$start}/{$end}/1m/dry";
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

            print_r(curl_exec($curl));


        }
        $timestamp += 86400;
    }
}
catch (\Exception $e){
    print_r($e->getMessage());
}


print_r("ending\n");
