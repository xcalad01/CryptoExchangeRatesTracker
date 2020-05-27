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

require __DIR__ . "/Stats.php";

$config = array(
    "binance" => array(
        'BTCEUR',
        'TRXBTC',
        'XMRBTC',
        'LTCBTC',
        'EOSBTC',
        'ETHEUR',
        'XRPEUR',
        'ADABTC',
        'XLMBTC',
        'XTZBTC',
        'NEOBTC',
        'DASHBTC',
        'ETCBTC',
        'ZECBTC',
        'XEMBTC',
        'DOGEBTC',
        'QTUMBTC',
        'BTGBTC',
        'ZRXBTC',
        'USDTRUB'
    ),
    "gdax" => array(
        'BTCUSD',
        'ETHUSD',
        'BCHUSD',
        'LTCUSD',
        'EOSUSD',
        'XRPUSD',
        'XLMUSD',
        'XTZUSD',
        'ETCUSD',
        'ZECUSDC',
        'ZRXUSD',
    ),

    "kraken" => array(
        'BTCEUR',
        'ETHUSD',
        'XRPUSD',
        'BCHUSD',
        'LTCUSD',
        'EOSUSD',
        'TRXUSD',
        'XMRUSD',
        'ADAUSD',
        'XLMUSD',
        'XTZUSD',
        'DASHUSD',
        'ETCUSD',
        'ZECUSD',
        'QTUMUSD',
    ),

    "bitfinex" => array(
        'BTCUSD',
        'ETHUSD',
        'XRPUSD',
        'LTCUSD',
        'EOSUSD',
        'BCHUSD',
        'TRXUSD',
        'XMRUSD',
        'XLMUSD',
        'XTZUSD',
        'NEOUSD',
        'ETCUSD',
        'ZECUSD',
        'BTGUSD',
        'ZRXUSD'
    ),

    "hitbtc" => array(
        "BTCUSD",
        "TRXUSD",
        "XMRUSD",
        "BCHUSD",
        "LTCUSD",
        "EOSUSD",
        "ETHUSD",
        "XRPUSDT",
        "ADAUSD",
        "XLMUSD",
        "XTZUSD",
        "NEOUSD",
        "DASHUSD",
        "ETCUSD",
        "ZECUSD",
        "XEMUSD",
        "DOGEUSD",
        "QTUMUSD",
        "BTGUSD",
        "ZRXUSD",
        "USDTUSD"
    ),

    "bitstamp" => array(
        "BTCUSD",
    ),

    "gemini" => array(
        "BTCUSD",
    ),
    "okcoin" => array(
        "BTCUSD",
        "TRXUSD",
        "BCHUSD",
        "LTCUSD",
        "EOSUSD",
        "ETHUSD",
        "XRPUSD",
        "ETCUSD",
    )
);

foreach ($config as $exchange => $coins){
    foreach ($coins as $coin){
        $coin_from = strtolower(substr($coin,0, 3));
        $coin_to = strtolower(substr($coin,3, 3));
        $url = "http://167.86.75.179:8001/api/crypto_historical/{$argv[1]}/1585785600/1587234900/{$exchange}/1h/{$coin_from}/{$coin_to}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $start = round(microtime(true) * 1000);
        print_r($start . "\n");
        json_decode(curl_exec($ch), true);
        print_r(round(microtime(true) * 1000) - $start);
        print_r("\n");
    }
}
