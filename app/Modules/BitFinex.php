<?php


namespace App\Modules;

use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise;

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

    private $from = null;
    private $to = null;

    private $do_break;

    private $client;

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

            print_r("Pair: {$config_item}\n");
            $from = strtolower(substr($config_item,1, 3));
            $to = strtolower(substr($config_item, 4, 3));

            print_r("Requesting from UTC timetamp: {$this->init_db_start_timestamp}\n");
            $url = "https://api-pub.bitfinex.com/v2/candles/trade:1m:t{$config_item}/hist?limit=10000&start={$this->init_db_start_timestamp}&sort=1";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            $last_timestamp = null;

            $this->do_break = false;

            while (true) {
                $running = null;
                $mh = curl_multi_init();
                foreach ($data as $item){
                    if ($item[0] == $this->init_db_stop_timestamp) {
                        $this->do_break = true;
                        break;
                    }
                    $last_timestamp = $item[0];
                    $body = array(
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
                    );
                    $body = json_encode($body);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/crypto_historical');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($body))
                    );
                    curl_multi_add_handle($mh, $ch);
                }

                if ($this->do_break) {
                    break;
                }

                $now = strtotime(date('Y-m-d H:i:s'));
                print_r("Saving to db\nUTC timestamp: {$now}\n");

                do {
                    curl_multi_exec($mh, $running);
                    curl_multi_select($mh);
                } while ($running > 0);



                $now = strtotime(date('Y-m-d H:i:s'));
                print_r("Data saved\nUTC timestamp: {$now}\n");

                curl_multi_close($mh);

                print_r("Requesting from timestamp: {$last_timestamp}\n");
                $url = "https://api-pub.bitfinex.com/v2/candles/trade:1m:tBTCUSD/hist?limit=10000&start={$last_timestamp}&sort=1";
                $this->set_curl_url($url);
                $data = $this->do_send_get();
            }
        }
    }

}
