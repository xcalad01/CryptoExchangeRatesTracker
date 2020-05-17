<?php


namespace App\Modules;

/**
 * Class Kraken
 * @package App\Modules
 */
class Kraken extends Base
{
    /**
     * Config of custom Kraken market pairs
     * @var array
     */
    protected $config = array(
        'BTCEUR',
        'ETHUSD',
        'XRPUSD',
        'BCHUSD',
        'LTCUSD',
        'EOSUSD',
        'TRXUSD',
        'XMRUSD',
        'ADAUSD',
        'XLMUSD',
        'XTZUSD',
        'DASH-USD',
        'ETCUSD',
        'ZECUSD',
        'QTUM-USD',
    );

    protected $exchange_id = 'kraken';

    private $timestamp = null;

    /**
     * Sends get request to Kraken API for OHLC and volume data
     * @return array|void
     */
    private function send_get(){
        print_r("Sending get to kraken API");

        $results = array();
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
            $url = "https://api.kraken.com/0/public/OHLC?pair={$item}&interval=1&since={$this->timestamp}";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            if ($data['error']){
                print_r($data['error']);
                return;
            }

            $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));
            $data = $data['result'];
            $keys = array_keys($data);
            array_push($results, array(
                "Exchange_id" => $this->exchange_id,
                "From" => $from,
                "To" => $to,
                "Timestamp" => $data[$keys[0]][0][0],
                "Historical" => array(
                    null,
                    $data[$keys[0]][0][1],
                    $data[$keys[0]][0][2],
                    $data[$keys[0]][0][3],
                    $data[$keys[0]][0][4],
                    $data[$keys[0]][0][6]
                )
            ));
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

    /**
     * Run get OHLC data task
     */
    public function run_task(){
        print_r("OHLC querying started\n");

        $this->timestamp = strtotime(date('Y-m-d H:i')) - 61;
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }

        print_r("OHLC querying ended\n");
    }
}
