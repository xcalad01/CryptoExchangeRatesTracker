<?php

namespace App\Modules\AddCrypto;

require __DIR__ . "/Base.php";

use App\Modules\Base;

class AddCrypto extends Base
{
    protected $config = array(
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

    protected $url = "http://127.0.0.1:8000/api/crypto";

    private function send_post(){
        $this->set_curl_post();
        $this->set_curl_url($this->url);

        foreach ($this->config as $item){
            $payload = json_encode(array(
                "Name"=>$item[0],
                "Id"=>$item[1]
            ));
            $this->do_send_post($payload);
        }

        $this->close_curl_conn();
    }

    public function run_task(){
        $this->send_post();
    }

}
