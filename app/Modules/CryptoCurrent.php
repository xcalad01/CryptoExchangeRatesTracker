<?php


namespace App\Modules\CryptoCurrent;

require __DIR__ . "/ModuleBase.php";

use App\Modules\ModuleBase\ModuleBase;

class CryptoCurrent extends ModuleBase
{
    protected $config_crypto_crypto = array(
        array('poloniex', 'btc', 'usdt'),
        array('binance', 'btc', 'usdt'),
        array('bittrex', 'btc', 'usdt'),
        array('hitbtc', 'btc', 'usdt'),
        array('okex', 'btc', 'usdt'),
    );

    protected $config_crypto_fiat = array(
        array('kraken', 'btc', 'usd'),
        array('gemini', 'btc', 'usd'),
        array('coinbase-pro/gdax', 'btc', 'usd'),
        array('bitstamp', 'btc', 'usd'),
        array('bitfinex', 'btc', 'usd'),
        array('cexio/cex', 'btc', 'usd'),
        array('bitbay', 'btc', 'usd'),
        array('okcoin', 'btc', 'usd'),
    );

    private function send_crypto_crypto($rates_result){
        $to_send = array(
            "items"=>array()
        );

        foreach ($this->config_crypto_crypto as $item){
            $array = $this->check_crypto_id_name($item);
            $exchange_id_crypto_watch = $array[0];
            $exchange_id_db = $array[1];

            $key = sprintf("market:%s:%s%s", $exchange_id_crypto_watch, $item[1], $item[2]);
            $value = $rates_result[$key];
            array_push($to_send["items"], array(
                "Exchange"=>$exchange_id_db,
                "From"=>$item[1],
                "To"=>$item[2],
                "Value"=>$value,
                "Type"=>"crypto"
            ));
        }

        $payload = json_encode($to_send);
        $this->do_send_post($payload);
    }

    private function send_crypto_fiat($rates_result){
        $to_send = array(
            "items"=>array()
        );

        foreach ($this->config_crypto_fiat as $item){
            $array = $this->check_crypto_id_name($item);
            $exchange_id_crypto_watch = $array[0];
            $exchange_id_db = $array[1];

            $key = sprintf("market:%s:%s%s", $exchange_id_crypto_watch, $item[1], $item[2]);
            $value = $rates_result[$key];
            array_push($to_send["items"], array(
                "Exchange"=>$exchange_id_db,
                "From"=>$item[1],
                "To"=>$item[2],
                "Value"=>$value,
                "Type"=>"fiat"
            ));
        }

        $payload = json_encode($to_send);
        $this->do_send_post($payload);

    }

    private function send_post($rates_result){
        $this->set_curl_post();

        $this->set_curl_url("http://127.0.0.1:8000/api/update_crypto_pair_value");
        $this->send_crypto_crypto($rates_result);

        $this->set_curl_url("http://127.0.0.1:8000/api/update_crypto_pair_value");
        $this->send_crypto_fiat($rates_result);

    }

    private function send_get(){
        $this->set_curl_url("https://api.cryptowat.ch/markets/prices");
        return $this->do_send_get();
    }

    private function run(){
        $result = $this->send_get();
        print_r(gettype($result));

        if ($result){
            $allowance = $result['allowance']['remaining'];
            $this->statsd->statsd->gauge('cryptowatch_remaining', $allowance, 1);
            $result = $result['result'];
            $this->send_post($result);
        }

        $this->close_curl_conn();
    }

    public function run_task(){
        $this->run();
    }

}
