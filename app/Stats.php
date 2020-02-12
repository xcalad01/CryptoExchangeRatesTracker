<?php

namespace Stats;

require __DIR__ . "/php-datadogstatsd/src/DogStatsd.php";

use DataDog\DogStatsd;

class Stats
{

    public $statsd;


    private $config = array(
        "host" => "127.0.0.1",
        "port" => 8125
    );


    public function __construct() {
        $this->statsd = new DogStatsd($this->config);
    }
}
