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

    private $exchange_id = "bitfinex";

    private $timestamp = null;

    private $init_db_start_timestamp = "1325376000000";
    private $init_db_stop_timestamp = "1585699200000";

    private function send_get(){
        $results = array();
        foreach ($this->config as $item){
            $url = "https://api-pub.bitfinex.com/v2/candles/trade:1m:{$item}/hist?limit=2";
            $this->set_curl_url($url);
            $data = $this->do_send_get();
            if ($data[1][0] and $data[1][0] / 1000 == $this->timestamp){
                $from = strtolower(substr($item,1, 3));
                $to = strtolower(substr($item, 4, 3));

                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));
                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $data[1][0] / 1000,
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
        print_r($this->timestamp);
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }
    }

    public function run_init_db_task(){
        foreach ($this->config as $config_item) {
            $from = strtolower(substr($config_item,1, 3));
            $to = strtolower(substr($config_item, 4, 3));

            $url = "https://api-pub.bitfinex.com/v2/candles/trade:1m:t{$config_item}/hist?limit=10000&start={$this->init_db_start_timestamp}&sort=1";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            $last_timestamp = null;

            $do_break = false;

            while (true) {
                $results = array();
                foreach ($data as $item) {
                    if ($item[0] == $this->init_db_stop_timestamp) {
                        $do_break = true;
                        break;
                    }
                    array_push($results, array(
                        "Exchange_id" => $this->exchange_id,
                        "From" => $from,
                        "To" => $to,
                        "Timestamp" => $item[0] / 1000,
                        "Historical" => array(
                            null,
                            $item[1],
                            $item[3],
                            $item[4],
                            $item[2],
                            $item[5]
                        )
                    ));

                    $last_timestamp = $item[0];
                }

                if ($do_break) {
                    break;
                }

                if (!empty($results)){
                    $this->send_post($results);
                }

                print_r("Querying from new timestamp\n");
                $url = "https://api-pub.bitfinex.com/v2/candles/trade:1m:tBTCUSD/hist?limit=10000&start={$last_timestamp}&sort=1";
                $this->set_curl_url($url);
                $data = $this->do_send_get();
            }
        }
    }

}
