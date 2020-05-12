<?php

require __DIR__ . "/../app/Modules/Stats.php";
use App\Modules\Stats;
$statsd = new Stats();

$curl = curl_init();


function query_coin_market_cap(){
    $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
    $parameters = [
        'start' => '1',
        'limit' => '1',
        'convert' => 'USD'
    ];

    $headers = [
        'Accepts: application/json',
        'X-CMC_PRO_API_KEY: 9d9d653a-06d3-45ab-85be-d49c26f3392b'
    ];
    $qs = http_build_query($parameters); // query string encode the parameters
    $request = "{$url}?{$qs}"; // create the request URL


    $curl = curl_init(); // Get cURL resource
    // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $request,            // set the request URL
        CURLOPT_HTTPHEADER => $headers,     // set the headers
        CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
    ));

    $response = curl_exec($curl); // Send the request, save the response
    print_r(json_decode($response, true)['data']); // print json decoded response
    curl_close($curl); // Close request
    return floatval(json_decode($response, true)['data'][0]['quote']['USD']['price']);
}


function query_coinbase(){

}

function query_coin_gecko(){
    $url = 'https://api.coingecko.com/api/v3/exchange_rates';

    $request = "{$url}"; // create the request URL


    $curl = curl_init(); // Get cURL resource
    // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $request,            // set the request URL
        CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
    ));

    $response = curl_exec($curl); // Send the request, save the response
    print_r(json_decode($response, true)); // print json decoded response
    curl_close($curl); // Close request
    return floatval(json_decode($response, true)['rates']['usd']['value']);
}

function query_coin_cap(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "api.coincap.io/v2/rates/bitcoin",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response, "\n";
    return floatval(json_decode($response, true)['data']['rateUsd']);
}

function query_internal_api(){
    $curl = curl_init();

    $start = $timestamp = strtotime(date('Y-m-d'));
    $end = $start + 86400;


    $url = "http://167.86.75.179:8001/api/crypto/historical/asset/value/btc/usd/{$start}/{$end}";
    print_r($url);

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
    echo $response, "\n";
    return floatval(json_decode($response, true)['data'][0][1]);
}


$timestamp = strtotime(date('Y-m-d H:i'));
$coin_market_price = query_coin_market_cap();
$coin_gecko_price = query_coin_gecko();
$coin_cap_price = query_coin_cap();
$internal_price = query_internal_api();


print_r($coin_market_price."\n");
print_r($coin_gecko_price."\n");
print_r($coin_cap_price."\n");
print_r($internal_price."\n");

$percents = 0;
foreach (array($coin_market_price, $coin_gecko_price, $coin_cap_price) as $price){
    $perc = $internal_price * 100 / $price;
    $percents = abs(100 - $perc);
}

print_r(100 - $percents / 3);
print_r("\n");

$statsd->statsd->gauge('avwp_price_check', 100 - $percents / 3, 1);

$file_handle = fopen('/var/log/bakalarka/avwp_price.csv', 'a');
fputcsv($file_handle, array($timestamp, $internal_price, $coin_market_price, $coin_gecko_price, $coin_cap_price, 100 - $percents / 3)); # $line is an array of string values here

fclose($file_handle);
