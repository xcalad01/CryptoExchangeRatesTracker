<?php


namespace App\Modules;


class OkCoin extends Base
{
    private $config = array(
        "BTC-USD",
        "TRX-USD",
        "BCH-USD",
        "LTC-USD",
        "EOS-USD",
        "ETH-USD",
        "XRP-USD",
        "ETC-USD",
        "USDT-USD"
    );

    private $exchange_id = "okcoin";
    private $timestamp;

    private function send_get(){
        print_r("Sending get to kraken API\n");

        $results = array();
        foreach ($this->config as $item){
            $url = "https://www.okcoin.com/api/spot/v3/instruments/{$item}/candles?granularity=60&start={$this->timestamp}&end={$this->timestamp}";
            $this->set_curl_url($url);
            $data = $this->do_send_get();
            if ($data){
                if (strpos($item, 'USDT') !== false) {
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
                    "Timestamp" => strtotime($this->timestamp),
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

    public function run_task(){
        print_r("OHLC querying started\n");

        $this->timestamp = strtotime(date('Y-m-d H:i')) - 60;
        $this->timestamp = date("Y-m-d\TH:i:00.000", $this->timestamp) . "Z";
        print_r($this->timestamp);
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }

        print_r("OHLC querying ended\n");
    }

}
