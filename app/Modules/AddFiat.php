<?php


namespace App\Modules;
use ErrorException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddFiat extends Base
{
    protected $config = array(
        array("usd", "American dollar"),
        array("eur", "Euro"),
        array("jpy","Japanese yen"),
        array("bgn","Bulgarian lev"),
        array("czk","Czech koruna "),
        array("dkk","Danish krone  "),
        array("gbp","Pound sterling"),
        array("huf","Hungarian forint"),
        array("pln","Polish zloty"),
        array("ron","Romanian leu"),
        array("sek","Swedish krona"),
        array("chf","Swiss franc"),
        array("isk","Icelandic krona"),
        array("nok","Norwegian krone"),
        array("hrk","Croatian kuna"),
        array("rub","Russian rouble"),
        array("try","Turkish lira"),
        array("aud","Australian dollar"),
        array("brl","Brazilian real"),
        array("cad","Canadian dollar"),
        array("cny","Chinese yuan renminbi"),
        array("hkd","Hong Kong dollar"),
        array("idr","Indonesian rupiah"),
        array("ils","Israeli shekel"),
        array("inr","Indian rupee"),
        array("krw","South Korean won"),
        array("mxn","Mexican peso"),
        array("myr","Malaysian ringgit"),
        array("nzd","New Zealand dollar"),
        array("php","Philippine peso"),
        array("sgd","Singapore dollar"),
        array("thb","Thai baht"),
        array("zar","South African ran"),
    );

    protected $url = 'http://127.0.0.1:8000/api/fiat';

    private function send_get(){
	$this->set_curl_url("https://api.exchangeratesapi.io/latest?base=USD");
	return $this->do_send_get();

    }

    private function send_post($rates, $date){
        $this->set_curl_post();
        $this->set_curl_url($this->url);

        foreach ($this->config as $item){
            $payload = json_encode(array(
                "Id"=>$item[0],
                "Name"=>$item[1],
                "Value"=>$rates['rates'][strtoupper($item[0])],
                "Key"=>$date
            ));
            print_r($payload);
            exit(0);
            $this->do_send_post($payload);
        }

        $this->close_curl_conn();
    }

    public function run_task(){
	$data = $this->send_get();
        if ($data){
            $this->send_post($data, $data["date"]);
        }
    }

    public function run_init_db_task(){
        $endpointParts = parse_url("http://127.0.0.1:8000/api/fiat");
        $socket = fsockopen($endpointParts['host'], $endpointParts['port']);

        $min_timestamp = DB::table("crypto_historical")->select(DB::raw('min("Timestamp")'))->get();

        if ($min_timestamp){
            $min_timestamp = $min_timestamp[0]->min;
            $min_timestamp = date("Y-m-d", $min_timestamp);
            $today = date("Y-m-d");

            $url = "https://api.exchangeratesapi.io/history?start_at={$min_timestamp}&end_at={$today}&base=USD";
            $this->set_curl_url($url);
            $data = $this->do_send_get();

            foreach ($data['rates'] as $key => $rates){
                foreach ($this->config as $item){
                    try {
                        $payload = json_encode(array(
                            "Id"=>$item[0],
                            "Name"=>$item[1],
                            "Value"=>$rates[strtoupper($item[0])],
                            "Key"=>$key
                        ));
                        $this->fire_and_forget_post($socket, "http://127.0.0.1:8000/api/fiat", $payload);
                    }
                    catch (ErrorException $e){
                        continue;
                    }
                }
            }
            return 0;
        }

        print_r("Something went worng.");
        return -1;

    }
}
