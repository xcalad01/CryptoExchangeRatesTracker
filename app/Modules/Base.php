<?php

namespace App\Modules;
use Illuminate\Support\Facades\Log;

class Base
{
    protected $statsd;
    private $ch;
    private $get_headers = array(
        'Content-Type: application/json',
        'Accept: application/json'
    );

    protected $url_base;

    public function __construct(){
        $this->statsd = new Stats();
	    $this->new_curl_instance();
	    $this->post_socket = null;
    }

    protected function new_curl_instance(){
        $this->ch = curl_init();
    }

    protected function set_url_base($url){
        $this->url_base = $url;
    }

    protected function check_crypto_id_name($item){
        if (strpos($item[0], '/') !== false){
            $exchange_id_crypto_watch = substr($item[0],0,strpos($item[0], '/'));
            $exchange_id_db = substr($item[0],strpos($item[0], '/') + 1);
        }
        else{
            $exchange_id_crypto_watch = $item[0];
            $exchange_id_db = $item[0];
        }

        return array($exchange_id_crypto_watch, $exchange_id_db);
    }

    protected function set_curl_url($url){
        curl_setopt($this->ch, CURLOPT_URL, $url);
    }

    protected function set_curl_post(){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
	        'Accept: application/json'
        ));
    }

    protected function close_curl_conn(){
        curl_close($this->ch);
    }

    protected function set_user_agent($key){
        if ($key == "get"){
            array_push($this->get_headers, "User-Agent: PostmanRuntime/7.23.0");
        }
    }

    protected function do_send_post($payload){
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
		    'Content-Length: ' . strlen($payload))
        );
	    $result = curl_exec($this->ch);
        $return_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        if($return_code != 200){
            $this->statsd->statsd->increment("api.error", 1, array('message'=>$result));
        }

        return $result;
    }

    protected function do_send_get(){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->get_headers);

        return json_decode(curl_exec($this->ch), true);
    }

    protected function fire_and_forget_post($socket, $endpoint, $postData){
        $endpointParts = parse_url($endpoint);

        $contentLength = strlen($postData);

        $request = "POST {$endpointParts['path']} HTTP/1.1\r\n";
        $request .= "Host: {$endpointParts['host']}\r\n";
        $request .= "Content-Length: {$contentLength}\r\n";
        $request .= "Content-Type: application/json\r\n\r\n";
        $request .= $postData;

        $prefix = substr($endpoint, 0, 8) === 'https://' ? 'tls://' : '';

        $socket = fsockopen($prefix.$endpointParts['host'], $endpointParts['port']);
        fwrite($socket, $request);
    }
}
