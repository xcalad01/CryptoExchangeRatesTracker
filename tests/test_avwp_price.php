<?php
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

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
    return json_decode($response, true)['data'][0]['quote']['USD']['price'];
}


function query_coinbase(){

}

function query_coin_gecko(){
    $client = new CoinGeckoClient();
    $data = $client->simple()->getPrice('bitcoin', 'usd');
    print_r($data);
    return $data;
}

function query_coin_cap(){

}

function query_internal_api(){

}


$timestamp = strtotime(date('Y-m-d H:i'));
//$coin_market_price = query_coin_market_cap();
$coin_gecko_data = query_coin_gecko();
//print_r($coin_market_price);
