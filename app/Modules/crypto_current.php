<?php

require './app/Stats.php';

use Stats\Stats;

$statsd = new Stats();

$config_crypto_crypto = array(
    array('poloniex', 'btc', 'usdt'),
    array('binance', 'btc', 'usdt'),
    array('bittrex', 'btc', 'usdt'),
    array('hitbtc', 'btc', 'usdt'),
    array('okex', 'btc', 'usdt'),
);

$config_crypto_fiat = array(
    array('kraken', 'btc', 'usd'),
    array('gemini', 'btc', 'usd'),
    array('coinbase-pro/gdax', 'btc', 'usd'),
    array('bitstamp', 'btc', 'usd'),
    array('bitfinex', 'btc', 'usd'),
    array('cexio/cex', 'btc', 'usd'),
    array('bitbay', 'btc', 'usd'),
    array('okcoin', 'btc', 'usd'),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
));
$url = "https://api.cryptowat.ch/markets/prices";
curl_setopt($ch, CURLOPT_URL, $url);
$data = json_decode(curl_exec($ch), true);
$allowance = $data['allowance']['remaining'];
$statsd->statsd->gauge('cryptowatch_remaining', $allowance, 1);
$data = $data['result'];
curl_close($ch);

$ch = curl_init('http://127.0.0.1:8000/api/update_crypto_pair_value?fiat=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);

$to_send = array(
    "items"=>array()
);
foreach ($config_crypto_crypto as $item){
    if (strpos($item[0], '/') !== false){
        $exchange_id_crypto_watch = substr($item[0],0,strpos($item[0], '/'));
        $exchange_id_db = substr($item[0],strpos($item[0], '/') + 1);
    }
    else{
        $exchange_id_crypto_watch = $item[0];
        $exchange_id_db = $item[0];
    }
    $key = sprintf("market:%s:%s%s", $exchange_id_crypto_watch, $item[1], $item[2]);
    $value = $data[$key];
    array_push($to_send["items"], array(
        "Exchange"=>$exchange_id_db,
        "From"=>$item[1],
        "To"=>$item[2],
        "Value"=>$value,
        "Type"=>"crypto"
    ));
}

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($to_send));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    )
);
$result = curl_exec($ch);
echo $result;
curl_close($ch);


$ch = curl_init('http://127.0.0.1:8000/api/update_crypto_pair_value');
$to_send = array(
    "items"=>array()
);
foreach ($config_crypto_fiat as $item){
    if (strpos($item[0], '/') !== false){
        $exchange_id_crypto_watch = substr($item[0],0,strpos($item[0], '/'));
        $exchange_id_db = substr($item[0],strpos($item[0], '/') + 1);
    }
    else{
        $exchange_id_crypto_watch = $item[0];
        $exchange_id_db = $item[0];
    }
    $key = sprintf("market:%s:%s%s", $exchange_id_crypto_watch, $item[1], $item[2]);
    $value = $data[$key];
    array_push($to_send["items"], array(
        "Exchange"=>$exchange_id_db,
        "From"=>$item[1],
        "To"=>$item[2],
        "Value"=>$value,
        "Type"=>"fiat"
    ));
}

print_r($to_send);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($to_send));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    )
);

$result = curl_exec($ch);
echo $result;
curl_close($ch);
