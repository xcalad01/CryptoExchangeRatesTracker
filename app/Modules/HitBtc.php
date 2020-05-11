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
        "XRP/USDT",
        "ADAUSD",
        "XLMUSD",
        "XTZUSD",
        "NEOUSD",
        "DASH-USD",
        "ETCUSD",
        "ZECUSD",
        "XEMUSD",
        "DOGE-USD",
        "QTUM-USD",
        "BTGUSD",
        "ZRXUSD",
        "USDT-USD"
    );

    private $end_timestamp;
    private $start_timestamp;
    private $exchange_id = "hitbtc";

    private $init_db_start_timestamp = "1356998400";
    private $init_db_stop_timestamp = "1585990500";



    private function make_url_symbols(){
        $result = null;
        foreach ($this->config as $item){
            if (strpos($item, '-') !== false) {
                $from = substr($item,0, strpos($item, '-'));
                $to = substr($item,strpos($item, '-') + 1, 3);
                $item = "{$from}{$to}";
            }
            else if (strpos($item, '/') !== false) {
                $from = substr($item,0, strpos($item, '/'));
                $to = substr($item,strpos($item, '/') + 1, 4);
                $item = "{$from}{$to}";
            }


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
        print_r($data);
        foreach ($this->config as $key){
            if (strpos($key, '-') !== false) {
                $from = substr($key,0, strpos($key, '-'));
                $to = substr($key,strpos($key, '-') + 1, 3);
                $key = "{$from}{$to}";

            }
            else if (strpos($key, '/') !== false) {
                $from = substr($key,0, strpos($key, '/'));
                $to = substr($key,strpos($key, '/') + 1, 4);
                $key = "{$from}{$to}";
            }
            else{
                $from = strtolower(substr($key,0, 3));
                $to = strtolower(substr($key, 3, 3));
            }
            $from = strtolower($from);
            $to = strtolower($to);
            $key_data = $data[$key];
            if ($key_data){
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
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto/historical');

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
