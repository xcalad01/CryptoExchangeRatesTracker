<?php

namespace App\Modules;
require __DIR__ . "/../Stats.php";

use Stats\Stats;

$configs = include('config.php');
$statsd = new Stats();

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
));

$results = array();
foreach ($configs['exchanges'] as $exchange){
    $url = sprintf("https://api.coingecko.com/api/v3/exchanges/%s", $exchange);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = json_decode(curl_exec($ch), true);

    array_push($results, array(
        "Exchange_id"=>$exchange,
        "Name"=>$data['name'],
        "Image"=>$data['image'],
        'Url'=>$data['url']
    ));

}
curl_close($ch);


$ch = curl_init('http://127.0.0.1:8000/api/exchange');
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
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($httpcode != 200){
        $statsd->statsd->increment("api.error", 1, array('message'=>$result));
    }
}

curl_close($ch);
