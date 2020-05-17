<?php

namespace App\Modules;

require __DIR__ . "/php-datadogstatsd/src/DogStatsd.php";

use DataDog\DogStatsd;

class Stats
{
    /**
     * Datadog statsd handler
     * @var DogStatsd
     */
    public $statsd;

    /**
     * Config of host and port
     * TODO: make env variables for this !
     * @var array
     */
    private $config = array(
        "host" => "167.86.75.179",
        "port" => 8125
    );


    public function __construct() {
        $this->statsd = new DogStatsd($this->config);
    }
}
