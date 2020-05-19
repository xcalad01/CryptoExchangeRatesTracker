<?php

$timestamp = 1483228800;
$crypto = array("btc", "eth", "bch", "ltc")
$currencies = array("usd", "eur", "jpy", "czk", "huf", "gbp");

while ($timestamp < 1577836800) # 3 years
{
    try {
        $curl = curl_init();


        $start = $timestamp;
        $end = $start + 86400;
        $timestamp = $end;
        $from = array_rand($crypto, 1);
        $to = array_rand($currencies, 1);

        $url = "http://167.86.75.179:8001/api/crypto/historical/asset/value/{$from}/{$to}/{$start}/{$end}";

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

        $data = json_decode($response, true)['data'];
        print_r($data);
    }
    catch (\Exception $e){
        print_r($e->getMessage());
    }
}
