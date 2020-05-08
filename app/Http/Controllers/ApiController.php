<?php

namespace App\Http\Controllers;
require __DIR__ . "/../../Modules/Stats.php";

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Exchange;
use App\Historical_available;
use App\CryptoHistorical;
use App\Cryptocurrencies;
use App\Crypto_exchange_pair;
use App\Crypto_fiat_exchange_pair;
use App\Fiat;
use App\Fiat_historical;
use App\Modules\Stats;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;


class ApiController extends Controller
{
    private $statsd;

    private $time_range_config;

    private $coins_config;

    private $coin_cap_candles_curl;

    private $coin_gecko_client;

    public function __construct()
    {
        $this->statsd = new Stats();
        $this->time_range_config = array(
            "1d" => 86400,
            "12h" => 43200,
            "6h" => 21600,
            "3h" => 10800,
            "1h" => 3600,
            "5m" => 300,
            "1m" => 60
        );

        $this->coins_config = array(
            "btc" => "bitcoin",
            "eth" => "ethereum",
            "trx" => "tron",
            "xmr" => "monero",
            "bch" => "bitcoin-cash",
            "ltc" => "litecoin",
            "eos" => "eos",
            "xrp" => "ripple",
            "ada" => "cardano",
            "xlm" => "stellar",
            "xtz" => "tezos",
            "neo" => "neo",
            "dash" => "dash",
            "etc" => "ethereum-classic",
            "zec" => "zcash",
            "doge" => "dogecoin",
            "usdt" => "tether",
            "xem" => "nem",
            "qtum" => "qtum",
            "btg" => "bitcoin-gold",
            "zrx" => "0x",
            "usd" => "united-states-dollar",
            "eur" => "euro",
        );

        $this->coin_cap_candles_curl = curl_init();

        $this->coin_gecko_client = new CoinGeckoClient();
    }

    /**
     * Ping
     */
    public function ping(){
        return response()->json([
            "message" => "pong"
        ], 200);
    }

