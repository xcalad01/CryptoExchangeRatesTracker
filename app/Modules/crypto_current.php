<?php

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
$url = "https://api.cryptowat.ch/markets/prices";
curl_setopt($ch, CURLOPT_URL, $url);
$data = json_decode(curl_exec($ch), true)['result'];
curl_close($ch);

$ch = curl_init('http://127.0.0.1:8000/api/update_crypto_pair_value');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);

$to_send = array(
    "items"=>array()
);
foreach ($config as $item){
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
        "Value"=>$value
    ));
}

curl_setopt($ch, CURLOPT_POSTFIELDS, $to_send);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    )
);
$result = curl_exec($ch);
curl_close($ch);
