<?php
$curl_coinbase = curl_init();
$curl_internal = curl_init();

function query_coinbase($date){
    # https://api.coinbase.com/v2/prices/BTC-USD/spot
    $curl = curl_init();
    print_r($date."\n");
    print_r(strtotime($date));
    $timestamp = strtotime($date);


    $start = $timestamp;
    $end = $start + 86400;


    $url = "https://api.coinbase.com/v2/prices/BTC-USD/spot?date={$date}";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return json_decode($response, true)['data']['amount'];
}

function query_internal($date){
    $curl = curl_init();
    print_r($date."\n");
    print_r(strtotime($date));
    $timestamp = strtotime($date);


    $start = $timestamp;
    $end = $start + 86400;


    $url = "http://167.86.75.179:8001/api/crypto/historical/asset/value/btc/usd/{$start}/{$end}";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return json_decode($response, true)['data'];
}

$dates = array(
    "2014-09-24",
    "2015-03-02",
    "2016-03-02",
    "2017-12-11",
    "2018-10-14",
    "2018-12-11",
    "2019-10-26",
    "2020-01-17",
    "2020-04-23",
    "2020-04-05",
    "2014-09-07",
    "2014-10-10",
    "2015-01-19",
    "2015-01-27",
    "2015-04-09",
    "2015-05-17",
    "2015-08-04",
    "2015-10-10",
    "2016-01-14",
    "2016-07-19",
    "2016-09-02",
    "2017-01-20",
    "2017-10-11",
    "2017-12-21",
    "2018-02-08",
    "2019-05-09",
    "2019-09-12",
    "2019-12-31",
    "2020-01-13",
    "2020-01-21",
);


### Coingecko API request ###
$url = 'https://api.coingecko.com/api/v3/coins/bitcoin/market_chart?vs_currency=usd&days=max';

$request = "{$url}"; // create the request URL


$curl = curl_init(); // Get cURL resource
// Set cURL options
curl_setopt_array($curl, array(
    CURLOPT_URL => $request,            // set the request URL
    CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
));

$response = curl_exec($curl); // Send the request, save the response
curl_close($curl); // Close request
$response = json_decode($response, true)['prices'];

$coin_gecko_prices = array();
foreach ($response as $cgp){
    $coin_gecko_prices[$cgp[0] / 1000] = $cgp[1];
}

$file_handle = fopen('tests/Table 6.1.csv', 'a');
$counter = 0;
foreach ($dates as $date){
    $timestamp = strtotime($date);
    $internal_price = query_internal($date)[0][1];
    $coin_base = query_coinbase($date);
    $coin_gecko_price = $coin_gecko_prices[$timestamp];

    if ($internal_price){
        if($coin_base){
            $coin_base_diff = abs($internal_price - $coin_base) / ($internal_price + $coin_base) * 100;
        }
        else{
            continue;
        }

        if ($coin_gecko_price){
            $coin_gecko_diff = abs($internal_price - $coin_gecko_price) / ($internal_price + $coin_gecko_price) * 100;
        }
        else{
            continue;
        }
    }
    else{
        continue;
    }

    $counter += 2;
    fputcsv($file_handle, array($date, $internal_price, $coin_gecko_price, $coin_gecko_diff, $coin_base, $coin_base_diff)); # $line is an array of string values here
    print_r(array($date, $internal_price, $coin_gecko_price, $coin_base));
}
