<?php
//
//
//namespace App\Modules;
//
//
//class FiatHistorical extends Base
//{
//    protected $config = array(
//        array("usd", "American dollar"),
//        array("eur", "Euro"),
//        array("jpy","Japanese yen"),
//        array("bgn","Bulgarian lev"),
//        array("czk","Czech koruna "),
//        array("dkk","Danish krone  "),
//        array("gbp","Pound sterling"),
//        array("huf","Hungarian forint"),
//        array("pln","Polish zloty"),
//        array("ron","Romanian leu"),
//        array("sek","Swedish krona"),
//        array("chf","Swiss franc"),
//        array("isk","Icelandic krona"),
//        array("nok","Norwegian krone"),
//        array("hrk","Croatian kuna"),
//        array("rub","Russian rouble"),
//        array("try","Turkish lira"),
//        array("aud","Australian dollar"),
//        array("brl","Brazilian real"),
//        array("cad","Canadian dollar"),
//        array("cny","Chinese yuan renminbi"),
//        array("hkd","Hong Kong dollar"),
//        array("idr","Indonesian rupiah"),
//        array("ils","Israeli shekel"),
//        array("inr","Indian rupee"),
//        array("krw","South Korean won"),
//        array("mxn","Mexican peso"),
//        array("myr","Malaysian ringgit"),
//        array("nzd","New Zealand dollar"),
//        array("php","Philippine peso"),
//        array("sgd","Singapore dollar"),
//        array("thb","Thai baht"),
//        array("zar","South African ran"),
//    );
//
//    protected $url = 'http://127.0.0.1:8000/api/fiat';
//
//    private function send_get(){
//        $this->set_curl_url("https://api.exchangeratesapi.io/history?start_at=2020-03-24&end_at=2020-03-25&base=USD");
//        return $this->do_send_get();
//    }
//
//    private function send_post($key, $rates){
//        $this->set_curl_post();
//        $this->set_curl_url($this->url);
//
//        foreach ($this->config as $item){
//            $payload = json_encode(array(
//                "Id"=>$item[0],
//                "Name"=>$item[1],
//                "Value"=>$rates[strtoupper($item[0])],
//                "Key"=>$key
//            ));
//            $this->do_send_post($payload);
//        }
//    }
//
//    public function run_task(){
//        $data = $this->send_get();
//        if ($data){
//            foreach ($data["rates"] as $key => $rate){
//		    $this->send_post($key, $rate);
//		    echo $key, "\n";
//            }
//            $this->close_curl_conn();
//
//        }
//    }
//}
