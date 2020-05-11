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
            try {
                $payload = json_encode(array(
                    "Id"=>$item[0],
                    "Name"=>$item[1],
                    "Value"=>$rates['rates'][strtoupper($item[0])],
                    "Key"=>$date
                ));
                print_r($this->do_send_post($payload));
            }
            catch (\Exception $e){
                print_r($e->getMessage());
            }
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
        $timestamp = 1433635200;
        $endpointParts = parse_url("http://127.0.0.1:8000/api/fiat");
        $socket = fsockopen($endpointParts['host'], $endpointParts['port']);

        try {
            while ($timestamp < 1588550400) {
                $date = date("Y-m-d", $timestamp);
                print_r($date."\n");
                $url = "https://api.exchangeratesapi.io/{$date}?&base=USD";
                print_r($url."\n");
                $this->set_curl_url($url);
                $data = $this->do_send_get();

                print_r($data);

                print_r($this->send_post($data, $date));

                print_r("\n");

                $timestamp += 86400;
            }
        }
        catch (\Exception $e){
            print_r($e->getMessage());
        }
    }
}
