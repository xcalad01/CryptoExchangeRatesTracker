<?php

/**
 *
 * Binance.php
 *
 * Binance API
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */

namespace App\Modules;

/**
 * Class Binance
 * @package App\Modules
 */
class Binance extends Base
{
    /**
     * Config of binance market pairs
     * @var array
     */
    protected $config = array(
        'BTCEUR',
        'TRXBTC',
        'XMRBTC',
        'LTCBTC',
        'EOSBTC',
        'ETHEUR',
        'XRPEUR',
        'ADABTC',
        'XLMBTC',
        'XTZBTC',
        'NEOBTC',
        'DASH-BTC',
        'ETCBTC',
        'ZECBTC',
        'XEMBTC',
        'DOGE-BTC',
        'QTUM-BTC',
        'BTGBTC',
        'ZRXBTC',
        'USDT-RUB'
    );

    private $exchange_id = "binance";

    private $timestamp = null;

    /**
     * Sends get request to binance API for OHLC data
     * @return array
     */
    private function send_get(){
        print_r("Sending get to binance API");

        $results = array();
        $start_end_timestamp = ($this->timestamp - 60) * 1000;

        foreach ($this->config as $item){
            if (strpos($item, '-') !== false) {
                $from = substr($item,0, strpos($item, '-'));
                $to = substr($item,strpos($item, '-') + 1, 3);
                $item = "{$from}{$to}";
                $from = strtolower($from);
                $to = strtolower($to);
            }
            else{
                $from = strtolower(substr($item,0, 3));
                $to = strtolower(substr($item, 3, 3));
            }

            $url = "https://api.binance.com/api/v3/klines?symbol={$item}&interval=1m&startTime={$start_end_timestamp}&endTime={$start_end_timestamp}";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            if ($data){
                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));

                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $this->timestamp - 60,
                    "Historical" => array(
                        null,
                        $data[0][1],
                        $data[0][2],
                        $data[0][3],
                        $data[0][4],
                        $data[0][5]
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
     * Runs get OHLC task on binance API
     */
    public function run_task(){
       print_r("OHLC querying started\n");
        $this->timestamp = strtotime(date('Y-m-d H:i'));
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }
        print_r("OHLC querying ended\n");
    }
}
