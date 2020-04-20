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

    private $init_db_start_timestamp = "1356998400";
    private $init_db_stop_timestamp = "1585990500";



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

    public function run_init_db_task(){
        $endpointParts = parse_url("http://127.0.0.1:8000/api/crypto/historical");
        $socket = fsockopen($endpointParts['host'], $endpointParts['port']);

        foreach ($this->config as $config_item){
            print_r("Pair: {$config_item}\n");

            $from = strtolower(substr($config_item,0, 3));
            $to = strtolower(substr($config_item, 3, 3));

            print_r("Requesting from UTC timetamp: {$this->init_db_start_timestamp}\n");
            $url = "https://api.hitbtc.com/api/2/public/candles?period=M15&from={$this->init_db_start_timestamp}&symbols={$config_item}";
            $this->set_curl_url($url);
            $data = $this->do_send_get();
            $key_data = $data[$config_item];

            $last_timestamp = null;

            $do_break = false;

            while (true) {
                foreach ($key_data as $item){
                    if (strtotime($item['timestamp']) >= $this->init_db_stop_timestamp) {
                        $do_break = true;
                        break;
                    }

                    $last_timestamp = $item['timestamp'];
                    $body = array(
                        "Exchange_id" => $this->exchange_id,
                        "From" => $from,
                        "To" => $to,
                        "Timestamp" => strtotime($item['timestamp']),
                        "Historical" => array(
                            null,
                            $item['open'],
                            $item['max'],
                            $item['min'],
                            $item['close'],
                            $item['volume']
                        )
                    );
                    $body = json_encode($body);
                    $this->fire_and_forget_post($socket,"http://127.0.0.1:8000/api/crypto/historical", $body);
                }

                if ($do_break) {
                    break;
                }

                print_r("Requesting from timestamp: {$last_timestamp}\n");
                $url = "https://api.hitbtc.com/api/2/public/candles?period=M15&from={$last_timestamp}&symbols={$config_item}";
                $this->set_curl_url($url);
                $data = $this->do_send_get();
                $key_data = $data[$config_item];
            }

            break; // BTC - EUR for now
        }
    }
}
