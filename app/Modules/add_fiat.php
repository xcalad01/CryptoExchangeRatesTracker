<?php

require __DIR__ . "/../Stats.php";

use Stats\Stats;

# Just USD for now

$config = array(
    array("usd", "American dollar", "1")
);

$statsd = new Stats();

$ch = curl_init('http://127.0.0.1:8000/api/fiat');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
foreach ($config as $item){
    $payload = json_encode(array(
        "Id"=>$item[0],
        "Name"=>$item[1],
        "Value"=>$item[2]
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        )
    );
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($httpcode != 200){
        $statsd->statsd->increment("api.error", 1, array('message'=>$result));
    }
}

curl_close($ch);
