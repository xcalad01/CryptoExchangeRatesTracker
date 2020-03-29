<?php


namespace App\Modules;


class BitFinex extends Base
{
    protected $config = array(
        'tBTCUSD',
        'tETHUSD',
        'tXRPUSD',
        'tLTCUSD',
        'tEOSUSD',
        'tBCHUSD',
        'tTRXUSD',
        'tXMRUSD',
        'tXLMUSD',
        'tXTZUSD',
        'tNEOUSD',
        'tETCUSD',
        'tZECUSD',
        'tBTGUSD',
        'tZRXUSD'
    );

    protected $exchange_id = "bitfinex";

    protected $timestamp = null;

    private function send_get(){
        $results = array();
        foreach ($this->config as $item){
            $url = "https://api-pub.bitfinex.com/v2/candles/trade:1m:{$item}/hist?limit=2";
            $this->set_curl_url($url);
            $data = $this->do_send_get();
            if ($data[1][0] / 1000 == $this->timestamp){
                $from = strtolower(substr($item,1, 3));
                $to = strtolower(substr($item, 4, 3));

                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));
                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $this->timestamp,
                    "Historical" => array(
                        null,
                        $data[1][1],
                        $data[1][3],
                        $data[1][4],
                        $data[1][2],
                        $data[1][5]
                    )
                ));
            }
        }


        return $results;
    }

    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto_historical');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            $this->do_send_post($payload);
        }
        $this->close_curl_conn();
    }

    public function run_task(){
        $this->timestamp = strtotime(date('Y-m-d H:i')) - 60;
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }
    }

}
