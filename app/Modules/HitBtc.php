<?php


namespace App\Modules;


class HitBtc extends Base
{

    private $config = array (
        "BTCUSD",
        "TRXUSD",
        "XMRUSD",
        "BCHUSD",
        "LTCUSD",
        "EOSUSD",
        "ETHUSD",
        "XRPUSDT",
        "ADAUSD",
        "XLMUSD",
        "XTZUSD",
        "NEOUSD",
        "DASHUSD",
        "ETCUSD",
        "ZECUSD",
        "XEMUSD",
        "DOGEUSD",
        "QTUMUSD",
        "BTGUSD",
        "ZRXUSD",
        "USDTUSD"
    );

    private $end_timestamp;
    private $start_timestamp;
    private $exchange_id = "hitbtc";

    private function make_url_symbols(){
        $result = null;
        foreach ($this->config as $item){
            $result .= $item . ",";
        }

        $result = substr($result, 0, -1);
        return $result;
    }

    private function send_get(){
        $results = array();
        $url_symbols = $this->make_url_symbols();
        $url = "https://api.hitbtc.com/api/2/public/candles?period=M1&from={$this->start_timestamp}&till={$this->end_timestamp}&symbols={$url_symbols}";

        $this->set_curl_url($url);
        $data = $this->do_send_get();

        foreach ($this->config as $key){
            $key_data = $data[$key];
            if ($key_data){
                $from = strtolower(substr($key,0, 3));
                $to = strtolower(substr($key, 3, 3));

                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));

                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $this->start_timestamp,
                    "Historical" => array(
                        null,
                        $key_data[0]['open'],
                        $key_data[0]['max'],
                        $key_data[0]['min'],
                        $key_data[0]['close'],
                        $key_data[0]['volume']
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
        $this->start_timestamp = strtotime(date('Y-m-d H:i')) - 60;
        $this->end_timestamp = strtotime(date('Y-m-d H:i'));

        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }
    }
}
