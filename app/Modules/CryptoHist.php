<?php

/**
 *
 * CryptoHist.php
 *
 * Crypto historical
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */


namespace App\Modules;

/**
 * Class CryptoHist
 * @package App\Modules
 */
class CryptoHist extends Base
{
    /**
     * Config of thress of exchange_ids and market pairs ids
     * @var array
     */
    protected $config = array(
    array('gemini', 'btc', 'usd'),
    array('bitstamp', 'btc', 'usd'),
    array('cexio/cex', 'btc', 'usd'),
    array('poloniex', 'btc', 'usdt'),
    array('bittrex', 'btc', 'usdt'),
    array('bitbay', 'btc', 'usd'),
    array('okex', 'btc', 'usdt'),
    );

    /**
     * Sends post request to internal API to store the data
     * @param $payload
     */
    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto/historical');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            $this->do_send_post($payload);
        }
        $this->close_curl_conn();
    }

    /**
     * Sends get request to Cryptowatch API for OHLC data
     * @param $timestamp
     * @return array
     */
    private function send_get($timestamp){
        $results = array();
        foreach ($this->config as $item){
            $array = $this->check_crypto_id_name($item);
            $exchange_id_crypto_watch = $array[0];
            $exchange_id_db = $array[1];

            print_r("Sending get for: {$exchange_id_db}\n");
            $url = sprintf($this->url_base, $exchange_id_crypto_watch, $item[1], $item[2], $timestamp, $timestamp);
            $this->set_curl_url($url);
            $this->statsd->statsd->increment('cryptowatch_request', 1);
            $data = $this->do_send_get();
            if ($data['result']['60']){
                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $exchange_id_crypto_watch, 'from' => $item[1], 'to' => $item[2]));
                array_push($results, array(
                    "Exchange_id" => $exchange_id_db,
                    "From" => $item[1],
                    "To" => $item[2],
                    "Timestamp" => $timestamp,
                    "Historical" => $data['result']['60'][0]
                ));
            }

            $this->statsd->statsd->increment('cryptowatch_request.finished', 1);
	    }

        print_r("Results: \n");
        print_r($results);
        return $results;

    }

    /**
     * First get data than store data
     */
    private function run(){
        print_r("OHLC data cryptowatch querying\n");
        $timestamp = strtotime(date('Y-m-d H:i'));
        $this->set_url_base("https://api.cryptowat.ch/markets/%s/%s%s/ohlc?periods=60&after=%s&before=%s");
        $exchange_rates_results = $this->send_get($timestamp);
        if (!empty($exchange_rates_results)){
            $this->send_post($exchange_rates_results);
        }

        print_r("Finished successfuly\n");
    }

    /**
     * Run get ohlc data task
     */
    public function run_task(){
        $this->run();
    }
}
