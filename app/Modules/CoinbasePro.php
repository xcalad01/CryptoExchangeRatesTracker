<?php


namespace App\Modules;


class CoinbasePro extends Base
{
    private $config = array(
        'BTC-USD',
        'ETH-USD',
        'BCH-USD',
        'LTC-USD',
        'EOS-USD',
        'XRP-USD',
        'XLM-USD',
        'XTZ-USD',
        'DASH-USD',
        'ETC-USD',
        'ZEC-USDC',
        'ZRX-USD',
    );

    protected $timestamp = null;
    protected $date = null;
    protected $exchange_id = "gdax";

    private function send_get(){
        $results = array();

        $this->set_user_agent("get");

        foreach ($this->config as $item){

            $url = "https://api.pro.coinbase.com/products/{$item}/candles?start={$this->date}&end={$this->date}&granularity=60";
            $this->set_curl_url($url);
            $data = $this->do_send_get();
            if ($data){
                if (array_key_exists('message', $data)){
                    print_r($data);
                    continue;
                }
                $from = strtolower(substr($item,0, 3));
                $to = strtolower(substr($item, 4, 3));

                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));

                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $data[0],
                    "Historical" => array(
                        null,
                        $data[0][3],
                        $data[0][1],
                        $data[0][2],
                        $data[0][4],
                        $data[0][5]
                    )
                ));
            }
            sleep(0.9);
        }

        return $results;
    }

    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto_historical');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            $this->do_send_post($payload);
        }
        $this->close_curl_conn();
    }

    public  function run_task(){
       $this->date = date('Y-m-d\TH:i',strtotime('-1 minute',strtotime(date('Y-m-d H:i'))));
       $this->timestamp = strtotime($this->date);

        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }

    }

}
