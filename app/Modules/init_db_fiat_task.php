<?php

namespace App\Modules;

$curl_foreign = curl_init();
$curl_send_to_db = curl_init();

$timestamp = 1488153284;

$config = array(
    array("usd", "American dollar"),
    array("eur", "Euro"),
    array("jpy","Japanese yen"),
    array("bgn","Bulgarian lev"),
    array("czk","Czech koruna "),
    array("dkk","Danish krone  "),
    array("gbp","Pound sterling"),
    array("huf","Hungarian forint"),
    array("pln","Polish zloty"),
    array("ron","Romanian leu"),
    array("sek","Swedish krona"),
    array("chf","Swiss franc"),
    array("isk","Icelandic krona"),
    array("nok","Norwegian krone"),
    array("hrk","Croatian kuna"),
    array("rub","Russian rouble"),
    array("try","Turkish lira"),
    array("aud","Australian dollar"),
    array("brl","Brazilian real"),
    array("cad","Canadian dollar"),
    array("cny","Chinese yuan renminbi"),
    array("hkd","Hong Kong dollar"),
    array("idr","Indonesian rupiah"),
    array("ils","Israeli shekel"),
    array("inr","Indian rupee"),
    array("krw","South Korean won"),
    array("mxn","Mexican peso"),
    array("myr","Malaysian ringgit"),
    array("nzd","New Zealand dollar"),
    array("php","Philippine peso"),
    array("sgd","Singapore dollar"),
    array("thb","Thai baht"),
    array("zar","South African ran"),
);

print_r("starting\n");
$api_url = "http://127.0.0.1:8000/api/fiat";
curl_setopt_array($curl_foreign, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
));

try {
    while ($timestamp < 1588550400){
        $date = date("Y-m-d", $timestamp);
        print_r($date."\n");
        $url = "https://api.exchangeratesapi.io/{$date}?base=USD";

        curl_setopt_array($curl_foreign, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $data = json_decode(curl_exec($curl_foreign), true);

        foreach ($data['rates'] as $key => $rates){
            foreach ($config as $item){
                try {
                    $payload = json_encode(array(
                        "Id"=>$item[0],
                        "Name"=>$item[1],
                        "Value"=>$rates[strtoupper($item[0])],
                        "Key"=>$key
                    ));
                    $this->fire_and_forget_post($socket, "http://127.0.0.1:8000/api/fiat", $payload);
                }
                catch (ErrorException $e){
                    continue;
                }
            }
        }

        $timestamp += 86400;
    }
}
catch (\Exception $e){
    print_r($e->getMessage());
}


print_r("ending\n");
