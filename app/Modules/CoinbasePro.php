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
        print_r("Sending get to coinbase_pro API");

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
                if (strpos($item, 'DASH') !== false) {
                    $from = strtolower(substr($item,0, 4));
                    $to = strtolower(substr($item, 5, 3));
                }
                else{
                    $from = strtolower(substr($item,0, 3));
                    $to = strtolower(substr($item, 4, 3));
                }

                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));

                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $data[0][0],
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
            sleep(1);
        }

        print_r("API resutls: \n");
        print_r($results);

        return $results;
    }

    private function send_post($payload){
        print_r("Sending API results to DB\n");
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto/historical');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            print_r($this->do_send_post($payload)."\n");
        }
        $this->close_curl_conn();
        print_r("All sended");
    }

    public  function run_task(){
        print_r("OHLC querying started\n");

       $this->date = date('Y-m-d\TH:i',strtotime('-1 minute',strtotime(date('Y-m-d H:i'))));
       $this->timestamp = strtotime($this->date);

        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }

        print_r("OHLC querying ended\n");
    }

}
