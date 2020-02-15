<?php


namespace App\Modules\AddFiat;

require __DIR__ . "/ModuleBase.php";

use App\Modules\ModuleBase\ModuleBase;

class AddFiat extends ModuleBase
{
    protected $config = array(
        array("usd", "American dollar", "1")
    );

    protected $url = 'http://127.0.0.1:8000/api/fiat';

    private function send_post(){
        $this->set_curl_post();
        $this->set_curl_url($this->url);

        foreach ($this->config as $item){
            $payload = json_encode(array(
                "Id"=>$item[0],
                "Name"=>$item[1],
                "Value"=>$item[2]
            ));
            $this->do_send_post($payload);
        }

        $this->close_curl_conn();
    }

    public function run_task(){
        $this->send_post();
    }
}