    public function create_exchange(Request $request) {
        try {
            $exchange = Exchange::where('Exchange_id', $request['Exchange_id'])->first();
            if ($exchange){
                Exchange::where('Exchange_id', $request['Exchange_id'])->update(array(
                    "Url"=>$request['Name'],
                    "Image"=>$request['Url'],
                    "Year"=>$request['Year_established'],
                    "Country"=>$request['Country'],
                    "Centralized"=>$request['Centralized'],
                    "Accepted_payment_methods"=>$request['Accepted_payment_methods'],
                    "Facebook"=>$request['Facebook_url'],
                    "Reddit"=>$request['Reddit_url'],
                    "Twitter"=>$request['Twitter']
                ));
                $this->statsd->statsd->increment("db.connections", 1, array("function"=>"update_exchange"));
                return response()->json([
                    "message" => "Exchange record updated"
                ], 200);
            }
            else{
                $exchange = new Exchange;
                $exchange->Exchange_id = $request['Exchange_id'];
                $exchange->Name = $request['Name'];
                $exchange->Url = $request['Url'];
                $exchange->Image = $request['Image'];
                $exchange->Year = $request['Year_established'];
                $exchange->Country = $request['Country'];
                $exchange->Centralized = $request['Centralized'];
                $exchange->Accepted_payment_methods = $request['Accepted_payment_methods'];
                $exchange->Facebook = $request['Facebook_url'];
                $exchange->Reddit = $request['Reddit_url'];
                $exchange->Twitter = $request['Twitter'];
                $exchange->save();
                $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_exchange"));
                return response()->json([
                    "message" => "Exchange record created"
                ], 200);
            }
        }
        catch (QueryException $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 501);
        }
    }

    private function create_available($data){
        $available = new Historical_available;
        $available->From = $data['from'];
        $available->To = $data['to'];
        $available->Exchange_id = $data['exchange_id'];

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_available"));

        $available->save();
    }

    private function add_crypto_historical_five($data, $exchange_id, $descr=null){
        $historical = new CryptoHistorical;
        $historical->id = $data['id'];
        $historical->Timestamp = $data['timestamp'];
        $historical->Open = $data['historical'][1];
        $historical->High = $data['historical'][2];
        $historical->Low = $data['historical'][3];
        $historical->Close = $data['historical'][4];
        $historical->Volume = $data['historical'][5];

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"add_crypto_historical_five", "exchange_id"=>"{$exchange_id}", "description"=>"{$descr}"));

        $historical->save();
    }

    public function crypto_add_historical(Request $request) {
        $exchange_id = $request['Exchange_id'];
        $available = Historical_available::where(['Exchange_id' => $exchange_id, 'From' => $request['From'], 'To' => $request['To']])->first();
        if (!$available) {
            $data = array(
                "from" => $request['From'],
                "to" => $request['To'],
                "exchange_id" => $request['Exchange_id']
            );
            try {
                self::create_available($data);
            }
            catch (QueryException $e){
                return response()->json([
                    "message" => $e->getMessage()
                ], 501);
            }

        }

        $available = Historical_available::where(['Exchange_id' => $exchange_id, 'From' => $request['From'], 'To' => $request['To']])->first();
        $data = array(
            "id"=>$available->id,
            'timestamp'=>$request['Timestamp'],
            "historical"=>$request['Historical']
        );

        try {
            self::add_crypto_historical_five($data, $exchange_id);
        }
        catch (QueryException $e){
            return response()->json([
                "message" => "crypto hist duplicate {$request['Exchange_id']}"
            ], 501);
        }

        return response()->json([
            "message" => "Historical five min saved"
        ], 200);
    }

    private function crypto_add_historical_internal($payload_data, $exchange_id, $from, $to){
        $available = Historical_available::where(['Exchange_id' => $exchange_id, 'From' => $from, 'To' => $to])->first();
        if (!$available) {
            $data = array(
                "from" => $from,
                "to" => $to,
                "exchange_id" => $exchange_id
            );
            try {
                self::create_available($data);
            }
            catch (QueryException $e){
                return response()->json([
                    "message" => $e->getMessage()
                ], 501);
            }

        }

        $available = Historical_available::where(['Exchange_id' => $exchange_id, 'From' => $from, 'To' => $to])->first();

        foreach ($payload_data as $payload){
            $data = array(
                "id"=>$available->id,
                'timestamp'=>$payload['Historical'][0],
                "historical"=>$payload['Historical']
            );

            try {
                self::add_crypto_historical_five($data, $exchange_id, "coincap");
            }
            catch (QueryException $e){
                return 0;
            }
        }
    }

    public function update_crypto_pair_value(Request $request){
	    foreach ($request['items'] as $item){
                    $type = $item['Type'];
                    if (!Exchange::where('Exchange_id', $item['Exchange'])->first()){
                        return response()->json([
                    "message"=>"Exchange $item[Exchange] does not exits"
                ], 404);
            }
            elseif (!Cryptocurrencies::where('Crypto_id', $item['From'])->first() and !Cryptocurrencies::where('Crypto_id', $item['To'])->first()){
                return response()->json([
                    "message"=>"Crypto $item[From] or $item[To] does not exits"
                ], 404);
            }

	    if ($type == "crypto") {
                $pair = Crypto_exchange_pair::where(['Exchange_id' => $item['Exchange'], 'From' => $item['From'], 'To' => $item['To']])->first();
            }
            else {
                $pair = Crypto_fiat_exchange_pair::where(['Exchange_id' => $item['Exchange'], 'From' => $item['From'], 'To' => $item['To']])->first();

            }
            if($pair){
                $this->statsd->statsd->increment("db.connections", 1, array("function"=>"update_crypto_pair_value"));
		        $pair->update(array("Value"=>$item['Value']));
		        continue;
            }

            if ($type == "crypto") {
                $to_save = new Crypto_exchange_pair;
            }
            else {
                $to_save = new Crypto_fiat_exchange_pair;
            }

            $to_save->Value = $item['Value'];
            $to_save->Exchange_id = $item['Exchange'];
            $to_save->From = $item['From'];
            $to_save->To = $item['To'];

            $this->statsd->statsd->increment("db.connections", 1, array("function"=>"update_crypto_pair_value"));

            try {
                $to_save->save();
            }
            catch (QueryException $e){
                return response()->json([
                    "message" => $e->getMessage()
                ], 501);
            }
        }

        return response()->json([
            "message" => "Crypto pair updated/created"
        ], 200);
    }

    public function create_crypto(Request $request){
        $crypto = new Cryptocurrencies;
        $crypto->Crypto_id = $request['Id'];
        $crypto->Name = $request['Name'];

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_crypto"));

        try {
            $crypto->save();
        }
        catch (QueryException $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 501);
        }

        return response()->json([
            "message" => "Crypto added"
        ], 200);
    }

    private function get_timestamp($date){
        return strtotime($date . '16:05:00');
    }

    private function get_today_timestamp($time){
        return strtotime($time);
    }

    private function insert_fiat($item){
        $fiat = Fiat::where("Fiat_id", $item['Id'])->first();
        if (!$fiat){
            $fiat = new Fiat;
            $fiat->Fiat_id = $item["Id"];
            $fiat->Name = $item["Name"];
        }

        $fiat_hist = Fiat_historical::where(["Date" => $this->get_timestamp($item["Key"]), "Fiat_id" => $item['Id']])->first();
        if ($fiat_hist){
            $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_update_fiat_special_day: {$this->get_timestamp($item["Key"])}"));
            $date = $this->get_today_timestamp("16:05");
        }
        else{
            $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_update_fiat_normal_day: {$this->get_timestamp($item["Key"])}"));
            $date = $this->get_timestamp($item["Key"]);
        }

        $fiat_hist = new Fiat_historical();
        $fiat_hist->Fiat_id = $fiat->Fiat_id;
        $fiat_hist->Value_USD = $item['Value'];
        $fiat_hist->Date = $date;


        $fiat->save();
        $fiat_hist->save();
    }

    public function create_fiat(Request $request){
	    try {
            $this->insert_fiat($request);
        }
        catch (QueryException $e){

            return response()->json([
                "message" => $e->getMessage()
            ], 501);
        }

        return response()->json([
            "message" => "FiatCommand added"
        ], 200);
    }

    private function check_exchange($exchange){
        $exchange = DB::table('exchanges')->where('Exchange_id', $exchange)->first();
        if (!$exchange){
            throw new \Exception('Exchange is not supported');
        }

        return $exchange;
    }

    private function check_coin($coin){
        $supported = DB::select(DB::raw("
            SELECT
	            *
                FROM(
	                SELECT
		                \"Fiat_id\" AS \"Coins\", 'fiat' as \"Type\"
	                FROM
		                \"fiat_historicals\"
	                UNION
	                SELECT
		                \"Crypto_id\", 'crypto'
	                FROM
		                \"cryptocurrencies\") AS \"fiats_cryptos\"
                    WHERE
	                    \"fiats_cryptos\".\"Coins\" = '{$coin}';
        "));

        if (!$supported){
            throw new \Exception("Coin {$coin} is not supported");
        }

        return $supported[0];
    }

    private function get_historical_available($exchange, $from){
        $historical_available = DB::table('historical_available')->where(array('Exchange_id'=>$exchange, 'From'=>$from))->first();
        if (!$historical_available){
            throw new \Exception("Exchange '{$exchange}' does not contain '{$from}' data");
        }

        return $historical_available;
    }

    private function get_fiat_historical($to, $start){
        $fiat_historical = DB::table('fiat_historicals')->where('Fiat_id',$to)->whereBetween('Date', [$start-86400, $start])->first();
        if (!$fiat_historical){
            throw new \Exception("Fiat '{$to}' not available in time range {$start} and {$start} - 1 day}");
        }

        return $fiat_historical;
    }

    public function get_crypto_value_time_range(Request $request, $start, $end, $exchange, $range, $from, $to){
        $historical_available = null;

        try {
            $this->check_exchange($exchange);

            $coin_info = $this->check_coin($to);

            if ($range and !key_exists($range, $this->time_range_config)){
                throw new \Exception('Time range not supported');
            }
            $range = $this->time_range_config[$range];

            if ($start + $range > $end){
                throw new \Exception('Range not between start and end');

	        }

            $historical_available = $this->get_historical_available($exchange, $from);

            $start = intval($start);
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }


        $values = array();
        if ($coin_info->Type == 'fiat'){
            $result = $this->value_fiat_time_range_query($range, $exchange, $historical_available, $to, $start, $end);
        }
        else{
            $result = $this->value_no_fiat_time_range_query($range, $exchange, $historical_available, $to, $start, $end);
        }

        foreach ($result as $data){
            array_push($values, array(
                strtotime($data->interval_alias),
                floatval($data->value)
            ));
        }

        return response()->json([
            "data" => $values
        ], 200);


    }

    public function get_crypto_ohlc_time_range(Request $request, $start, $end, $exchange, $range, $from, $to){
        $historical_available = null;

        try {
            $this->check_exchange($exchange);

            $coin_info = $this->check_coin($to);

            if ($range and !key_exists($range, $this->time_range_config)){
                throw new \Exception('Time range not supported');
            }

            $range = $this->time_range_config[$range];

            if ($start + $range > $end){
                throw new \Exception('Range not between start and end');
            }

            $historical_available = $this->get_historical_available($exchange, $from);


            $start = intval($start);

        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        if ($coin_info->Type == "fiat"){
            $result = $this->ohlc_fiat_time_range_query($range, $exchange, $historical_available, $to, $start, $end);
        }
        else{
            $result = $this->ohlc_no_fiat_time_range_query($range, $exchange, $historical_available, $to, $start, $end);
        }

        $values = array();
        foreach ($result as $data){
            array_push($values, array(
                "x" => strtotime($data->interval_alias),
                "y" => array(
                    floatval($data->Open),
                    floatval($data->High),
                    floatval($data->Low),
                    floatval($data->Close)
                )
            ));
        }
        return response()->json([
            "data" => $values
        ], 200);
    }


    public function get_all_hist_avail(Request $request, $exchange){
        try {
            $this->check_exchange($exchange);
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        $fiats = DB::table('fiats')->get("Fiat_id");

        $fiat_values = array();
        foreach ($fiats as $fiat){
            array_push($fiat_values, $fiat->Fiat_id);
        }

        $results = DB::table('historical_available')->where('Exchange_id', $exchange)->get();

        $values = array();
        foreach ($results as $result){
            if (!key_exists($result->From, $values)){
                $values[$result->From] = array();
            }
            array_push($values[$result->From], $result->To);
            if (in_array($result->To, array("eur", "usd"))){
                $values[$result->From] = array_unique(array_merge($values[$result->From], $fiat_values), SORT_REGULAR);
            }
        }

        return response()->json([
            "data" => $values
        ], 200);
    }


    public function get_crypto_volume_time_range(Request $request, $start, $end, $exchange, $range, $from, $to){
        $historical_available = null;

        try {
            $this->check_exchange($exchange);

            $coin_info = $this->check_coin($to);

            if ($range and !key_exists($range, $this->time_range_config)){
                throw new \Exception('Time range not supported');
            }
            $range = $this->time_range_config[$range];

            if ($start + $range > $end){
                throw new \Exception('Range not between start and end');
            }

            $historical_available = $this->get_historical_available($exchange, $from);

        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        $volume_data = array();

        if ($coin_info->Type == 'fiat'){
            $result = $this->volume_fiat_time_range_query($range, $exchange, $historical_available, $to, $start, $end);
        }
        else{
            $result = $this->volume_no_fiat_time_range_query($range, $exchange, $historical_available, $to, $start, $end);
        }

        foreach ($result as $res){
            array_push($volume_data, array(
                "x" => strtotime($res->interval_alias),
                "y" => floatval($res->Volume)
            ));
        }


        return response()->json([
            "data" => $volume_data
        ], 200);

    }

    public function fiat_historical(Request $request, $timestamp, $fiat, $old_fiat=null){
        try {
            $fiat = $this->get_fiat_historical($fiat, $timestamp);
            if ($old_fiat){
                $old_fiat = $this->get_fiat_historical($old_fiat, $timestamp);
            }
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        return response()->json([
            "data" => array(
                "fiat"=>$fiat->Value_USD,
                "old_fiat"=>$old_fiat->Value_USD
            )
        ], 200);


    }

    private function coin_cap_candles($exchange, $from, $to, $start, $end){
        $from_req = $this->coins_config[$from];
        $to_req = $this->coins_config[$to];

        $start_req = $start * 1000;
        $end_req = $end * 1000;

        $url = "api.coincap.io/v2/candles?exchange={$exchange}&interval=m5&baseId={$from_req}&quoteId={$to_req}&start={$start_req}&end={$end_req}";

        curl_setopt_array($this->coin_cap_candles_curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($this->coin_cap_candles_curl);
        $response = json_decode($response, true);
        $save_to_db = array();
        foreach ($response['data'] as $data){
            array_push($save_to_db, array(
                "Historical" => array(
                    $data['period'] / 1000,
                    $data['open'],
                    $data['high'],
                    $data['low'],
                    $data['close'],
                    $data['volume']
                )
            ));
        }

        $this->crypto_add_historical_internal($save_to_db, $exchange, $from, $to);
    }

    private function check_availability_of_asset_data($asset, $start, $end){
        $hist_avail = DB::select(DB::raw("
            select \"From\", \"To\", \"Exchange_id\", \"id\" from historical_available where \"From\" = '{$asset}';
        "));

        $availability = DB::select(DB::raw("
            SELECT \"Exchange_id\"
            From historical_available INNER JOIN crypto_historical on historical_available.id = crypto_historical.id
            WHERE \"From\" = '{$asset}' and \"Timestamp\" BETWEEN {$start} and {$end}
            GROUP BY \"Exchange_id\", \"From\", \"To\";

        "));

        $avail_exchanges = array();
        foreach ($availability as $avail){
            array_push($avail_exchanges, $avail->Exchange_id);
        }

        foreach ($hist_avail as $avail){
            if (!in_array($avail->Exchange_id, $avail_exchanges)){
                $this->coin_cap_candles($avail->Exchange_id, $avail->From, $avail->To, $start, $end);
            }
        }
    }

    public function crypto_asset_value(Request $request, $crypto_id, $convert_to_id, $start, $end, $range=null, $dry=null)
    {
        try {
            $this->check_coin($crypto_id);
            $convert_to_info = $this->check_coin($convert_to_id);

            if (($end - $start) <= 86400 ){ # TODO: remove, this is temporary
                $this->check_availability_of_asset_data($crypto_id, $start, $end);
            }
            if ($dry){
                return response()->json([
                    "data" => "Dry completed"
                ], 200);
            }

            if ($range){
                if (!key_exists($range, $this->time_range_config)){
                    throw new \Exception('Time range not supported');
                }
                $range = $this->time_range_config[$range];
            }
            else{
                $range = $end - $start;
            }

            if ($start + $range > $end){
                throw new \Exception('Range not between start and end');
            }
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        if ($convert_to_info->Type == 'fiat'){
            $result = $this->value_crypto_asset_fiat($range, $crypto_id, $convert_to_id, $start, $end);
        }
        else{
            $result = $this->value_crypto_asset_no_fiat($range, $crypto_id, $convert_to_id, $start, $end);
        }

        $values = array();
        foreach ($result as $data){
            array_push($values, array(
                strtotime($data->interval_alias),
                floatval($data->sum)
            ));
        }

        return response()->json([
            "data" => $values
        ], 200);
    }

    public function list_all_fiat(Request $request){
        $result = DB::select(DB::raw('Select "Fiat_id" from fiats'));
        $values = array();
        foreach ($result as $item){
            array_push($values, $item->Fiat_id);
        }
        return response()->json([
            "data"=> $values
        ], 200);
    }


    public function get_crypto_asset_info(Request $request, $crypto_id){
        try {
            $this->check_coin($crypto_id);
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        $crypto = DB::table('cryptocurrencies')->where('Crypto_id', $crypto_id)->first();
        $total_supply = null;
        $circulating_supply = null;
        if ($crypto){
            $coin_gecko_resp = $this->coin_gecko_client->coins()->getCoin($this->coins_config[$crypto_id]);
            if ($coin_gecko_resp){
                $total_supply = $coin_gecko_resp['market_data']['total_supply'];
                $circulating_supply = $coin_gecko_resp['market_data']['circulating_supply'];
                if ($crypto->CirculatingCap == -1){
                    DB::table('cryptocurrencies')->where('Crypto_id', $crypto_id)->update([
                        'CirculatingCap' => $total_supply ?: -1,
                        'CirculatingActual' => $circulating_supply
                        ]);
                }
            }
            else{
                $total_supply = $crypto->CirculatingCap;
                $circulating_supply = $crypto->CirculatingActual;
            }

            return response()->json([
                "data"=> array(
                    "name" => $crypto->Name,
                    "total_supply" => $total_supply,
                    "circulating_supply" => $circulating_supply
                )
            ], 200);
        }

        return response()->json([
            "data"=> "Internal server error"
        ], 501);

    }

    public function crypto_asset_all_time(Request $request, $crypto_id){
        try {
            $this->check_coin($crypto_id);
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        $fiat_value = $this->last_fiat_value('eur');
        if ($fiat_value){
            $min_max = $this->crypto_asset_min_max($crypto_id, $fiat_value[0]->Value_USD);
            if ($min_max){
                return response()->json([
                    "data" => array(
                        "min" => $min_max[0]->min,
                        "max" => $min_max[0]->max,
                        "currency" => "usd"
                    )
                ], 200);
            }
        }

        return response()->json([
            "message" => "Internal server error"
        ], 501);
    }

    public function exchange_stats(Request $request, $exchange_id){
        $to_coins = DB::select(DB::raw("
            SELECT DISTINCT \"To\" from historical_available where \"Exchange_id\" = '{$exchange_id}';
        "));

        $coin_values_usd = array();
        foreach ($to_coins as $t_c){
            $info = $this->check_coin($t_c->To);
            if ($info->Type == 'fiat'){
                $coin_value_usd = $this->last_fiat_value($t_c->To);
                $coin_values_usd[$t_c->To] = 1 / $coin_value_usd[0]->Value_USD;
            }
            else{
                $now =strtotime('now');
                $start = strtotime("today", $now);
                $end = strtotime("tomorrow", $start) - 1;
                $coin_value_usd = $this->value_crypto_asset_fiat($end - $start, $t_c->To, 'usd', $start, $end);
                $coin_values_usd[$t_c->To] = 1 / $coin_value_usd[0]->sum;
            }
        }

        $volume_by_currency = $this->exchange_volume_by_currency($exchange_id, $to_coins, $coin_values_usd);
        $volume_per_pair = $this->exchange_volume_pair($exchange_id, $coin_values_usd);
        $additional_info = $this->exchange_additional_info($exchange_id);


        return response()->json([
            "data" => array(
                "volume_by_currency" => $volume_by_currency,
                "volume_by_pair" => $volume_per_pair,
                "additional" => $additional_info[0]
            )
        ], 200);
    }

    private function exchange_volume_by_currency($exchange_id, $to_coins, $coin_values_usd){
        $values_in_usd = array();
        $sum_volume_usd = 0;
        foreach ($to_coins as $t_c){
            $volume_for_coin = DB::select(DB::raw("
                SELECT
                    SUM(\"Volume\") AS \"SUM\"
                FROM
                    historical_available
                    JOIN crypto_historical ON historical_available.id = crypto_historical.id
                WHERE
                    \"Exchange_id\" = '{$exchange_id}'
                    AND \"To\" = '{$t_c->To}';
            "));
            $values_in_usd[$t_c->To] = $coin_values_usd[$t_c->To] * $volume_for_coin[0]->SUM;
            $sum_volume_usd += $coin_values_usd[$t_c->To] * $volume_for_coin[0]->SUM;
        }

        $result = array();

        foreach ($values_in_usd as $key => $value){
            $result[$key] = $value * 100 / $sum_volume_usd;
        }

        return $result;
    }


    private function exchange_volume_pair($exchange_id, $coin_values_usd){
        $coin_pairs_value = DB::select(DB::raw("
            SELECT
                SUM(\"Volume\") AS \"SUM\",
                \"From\",
                \"To\"
            FROM
                historical_available
                JOIN crypto_historical ON historical_available.id = crypto_historical.id
            WHERE
                \"Exchange_id\" = '{$exchange_id}'
            GROUP BY
                \"From\",
                \"To\";
        "));

        $volume_sum = 0;
        $volumes_per_pair = array();
        foreach ($coin_pairs_value as $cpv){
            $volumes_per_pair["{$cpv->From}/{$cpv->To}"] = $cpv->SUM * $coin_values_usd[$cpv->To];
            $volume_sum += $cpv->SUM * $coin_values_usd[$cpv->To];
        }

        $result = array();
        foreach ($volumes_per_pair as $key => $vpp){
            $result[$key] = $vpp * 100 / $volume_sum;
        }

        return $result;
    }


    private function exchange_additional_info($exchange_id){
        return DB::select(DB::raw("
            SELECT
                *
            FROM
                exchanges
            WHERE
                \"Exchange_id\" = '{$exchange_id}';
        "));
    }

    private function crypto_asset_min_max($crypto_id, $fiat_value){
        return DB::select(DB::raw("
            WITH min_max AS (
            SELECT
                CASE WHEN \"To\" = 'eur' THEN
                    MIN((\"Open\" + \"High\" + \"Low\") / 3 * {$fiat_value})
                ELSE
                    MIN((\"Open\" + \"High\" + \"Low\") / 3)
                END AS \"MIN\",
                CASE WHEN \"To\" = 'eur' THEN
                    MAX((\"Open\" + \"High\" + \"Low\") / 3 * {$fiat_value})
                ELSE
                    MAX((\"Open\" + \"High\" + \"Low\") / 3)
                END AS \"MAX\"
            FROM
                crypto_historical
                INNER JOIN historical_available ON crypto_historical.id = historical_available.id
            WHERE
                \"From\" = '{$crypto_id}'
                AND \"To\" IN('usd','eur')
            GROUP BY
                \"To\"
        )
        SELECT
            MIN(\"MIN\"),
            MAX(\"MAX\")
        FROM
            min_max;
        "));
    }

    private function last_fiat_value($fiat_id){ # Could be for more values in future
        return DB::select(DB::raw("
            SELECT
                \"Value_USD\"
            FROM
                fiat_historicals
            WHERE
                \"Fiat_id\" IN ('{$fiat_id}')
            ORDER BY
                \"Date\" DESC
            LIMIT 1;

        "));
    }

    private function ohlc_fiat_time_range_query($range, $exchange, $historical_available, $to, int $start, int $end)
    {
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	        values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            )

            SELECT
	            (array_agg(\"ch\".\"Open\" / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\" ORDER BY \"ch\".\"Timestamp\" ASC)) [1] AS \"Open\",
	            MAX(\"ch\".\"High\" / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\" )  AS \"High\",
	            MIN(\"ch\".\"Low\" / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\" ) AS \"Low\",
	            (array_agg(\"ch\".\"Close\" / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\" ORDER BY \"Timestamp\" DESC)) [1] AS \"Close\",
	            to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + offset_val) AT TIME ZONE 'UTC' AS 	\"interval_alias\"
            FROM
                offset_value,
	            \"historical_available\" AS \"ha\"
	            JOIN \"crypto_historical\" AS \"ch\" ON \"ha\".\"id\" = \"ch\".\"id\"
	            JOIN \"fiat_historicals\" AS \"fh1\" ON \"ha\".\"To\" = \"fh1\".\"Fiat_id\"
	            JOIN \"fiat_historicals\" AS \"fh2\" ON '{$to}' = \"fh2\".\"Fiat_id\"
            WHERE
	            \"ha\".\"Exchange_id\" = '{$exchange}'
                AND \"ha\".\"From\" = '{$historical_available->From}'
                AND \"ha\".\"To\" = '{$historical_available->To}'
                AND \"ch\".\"Timestamp\" BETWEEN '{$start}' AND '{$end}'
                AND \"fh1\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
                AND \"ch\".\"Timestamp\"
                AND \"fh2\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
                AND \"ch\".\"Timestamp\"
            GROUP BY
	            \"interval_alias\"
	        ORDER BY
	            \"interval_alias\"
        "));
    }

    private function ohlc_no_fiat_time_range_query($range, $exchange, $historical_available, $to, int $start, int $end)
    {
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	        values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            )

            SELECT
	            (array_agg(\"ch\".\"Open\" ORDER BY \"ch\".\"Timestamp\" ASC)) [1] AS \"Open\",
	            MAX(\"ch\".\"High\")  AS \"High\",
	            MIN(\"ch\".\"Low\") AS \"Low\",
	            (array_agg(\"ch\".\"Close\" ORDER BY \"Timestamp\" DESC)) [1] AS \"Close\",
	            to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + \"ov\".offset_val) AT TIME ZONE 'UTC' AS 	\"interval_alias\"
            FROM
                \"offset_value\" AS \"ov\",
	            \"historical_available\" AS \"ha\"
	            JOIN \"crypto_historical\" AS \"ch\" ON \"ha\".\"id\" = \"ch\".\"id\"
            WHERE
	            \"ha\".\"Exchange_id\" = '{$exchange}'
                AND \"ha\".\"From\" = '{$historical_available->From}'
                AND \"ha\".\"To\" = '{$historical_available->To}'
                AND \"ch\".\"Timestamp\" BETWEEN '{$start}' AND '{$end}'
            GROUP BY
	            \"interval_alias\"
	        ORDER BY
	            \"interval_alias\"
        "));

    }

    private function value_fiat_time_range_query($range, $exchange, $historical_available, $to, int $start, int $end)
    {
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	        values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            )

            SELECT
                AVG((\"Open\" + \"Close\" + \"High\") / 3 / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\" ) AS \"value\",
	            to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + offset_val) AT TIME ZONE 'UTC' AS \"interval_alias\"
            FROM
                offset_value,
	            \"crypto_historical\" AS \"ch\"
	                JOIN \"historical_available\" AS \"ha\" ON \"ch\".\"id\" = \"ha\".\"id\"
	                JOIN \"fiat_historicals\" AS \"fh1\" ON \"ha\".\"To\" = \"fh1\".\"Fiat_id\"
	                JOIN \"fiat_historicals\" AS \"fh2\" ON '{$to}' = \"fh2\".\"Fiat_id\"
            WHERE
	            \"ha\".\"Exchange_id\" = '{$exchange}'
                AND \"ha\".\"From\" = '{$historical_available->From}'
                AND \"ha\".\"To\" = '{$historical_available->To}'
                AND \"ch\".\"Timestamp\" BETWEEN {$start} AND {$end}
                AND \"fh1\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
                AND \"ch\".\"Timestamp\"
                AND \"fh2\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
                AND \"ch\".\"Timestamp\"
            GROUP BY
	            \"interval_alias\"
	        ORDER BY
	            \"interval_alias\""
        ));
    }

    private function value_no_fiat_time_range_query($range, $exchange, $historical_available, $to, int $start, int $end)
    {
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	        values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            )
            SELECT
                AVG((\"Open\" + \"Close\" + \"High\") / 3) AS \"value\",
	            to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + \"ov\".offset_val) AT TIME ZONE 'UTC' AS \"interval_alias\"
            FROM
                \"offset_value\" AS \"ov\",
	            \"crypto_historical\" AS \"ch\"
	                JOIN \"historical_available\" AS \"ha\" ON \"ch\".\"id\" = \"ha\".\"id\"
            WHERE
	            \"ha\".\"Exchange_id\" = '{$exchange}'
                AND \"ha\".\"From\" = '{$historical_available->From}'
                AND \"ha\".\"To\" = '{$historical_available->To}'
                AND \"ch\".\"Timestamp\" BETWEEN {$start} AND {$end}
            GROUP BY
	            \"interval_alias\"
	        ORDER BY
	            \"interval_alias\""
        ));
    }

    private function volume_fiat_time_range_query($range, $exchange, $historical_available, $to, int $start, int $end)
    {
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	        values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            )
            SELECT
                AVG(\"Volume\" / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\") AS \"Volume\",
	            to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + offset_val) AT TIME ZONE 'UTC' AS \"interval_alias\"
            FROM
                offset_value,
	            \"crypto_historical\" AS \"ch\"
	                JOIN \"historical_available\" AS \"ha\" ON \"ch\".\"id\" = \"ha\".\"id\"
	                JOIN \"fiat_historicals\" AS \"fh1\" ON \"ha\".\"To\" = \"fh1\".\"Fiat_id\"
	                JOIN \"fiat_historicals\" AS \"fh2\" ON '{$to}' = \"fh2\".\"Fiat_id\"
            WHERE
	            \"ha\".\"Exchange_id\" = '{$exchange}'
                AND \"ha\".\"From\" = '{$historical_available->From}'
                AND \"ha\".\"To\" = '{$historical_available->To}'
                AND \"ch\".\"Timestamp\" BETWEEN {$start} AND {$end}
                AND \"fh1\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
                AND \"ch\".\"Timestamp\"
                AND \"fh2\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
                AND \"ch\".\"Timestamp\"
            GROUP BY
	            \"interval_alias\"
	        ORDER BY
	            \"interval_alias\""
        ));

    }

    private function volume_no_fiat_time_range_query($range, $exchange, $historical_available, $to, int $start, int $end)
    {
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	        values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            )
            SELECT
                AVG(\"Volume\") AS \"Volume\",
	            to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + offset_val) AT TIME ZONE 'UTC' AS \"interval_alias\"
            FROM
                offset_value,
	            \"crypto_historical\" AS \"ch\"
	            JOIN \"historical_available\" AS \"ha\" ON \"ch\".\"id\" = \"ha\".\"id\"
            WHERE
	            \"ha\".\"Exchange_id\" = '{$exchange}'
                AND \"ha\".\"From\" = '{$historical_available->From}'
                AND \"ha\".\"To\" = '{$historical_available->To}'
                AND \"ch\".\"Timestamp\" BETWEEN {$start} AND {$end}
            GROUP BY
	            \"interval_alias\"
	        ORDER BY
	            \"interval_alias\""
        ));

    }

    private function value_crypto_asset_fiat($range, $crypto_id, $convert_to, int $start, int $end)
    {
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	            values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            ),
            main_table AS (
	            SELECT
		        SUM(\"ch\".\"Volume\" / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\") AS \"Volume_Exchange\",
		        AVG((\"ch\".\"Open\" + \"ch\".\"Close\" + \"ch\".\"High\") / 3 / \"fh1\".\"Value_USD\" * \"fh2\".\"Value_USD\") AS \"Price\",
		        to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + \"ov\".offset_val) AT TIME ZONE 'UTC' AS \"interval_alias\"
	        FROM
	            \"offset_value\" AS \"ov\",
		        \"crypto_historical\" AS \"ch\"
		        JOIN \"historical_available\" AS \"ha\" ON \"ch\".\"id\" = \"ha\".\"id\"
                JOIN \"fiat_historicals\" AS \"fh1\" ON \"ha\".\"To\" = \"fh1\".\"Fiat_id\"
                JOIN \"fiat_historicals\" AS \"fh2\" ON '{$convert_to}' = \"fh2\".\"Fiat_id\"
	        WHERE
                \"ha\".\"From\" = '{$crypto_id}'
                AND \"ha\".\"To\" IN('usd','eur')
                AND \"ch\".\"Timestamp\" BETWEEN '{$start}'
                AND '{$end}' - 1
                AND \"fh1\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
                AND \"ch\".\"Timestamp\"
                AND \"fh2\".\"Date\" BETWEEN \"ch\".\"Timestamp\" - 86400
		        AND \"ch\".\"Timestamp\"
	        GROUP BY
                \"interval_alias\",
                \"Exchange_id\"
        ),
        sum_table AS (
	        SELECT
                \"interval_alias\",
                SUM(\"Volume_Exchange\") AS \"Volume_Sum\"
	        FROM
		        main_table
            GROUP BY
                \"interval_alias\"
        )
        SELECT
            SUM(\"Volume_Exchange\" / \"Volume_Sum\" * \"Price\"),
            \"st\".\"interval_alias\"
        FROM
            main_table AS \"mt\",
            sum_table AS \"st\"
        WHERE
            \"mt\".\"interval_alias\" = \"st\".\"interval_alias\" AND
            \"Volume_Sum\" != 0
        GROUP BY
            \"st\".\"interval_alias\"
        ORDER BY \"st\".\"interval_alias\";

        "));

    }

    private function value_crypto_asset_no_fiat($range, $crypto_id, $convert_to, int $start, int $end){
        return DB::select(DB::raw("
            WITH offset_value(offset_val) as (
	            values('{$start}' - floor((extract('epoch' FROM to_timestamp('{$start}')) / {$range})) * {$range})
            ),
            main_table AS (
                SELECT
                    SUM(\"ch\".\"Volume\") AS \"Volume_Exchange\",
                    AVG((\"ch\".\"Open\" + \"ch\".\"Close\" + \"ch\".\"High\") / 3) AS \"Price\",
                    to_timestamp(floor((extract('epoch' FROM to_timestamp(\"ch\".\"Timestamp\")) / {$range})) * {$range} + \"ov\".\"offset_val\") AT TIME ZONE 'UTC' AS \"interval_alias\"
                FROM
                    \"offset_value\" AS \"ov\",
                    \"crypto_historical\" AS \"ch\"
                    JOIN \"historical_available\" AS \"ha\" ON \"ch\".\"id\" = \"ha\".\"id\"
                WHERE
                    \"ha\".\"From\" = '{$crypto_id}'
                    AND \"ha\".\"To\" = '{$convert_to}'
                    AND \"ch\".\"Timestamp\" BETWEEN '{$start}'
                    AND '{$end}' - 1
                GROUP BY
                    \"interval_alias\",
                    \"Exchange_id\"
                ),
            sum_table AS (
                    SELECT
                        \"interval_alias\",
                        SUM(\"Volume_Exchange\") AS \"Volume_Sum\"
                    FROM
                        main_table
                    GROUP BY
                        \"interval_alias\"
                )
                SELECT
                    SUM(\"Volume_Exchange\" / \"Volume_Sum\" * \"Price\"),
                    \"st\".\"interval_alias\"
                FROM
                    main_table AS \"mt\",
                    sum_table AS \"st\"
                WHERE
                    \"mt\".\"interval_alias\" = \"st\".\"interval_alias\" AND
                    \"Volume_Sum\" != 0
                GROUP BY
                    \"st\".\"interval_alias\"
                ORDER BY \"st\".\"interval_alias\";

        "));
    }
}
