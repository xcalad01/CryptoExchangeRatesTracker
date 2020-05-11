<?php


namespace App\Modules;


class Binance extends Base
{
    protected $config = array(
        'BTCEUR',
        'TRXBTC',
        'XMRBTC',
        'LTCBTC',
        'EOSBTC',
        'ETHEUR',
        'XRPEUR',
        'ADABTC',
        'XLMBTC',
        'XTZBTC',
        'NEOBTC',
        'DASH-BTC',
        'ETCBTC',
        'ZECBTC',
        'XEMBTC',
        'DOGE-BTC',
        'QTUM-BTC',
        'BTGBTC',
        'ZRXBTC',
        'USDT-RUB'
    );

    private $exchange_id = "binance";

    private $timestamp = null;

    private $init_db_start_timestamp = "1498867200000";
    private $init_db_stop_timestamp = "1585699200000";

    private function send_get(){
        $results = array();
        $start_end_timestamp = ($this->timestamp - 60) * 1000;

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

            $url = "https://api.binance.com/api/v3/klines?symbol={$item}&interval=1m&startTime={$start_end_timestamp}&endTime={$start_end_timestamp}";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            if ($data){
                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));

                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $this->timestamp - 60,
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

        return $results;
    }

    private function send_post($payload){
        $this->set_curl_post();
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto/historical');

        foreach ($payload as $item) {
            $payload = json_encode($item);
            $this->do_send_post($payload);
        }
        $this->close_curl_conn();
    }

    public function run_task(){
        $this->timestamp = strtotime(date('Y-m-d H:i'));
        $payload = $this->send_get();
        if (!empty($payload)){
            $this->send_post($payload);
        }
    }

    public function run_init_db_task(){
        $endpointParts = parse_url("http://127.0.0.1:8000/api/crypto/historical");
        $socket = fsockopen($endpointParts['host'], $endpointParts['port']);

        foreach ($this->config as $config_item){
            print_r("Pair: {$config_item}\n");

            $from = strtolower(substr($config_item,0, 3));
            $to = strtolower(substr($config_item, 3, 3));

            print_r("Requesting from UTC timetamp: {$this->init_db_start_timestamp}\n");
            $url = "https://api.binance.com/api/v3/klines?symbol={$config_item}&interval=15m&startTime={$this->init_db_start_timestamp}&limit=1000";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            $last_timestamp = null;

            $do_break = false;

            while (true) {
                foreach ($data as $item){
                    if ($item[0] >= $this->init_db_stop_timestamp) {
                        $do_break = true;
                        break;
                    }
                    $last_timestamp = $item[0];
                    $body = array(
                        "Exchange_id" => $this->exchange_id,
                        "From" => $from,
                        "To" => $to,
                        "Timestamp" => $item[0] / 1000,
                        "Historical" => array(
                            null,
                            $item[1],
                            $item[2],
                            $item[3],
                            $item[4],
                            $item[5]
                        )
                    );
                    $body = json_encode($body);
                    $this->fire_and_forget_post($socket,"http://127.0.0.1:8000/api/crypto/historical", $body);
                }

                if ($do_break) {
                    break;
                }

                print_r("Requesting from timestamp: {$last_timestamp}\n");
                $url = "https://api-pub.bitfinex.com/v2/candles/trade:15m:tBTCUSD/hist?limit=10000&start={$last_timestamp}&sort=1";
                $this->set_curl_url($url);
                $data = $this->do_send_get();
            }

            break; // BTC - EUR for now
        }
    }
}
