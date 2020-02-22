<?php


namespace App\Modules;

class AddExchange extends Base
{
    protected $config = array(
        "exchanges" => ['gemini', 'gdax', 'kraken', 'bitstamp', 'bitfinex', 'cex', 'poloniex', 'binance', 'bittrex', 'hitbtc', 'binance', 'bitbay', 'okcoin', 'okex']
    );

    private function send_get(){
        $this->set_url_base("https://api.coingecko.com/api/v3/exchanges/%s");
        foreach ($this->config['exchanges'] as $exchange){
            $url = sprintf($this->url_base, $exchange);
            $this->set_curl_url($url);

            $data = $this->do_send_get();
            return json_encode($data, true);
        }

        return 0;
    }

    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/exchange');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            $this->do_send_post($payload);
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
