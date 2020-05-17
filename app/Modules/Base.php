<?php

namespace App\Modules;

/**
 * Class Base
 * @package App\Modules
 * Base class for all modules which interact with internal API.
 */
class Base
{
    /**
     * Datadog statsd
     */
    protected $statsd;

    /**
     * Curl handler
     */
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

    /**
     * Inits new curl instance
     */
    protected function new_curl_instance(){
        $this->ch = curl_init();
    }

    /**
     * Sets new url base
     */
    protected function set_url_base($url){
        $this->url_base = $url;
    }

    /**
     * Checks custom crypto pair id an returns ids for cryptowatch and internal API
     * @param $item
     * @return array
     */
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

    /**
     * Sets url for curl handler
     * @param $url
     */
    protected function set_curl_url($url){
        curl_setopt($this->ch, CURLOPT_URL, $url);
    }

    /**
     * Sets curl post parameters
     */
    protected function set_curl_post(){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
	        'Accept: application/json'
        ));
    }

    /**
     * Closes curl connection
     */
    protected function close_curl_conn(){
        curl_close($this->ch);
    }

    /**
     * Set User Agent in get headers
     * @param $key
     */
    protected function set_user_agent($key){
        if ($key == "get"){
            array_push($this->get_headers, "User-Agent: PostmanRuntime/7.23.0");
        }
    }

    /**
     * Sends post request
     * @param $payload
     * @return bool|string
     */
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

    /**
     * Sends get request
     * @return mixed
     */
    protected function do_send_get(){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->get_headers);

        return json_decode(curl_exec($this->ch), true);
    }

//    /**
//     * Send request and do not wait for response
//     * @param $socket
//     * @param $endpoint
//     * @param $postData
//     */
//    protected function fire_and_forget_post($socket, $endpoint, $postData){
//        $endpointParts = parse_url($endpoint);
//
//        $contentLength = strlen($postData);
//
//        $request = "POST {$endpointParts['path']} HTTP/1.1\r\n";
//        $request .= "Host: {$endpointParts['host']}\r\n";
//        $request .= "Content-Length: {$contentLength}\r\n";
//        $request .= "Content-Type: application/json\r\n\r\n";
//        $request .= $postData;
//
//        $prefix = substr($endpoint, 0, 8) === 'https://' ? 'tls://' : '';
//
//        $socket = fsockopen($prefix.$endpointParts['host'], $endpointParts['port']);
//        fwrite($socket, $request);
//    }
}
