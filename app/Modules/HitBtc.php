<?php

/**
 *
 * HitBtc.php
 *
 * HitBtc Api
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */

namespace App\Modules;

/**
 * Class HitBtc
 * @package App\Modules
 */
class HitBtc extends Base
{
    /**
     * Config of custom hitbtc market pairs
     * @var array
     */
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

    /**
     * Creates string of url symbols divided by ','
     * @return false|string|null
     */
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

    /**
     * Sends get request to HitBtc API for OHLC data
     * @return array
     */
    private function send_get(){
        print_r("Sending get to hitbtc API\n");

        $results = array();
        $url_symbols = $this->make_url_symbols();
        $url = "https://api.hitbtc.com/api/2/public/candles?period=M1&from={$this->start_timestamp}&till={$this->end_timestamp}&symbols={$url_symbols}";
        $this->set_curl_url($url);
        $data = $this->do_send_get();
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

        print_r("API resutls: \n");
        print_r($results);

        return $results;
    }

    /**
     * Sends post request to internal API to store the data
     * @param $payload
     */
    private function send_post($payload){
        print_r("Sending API results to DB");

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
     * Run get ohlc data task
     */
    public function run_task(){
        print_r("OHLC querying started\n");

        $this->start_timestamp = strtotime(date('Y-m-d H:i')) - 60;
        $this->end_timestamp = strtotime(date('Y-m-d H:i'));

        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }

        print_r("OHLC querying ended\n");
    }
}
