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
        'DASHBTC',
        'ETCBTC',
        'ZECBTC',
        'XEMBTC',
        'DOGEBTC',
        'QTUMBTC',
        'BTGBTC',
        'ZRXBTC',
        'USDTRUB'
    );

    private $exchange_id = "binance";

    private $timestamp = null;

    private function send_get(){
        $results = array();
        $start_end_timestamp = ($this->timestamp - 60) * 1000;

        foreach ($this->config as $item){
            $url = "https://api.binance.com/api/v3/klines?symbol={$item}&interval=1m&startTime={$start_end_timestamp}&endTime={$start_end_timestamp}";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            if ($data){
                $from = strtolower(substr($item,0, 3));
                $to = strtolower(substr($item, 3, 3));

                $this->statsd->statsd->increment('hist_five_min_downloaded', 1, array('exchange' => $this->exchange_id, 'from' => $from, 'to' => $to));

                array_push($results, array(
                    "Exchange_id" => $this->exchange_id,
                    "From" => $from,
                    "To" => $to,
                    "Timestamp" => $this->timestamp,
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
        $this->set_curl_url('http://127.0.0.1:8000/api/crypto_historical');

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
}
