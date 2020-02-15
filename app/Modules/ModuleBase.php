<?php

namespace App\Modules\ModuleBase;

require __DIR__ . "/../Stats.php";

use Stats\Stats;

class ModuleBase
{
    protected $statsd;
    private $ch;
    protected $url;
    protected $url_base;

    public function __construct(){
        $this->statsd = new Stats();
        $this->ch = curl_init();
        $this->url = "";
    }

    protected function set_url_base($url){
        $this->url_base = $url;
    }

    protected function check_crypto_id_name($item){
        if (strpos($item[0], '/') !== false){
            $exchange_id_crypto_watch = substr($item[0],0,strpos($item[0], '/'));
            $exchange_id_db = substr($item[0],strpos($item[0], '/') + 1);
        }
        else{
            $exchange_id_crypto_watch = $item[0];
            $exchange_id_db = $item[0];
        }

        return array($exchange_id_crypto_watch, $exchange_id_db);
    }

    protected function set_curl_url($url){
        curl_setopt($this->ch, CURLOPT_URL, $url);
    }

    protected function set_curl_post(){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
    }

    protected function close_curl_conn(){
        curl_close($this->ch);
    }

    protected function do_send_post($payload){
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
        );
        $result = curl_exec($this->ch);
        $return_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        if($return_code != 200){
            $this->statsd->statsd->increment("api.error", 1, array('message'=>$result));
        }

        return $result;
    }

    protected function do_send_get(){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        return json_decode(curl_exec($this->ch), true);
    }
}
