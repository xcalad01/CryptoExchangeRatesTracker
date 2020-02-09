<?php

require './app/Stats.php';

use Stats\Stats;

$statsd = new Stats();

$config = array(
    array('kraken', 'btc', 'usd'),
    array('gemini', 'btc', 'usd'),
    array('gdax', 'btc', 'usd'),
    array('bitstamp', 'btc', 'usd'),
    array('bitfinex', 'btc', 'usd'),
    array('cexio/cex', 'btc', 'usd'),
    array('poloniex', 'btc', 'usdt'),
    array('binance', 'btc', 'usdt'),
    array('bittrex', 'btc', 'usdt'),
    array('hitbtc', 'btc', 'usdt'),
    array('bitbay', 'btc', 'usd'),
    array('okcoin', 'btc', 'usd'),
    array('okex', 'btc', 'usdt'),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
));

$timestamp = strtotime(date('Y-m-d H:i'));

$results = array();
foreach ($config as $item){
    if (strpos($item[0], '/') !== false){
        $exchange_id_crypto_watch = substr($item[0],0,strpos($item[0], '/'));
        $exchange_id_db = substr($item[0],strpos($item[0], '/') + 1);
    }
    else{
        $exchange_id_crypto_watch = $item[0];
        $exchange_id_db = $item[0];
    }
    $url = sprintf("https://api.cryptowat.ch/markets/%s/%s%s/ohlc?periods=300&after=%s&before=%s", $exchange_id_crypto_watch, $item[1], $item[2], $timestamp, $timestamp);
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = json_decode(curl_exec($ch), true);

    if ($data['result']['300']) {
        $statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $exchange_id_crypto_watch, 'from' => $item[1], 'to' => $item[2]));
	    $statsd->statsd->gauge('cryptowatch_remaining', $data['allowance']['remaining'], 1);
	array_push($results, array(
            "Exchange_id" => $exchange_id_db,
            "From" => $item[1],
            "To" => $item[2],
            "Timestamp" => $timestamp,
            "Historical" => $data['result']['300'][0]
        ));
    }
}

print_r($results);
curl_close($ch);


$ch = curl_init('http://127.0.0.1:8000/api/crypto_historical_five');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
foreach ($results as $item){
    $payload = json_encode($item);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
    );
    $result = curl_exec($ch);
}

curl_close($ch);
