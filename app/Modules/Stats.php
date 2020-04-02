<?php

namespace App\Modules;

require __DIR__ . "/php-datadogstatsd/src/DogStatsd.php";

use DataDog\DogStatsd;

class Stats
{

    public $statsd;


    private $config = array(
        "host" => "167.86.75.179",
        "port" => 8125
    );


    public function __construct() {
        $this->statsd = new DogStatsd($this->config);
    }
}
