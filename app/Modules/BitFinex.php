<?php

/**
 *
 * Bitfinex.php
 *
 * Bitfinex API
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */


namespace App\Modules;

use DemeterChain\C;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;

/**
 * Class BitFinex
 * @package App\Modules
 */
class BitFinex extends Base
{
    /**
     * Config of bitfinex custom market pairs ids
     * @var array
     */
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

    /**
     * Sends get request to bitfinex API to get the OHLC data
     * @return array
     */
    private function send_get(){
        print_r("Sending get to bitfinex API");

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

        print_r("API resutls: \n");
        print_r($results);

        return $results;
    }

    /**
     * Sends post request to internal API to store the data
     * @param $payload
     */
    private function send_post($payload){
        print_r("Sending API results to DB\n");
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto/historical');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            print_r($this->do_send_post($payload)."\n");
        }
        $this->close_curl_conn();

        print_r("All sended");
    }

    /**
     * Runs get OHLC data task
     */
    public function run_task(){
        print_r("OHLC querying started\n");
        $this->timestamp = strtotime(date('Y-m-d H:i')) - 60;
        print_r($this->timestamp);
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }
        print_r("OHLC querying ended\n");
    }
}
