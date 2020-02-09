<?php

$config = array(
    array("Bitcoin","btc"),
    array("Tron","trx"),
    array("Monero","xmr"),
    array("Bitcoin Cash","bch"),
    array("LiteCoin","ltc"),
    array("EOS","eos"),
    array("Ethereum","eth"),
    array("XRP","xrp"),
    array("Cardano","ada"),
    array("Stellar","xlm"),
    array("Tezos","xtz"),
    array("Neo","neo"),
    array("Dash","dash"),
    array("Ethereum Classis","etc"),
    array("ZCash","zec"),
    array("Nem","xem"),
    array("Dogecoin","doge"),
    array("Qtum","qtum"),
    array("Bitcoin Gold","btg"),
    array("0x","zrx"),
    array("Tether", "usdt")
);

$ch = curl_init('http://127.0.0.1:8000/api/crypto');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
foreach ($config as $item){
    $payload = json_encode(array(
        "Name"=>$item[0],
        "Id"=>$item[1]
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
           )
    );
    $result = curl_exec($ch);

}

curl_close($ch);

