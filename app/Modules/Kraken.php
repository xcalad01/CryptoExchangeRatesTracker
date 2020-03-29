<?php


namespace App\Modules;


class Kraken extends Base
{

    protected $config = array(
        'BTCEUR',
        'ETHUSD',
        'XRPUSD',
        'BCHUSD',
        'LTCUSD',
        'EOSUSD',
        'TRXUSD',
        'XMRUSD',
        'ADAUSD',
        'XLMUSD',
        'XTZUSD',
        'DASHUSD',
        'ETCUSD',
        'ZECUSD',
        'QTUMUSD',
    );

    protected $exchange_id = 'kraken';

    private $timestamp = null;

    private function send_get(){
        $results = array();
        foreach ($this->config as $item){
            $url = "https://api.kraken.com/0/public/OHLC?pair={$item}&interval=1&since={$this->timestamp}";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            if ($data['error']){
                print_r($data['error']);
                return;
            }

            $from = strtolower(substr($item,0, 3));
            $to = strtolower(substr($item, 3, 3));
            $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));
            $data = $data['result'];
            $keys = array_keys($data);
            array_push($results, array(
                "Exchange_id" => $this->exchange_id,
                "From" => $from,
                "To" => $to,
                "Timestamp" => $data[$keys[0]][0][0],
                "Historical" => array(
                    null,
                    $data[$keys[0]][0][1],
                    $data[$keys[0]][0][2],
                    $data[$keys[0]][0][3],
                    $data[$keys[0]][0][4],
                    $data[$keys[0]][0][6]
                )
            ));
        }

        print_r($results);
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
        $this->timestamp = strtotime(date('Y-m-d H:i')) - 61;
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }
    }
}
