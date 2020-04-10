<?php


namespace App\Modules;

use DemeterChain\C;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;

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
        $endpointParts = parse_url("http://127.0.0.1:8000/api/crypto_historical");
        $socket = fsockopen($endpointParts['host'], $endpointParts['port']);

        foreach ($this->config as $config_item) {
            print_r("Pair: {$config_item}\n");
            $from = strtolower(substr($config_item,1, 3));
            $to = strtolower(substr($config_item, 4, 3));

            print_r("Requesting from UTC timetamp: {$this->init_db_start_timestamp}\n");
            $url = "https://api-pub.bitfinex.com/v2/candles/trade:15m:t{$config_item}/hist?limit=10000&start={$this->init_db_start_timestamp}&sort=1";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            $last_timestamp = null;

            $this->do_break = false;

            while (true) {
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
                    $this->fire_and_forget_post($socket,"http://127.0.0.1:8000/api/crypto_historical", $body);
                }

                if ($this->do_break) {
                    break;
                }

                print_r("Requesting from timestamp: {$last_timestamp}\n");
                $url = "https://api-pub.bitfinex.com/v2/candles/trade:15m:tBTCUSD/hist?limit=10000&start={$last_timestamp}&sort=1";
                $this->set_curl_url($url);
                $data = $this->do_send_get();
            }

            break; // BTC - USD for now
        }
        fclose($socket);
    }

}
