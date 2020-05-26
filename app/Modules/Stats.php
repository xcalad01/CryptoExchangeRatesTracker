<?php

/**
 *
 * Stats.php
 *
 * Datadog statsd
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */

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
     * Datadog host
     * @var array|false|string
     */
    private $datadog_host;

    public function __construct() {
        $this->datadog_host = getenv('DD_AGENT_HOST');
        $this->statsd = new DogStatsd(array(
            "host" => $this->datadog_host,
            "port" => 8125
        ));
    }
}
