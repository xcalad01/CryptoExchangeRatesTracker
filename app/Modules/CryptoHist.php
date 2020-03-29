<?php


namespace App\Modules;
use Illuminate\Support\Facades\Log;

class CryptoHist extends Base
{
    protected $config = array(
	array('kraken', 'btc', 'usd'),
	array('kraken', 'eth', 'usd'),
    array('kraken', 'ltc', 'usd'),
	array('kraken', 'xrp', 'usd'),
	array('kraken', 'bch', 'usd'),
	array('kraken', 'eos', 'usd'),
	array('kraken', 'trx', 'usd'),
    array('gemini', 'btc', 'usd'),
    array('gdax', 'btc', 'usd'),
    array('bitstamp', 'btc', 'usd'),
    array('cexio/cex', 'btc', 'usd'),
    array('poloniex', 'btc', 'usdt'),
    array('binance', 'btc', 'usdt'),
    array('bittrex', 'btc', 'usdt'),
    array('hitbtc', 'btc', 'usdt'),
    array('bitbay', 'btc', 'usd'),
    array('okcoin', 'btc', 'usd'),
    array('okex', 'btc', 'usdt'),
    );

    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto_historical');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            $this->do_send_post($payload);
        }
        $this->close_curl_conn();
    }

    private function send_get($timestamp){
        $results = array();
        foreach ($this->config as $item){
            $array = $this->check_crypto_id_name($item);
            $exchange_id_crypto_watch = $array[0];
            $exchange_id_db = $array[1];

            $url = sprintf($this->url_base, $exchange_id_crypto_watch, $item[1], $item[2], $timestamp, $timestamp);
            $this->set_curl_url($url);
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

            $this->statsd->statsd->gauge('cryptowatch_remaining', $data['allowance']['remaining'], 1);
	    echo "Added";
	}

        return $results;

    }

    private function run(){
        $timestamp = strtotime(date('Y-m-d H:i'));
        $this->set_url_base("https://api.cryptowat.ch/markets/%s/%s%s/ohlc?periods=60&after=%s&before=%s");
        $exchange_rates_results = $this->send_get($timestamp);
        if (!empty($exchange_rates_results)){
            $this->send_post($exchange_rates_results);
        }
    }

    public function run_task(){
        $this->run();
    }
}
