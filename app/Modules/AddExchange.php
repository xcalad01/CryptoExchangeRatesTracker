<?php

namespace App\Modules;

class AddExchange extends Base
{
    protected $config = array(
        "exchanges" => ['gemini', 'gdax', 'kraken', 'bitstamp', 'bitfinex', 'cex', 'poloniex', 'binance', 'bittrex', 'hitbtc', 'binance', 'bitbay', 'okcoin', 'okex']
    );

    private function send_get(){
        $this->set_url_base("https://api.coingecko.com/api/v3/exchanges/%s");
        $result = array();
        foreach ($this->config['exchanges'] as $exchange){
            $url = sprintf($this->url_base, $exchange);
            $this->set_curl_url($url);

            $data = $this->do_send_get();
            array_push($result, $data);
        }

        return $result;
    }

    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/exchange');

        $counter = 0;
        foreach ($payload as $item) {
            $item_payload = json_encode(array(
                'Exchange_id' => $this->config['exchanges'][$counter],
                'Name' => $item['name'],
                'Image' => $item['image'],
                'Url' => $item['url']
            ));
            $this->do_send_post($item_payload);
            $counter += 1;
        }
        $this->close_curl_conn();
    }

    private function run(){
        $result = $this->send_get();
        if ($result){
            $this->send_post($result);
        }
    }

    public function run_task(){
        $this->run();
    }
}
