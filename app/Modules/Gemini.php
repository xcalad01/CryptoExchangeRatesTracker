<?php

/**
 *
 * Gemini.php
 *
 * Gemini API
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */

namespace App\Modules;

/**
 * Class Gemini
 * @package App\Modules
 */
class Gemini extends Base
{
    /**
     * Config of binance market pairs
     * @var array
     */
    protected $config = array(
        'BTCUSD',
        'ETHUSD',
        'ZECUSD',
        'BCHUSD',
        'LTCUSD',

    );

    private $exchange_id = "gemini";

    private function send_get(){
        $results = array();
        foreach ($this->config as $item){
            $url = "https://api.gemini.com/v2/candles/{$item}/1m";
            $this->set_curl_url($url);
            $data = $this->do_send_get();
            $from = strtolower(substr($item,0, 3));
            $to = strtolower(substr($item, 3, 3));
            if ($data){
                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));
                $latest = end($data);
                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $latest[0],
                    "Historical" => array(
                        null,
                        $latest[1],
                        $latest[2],
                        $latest[3],
                        $latest[4],
                        $latest[5]
                    )
                ));
            }
        }
        return $results;
    }

    /**
     * Sends post request to internal API to store the data
     * @param $payload
     */
    private function send_post($payload){
        print_r("Sending API results to DB \n");
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
     * Runs get OHLC task on gemini API
     */
    public function run_task(){
        print_r("OHLC querying started\n");
        $payload = $this->send_get();
        if(!empty($payload)){
            $this->send_post($payload);
        }
        print_r("OHLC querying ended\n");
    }
}
