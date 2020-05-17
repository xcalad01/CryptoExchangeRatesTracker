<?php

namespace App\Modules;

/**
 * Class AddExchange
 * @package App\Modules
 */
class AddExchange extends Base
{
    /**
     * Config of exchange ids and payment methods
     * @var array
     */
    protected $config = array(
        "exchanges" => [
            ['gemini', 'Wire Transfer ACH BTC ETH'],
            ['gdax', 'PayPal (US only) Bank Account (US only) Deposit Credit/Debit Card'],
            ['kraken', 'Wire transfer'],
            ['bitstamp', 'Debit/ Credit Card SEPA International Wire Transfer'],
            ['bitfinex', 'Wire transfer'],
            ['cex', 'Credit card Bank Transfer'],
            ['poloniex', 'BTC USD ETH'],
            ['binance', 'Cryptocurrency only'],
            ['bittrex', 'BTC and other supported cryptocurrencies, USDT'],
            ['hitbtc', 'BTC and other supported cryptocurrencies, USDT'],
            ['bitbay', 'EUR & USD - Credit/ Debit Card PLN - Cash deposit (Zabka or FreshMarket store, Polish Post Office)'],
            ['okcoin', 'Bank Transfer Epay Prime X'],
            ['okex', 'Cryptocurrency only ']
        ]
    );

    /**
     * Sends get request to coingecko API for acquire info about specific exchange
     * @return array
     */
    private function send_get(){
        $this->set_url_base("https://api.coingecko.com/api/v3/exchanges/%s");
        $result = array();
        foreach ($this->config['exchanges'] as $exchange_config){
            $url = sprintf($this->url_base, $exchange_config[0]);
            $this->set_curl_url($url);

            $data = $this->do_send_get();
            array_push($result, $data);
        }

        return $result;
    }

    /**
     * Sends post request to internal API to store the exchange info
     * @param $payload
     */
    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/exchange');

        $counter = 0;
        foreach ($payload as $item) {
            $item_payload = json_encode(array(
                'Exchange_id' => $this->config['exchanges'][$counter][0],
                'Name' => $item['name'],
                'Image' => $item['image'],
                'Url' => $item['url'],
                'Year_established'=> $item['year_established'] ?: "",
                'Country'=> $item['country'] ?: "",
                'Centralized'=> $item['centralized'] ?: null,
                'Accepted_payment_methods'=> $this->config['exchanges'][$counter][1],
                'Facebook_url' => $item['facebook_url'] ?: "None",
                'Reddit_url' => $item['reddit_url'] ?: "None",
                "Twitter" => $item['twitter_handle'] ?: "None"
            ));
            print_r($this->do_send_post($item_payload));
            $counter += 1;
        }
        $this->close_curl_conn();
    }

    /**
     * First get info than store to DB
     */
    private function run(){
        $result = $this->send_get();
        if ($result){
            $this->send_post($result);
        }
    }

    /**
     * Run add exchange task
     */
    public function run_task(){
        $this->run();
    }
}
