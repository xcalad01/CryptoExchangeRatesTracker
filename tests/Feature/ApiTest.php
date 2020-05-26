<?php

/**
 *
 * ApiTest.php
 *
 * Test API
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 */

namespace Tests\Feature;

use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * Test ping
     *
     * @return void
     */
    public function test_ping(){
        $response = $this->get('/api/ping');

        $response->assertStatus(200);
        assert($response->json() == array("message" => "pong"));
    }

    /**
     * Test crypto asset info endpoint
     *
     * @return void
     */
    public function test_crypto_asset_info(){
        $response = $this->get('/api/crypto/asset/info/btc');
        $response->assertStatus(200);
        assert($response->json()['data']['name'] == "Bitcoin");

        $response = $this->get('/api/crypto/asset/info/xxx');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin xxx is not supported"));
    }

    /**
     * Test historical asset value info endpoint
     *
     * @return void
     */
    public function test_historical_asset_value(){
        $response = $this->get('/api/crypto/historical/asset/value/btc/usd/1587340800/1587427200');
        $response->assertStatus(200);
        assert(count($response->json()['data']) == 1);

        $response = $this->get('/api/crypto/historical/asset/value/xxx/usd/1587340800/1587427200');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin xxx is not supported"));

        $response = $this->get('/api/crypto/historical/asset/value/yyy/usd/1587340800/1587427200');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin yyy is not supported"));

        $response = $this->get('/api/crypto/historical/asset/value/btc/usd/1687340800/1587427200');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Start > End"));
    }

    /**
     * Test historical asset value with range info endpoint
     *
     * @return void
     */
    public function test_historical_asset_value_range(){
        $response = $this->get('/api/crypto/historical/asset/value/btc/usd/1587340800/1587427200/1h');
        $response->assertStatus(200);
        assert(count($response->json()['data']) == 24);

        $response = $this->get('/api/crypto/historical/asset/value/btc/usd/1587340800/1587427200/xxx');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Time range not supported"));

        $response = $this->get('/api/crypto/historical/asset/value/btc/usd/1587340800/1587380400/12h');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Range not between start and end"));
    }

    /**
     * Test asset all time endpoint
     *
     * @return void
     */
    public function test_all_time_asset(){
        $response = $this->get('/api/crypto/historical/asset/all_time/btc');
        $response->assertStatus(200);

        $response = $this->get('/api/crypto/historical/asset/all_time/xxx');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin xxx is not supported"));
    }

    /**
     * Test historical pairs endpoint
     *
     * @return void
     */
    public function test_historical_pairs(){
        $response = $this->get('/api/crypto/historical/pairs/kraken');
        $response->assertStatus(200);

        $response = $this->get('/api/crypto/historical/pairs/xxx');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Exchange is not supported"));
    }

    /**
     * Test exchange stats endpoint
     *
     * @return void
     */
    public function test_exchange_stats(){
        $response = $this->get('/api/exchange/stats/kraken');
        $response->assertStatus(200);

        $response = $this->get('/api/exchange/stats/xxx');
        print_r($response->content());
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Exchange is not supported"));
    }

    /**
     * Test exchange ohlc endpoint
     *
     * @return void
     */
    public function test_historical_ohlc(){
        $response = $this->get('/api/crypto/historical/ohlc/1587340800/1587427200/kraken/1h/btc/usd');
        $response->assertStatus(200);

        $resp_json = $response->json();
        assert($resp_json['data'][0]['x'] == 1587340800);
        assert($resp_json['data'][count($resp_json['data']) - 1]['x'] == 1587427200);

        $response = $this->get('/api/crypto/historical/ohlc/1587340800/1587427200/kraken/1h/xxx/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin xxx is not supported"));

        $response = $this->get('/api/crypto/historical/ohlc/1587340800/1587427200/kraken/1h/btc/yyy');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin yyy is not supported"));

        $response = $this->get('/api/crypto/historical/ohlc/1687340800/1587427200/kraken/1h/btc/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Start > End"));

        $response = $this->get('/api/crypto/historical/ohlc/1587340800/1587380400/kraken/12h/btc/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Range not between start and end"));
    }

    /**
     * Test exchange value endpoint
     *
     * @return void
     */
    public function test_historical_value(){
        $response = $this->get('/api/crypto/historical/value/1587340800/1587427200/kraken/1h/btc/usd');
        $response->assertStatus(200);

        $resp_json = $response->json();
        assert($resp_json['data'][0][0] == 1587340800);
        assert($resp_json['data'][count($resp_json['data']) - 1][0] == 1587427200);

        $response = $this->get('/api/crypto/historical/value/1587340800/1587427200/kraken/1h/xxx/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin xxx is not supported"));

        $response = $this->get('/api/crypto/historical/value/1587340800/1587427200/kraken/1h/btc/yyy');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin yyy is not supported"));

        $response = $this->get('/api/crypto/historical/value/1687340800/1587427200/kraken/1h/btc/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Start > End"));

        $response = $this->get('/api/crypto/historical/value/1587340800/1587380400/kraken/12h/btc/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Range not between start and end"));
    }

    /**
     * Test exchange volume endpoint
     *
     * @return void
     */
    public function test_historical_volume(){
        $response = $this->get('/api/crypto/historical/volume/1587340800/1587427200/kraken/1h/btc/usd');
        $response->assertStatus(200);

        $resp_json = $response->json();
        assert($resp_json['data'][0]['x'] == 1587340800);
        assert($resp_json['data'][count($resp_json['data']) - 1]['x'] == 1587427200);

        $response = $this->get('/api/crypto/historical/volume/1587340800/1587427200/kraken/1h/xxx/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin xxx is not supported"));

        $response = $this->get('/api/crypto/historical/volume/1587340800/1587427200/kraken/1h/btc/yyy');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin yyy is not supported"));

        $response = $this->get('/api/crypto/historical/volume/1687340800/1587427200/kraken/1h/btc/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Start > End"));

        $response = $this->get('/api/crypto/historical/volume/1587340800/1587380400/kraken/12h/btc/usd');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Range not between start and end"));
    }

    /**
     * Test fiat supported
     *
     * @return void
     */
    public function test_fiat_supported(){
        $response = $this->get('/api/fiat/all_supported');
        $response->assertStatus(200);
    }

    /**
     * Test fiat historical v2 endpoint
     *
     * @return void
     */
    public function test_fiat_historical_v2(){
        $response = $this->get('/api/fiat/historical_v2/1377836800/usd,eur');
        $response->assertStatus(200);

        $response = $this->get('/api/fiat/historical_v2/1377836800/usd,eur,x');
        $response->assertStatus(404);
        $response->assertExactJson(array("message" => "Coin x is not supported"));
    }
}
