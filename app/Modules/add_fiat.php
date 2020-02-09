<?php


# Just USD for now

$config = array(
    array("usd", "American dollar", "1")
);

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

}

curl_close($ch);
