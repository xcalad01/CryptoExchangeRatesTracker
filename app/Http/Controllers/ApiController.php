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


class ApiController extends Controller
{
    private $statsd;

    private $time_range_config;

    private $coin_cap_coins_config;

    private $coin_cap_fiat_config;

    private $coin_cap_candles_curl;

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

        $this->coin_cap_coins_config = array(
            "btc" => "bitcoin",
            "usd" => "united-states-dollar",
            "eur" => "euro",
            "usdt" => "tether"
        );

        $this->coin_cap_candles_curl = curl_init();
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
        $exchange = new Exchange;
        $exchange->Exchange_id = $request['Exchange_id'];
        $exchange->Name = $request['Name'];
        $exchange->Url = $request['Url'];
        $exchange->Image = $request['Image'];


        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_exchange"));
        try {
            $exchange->save();
            return response()->json([
                "message" => "Exchange record created"
            ], 200);
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

//    public function fix_fiat_timestamp(Request $request){
//        $timestamps = $request["Timestamps"];
//        foreach ($timestamps as $t){
//            $fiats = Fiat_historical::where("Date", $t)->get();
//            foreach ($fiats as $f) {
//                if ($f->Fiat_id == 'usd'){
//                    continue;
//                }
//                $fiat_hist1 = new Fiat_historical();
//                $fiat_hist2 = new Fiat_historical();
//
//                $fiat_hist1->Fiat_id = $f->Fiat_id;
//                $fiat_hist2->Fiat_id = $f->Fiat_id;
//
//                $fiat_hist1->Value_USD = $f->Value_USD;
//                $fiat_hist2->Value_USD = $f->Value_USD;
//
//
//                $fiat_hist1->Date = $f->Date + 86400;
//                $fiat_hist2->Date = $f->Date + (86400*2);
//
//                $fiat_hist1->save();
//                $fiat_hist2->save();
//            }
//        }
//    }

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

    private function do_get_value_time_range($start, $end, $exchange, $range, $from, $to, $historical_available, $coin_info){
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
        return $values;
    }


    public function get_crypto_value_timestamp(Request $request, $timestamp, $exchange, $from, $to, $init){
        $historical_available = null;

        try {
            $this->check_exchange($exchange);
            $coin_info = $this->check_coin($to);

            $historical_available = $this->get_historical_available($exchange, $from);

        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        if($init == "true"){
            $result = $this->do_get_value_time_range($timestamp - 1080, $timestamp + 60, $exchange, 60, $from, $to, $historical_available, $coin_info);
            return response()->json([
                "data" => $result
            ], 200);
        }
        else{
            $result = DB::table('historical_available')
                ->select(DB::raw('("Open"+"Close")/2 as value'))
                ->join('crypto_historical', 'historical_available.id', '=', 'crypto_historical.id')
                ->where([
                    ['Exchange_id', '=', DB::raw("'{$exchange}'")],
                    ['From', '=', DB::raw("'{$historical_available->From}'")],
                    ['To', '=', DB::raw("'{$historical_available->To}'")],
                    ['Timestamp', '=', DB::raw("'{$timestamp}'")]
                ])
                ->groupBy(['Exchange_id', 'Open', 'Close'])->get();

            $result = json_decode($result, true);

            $fiat_db = $this->get_fiat_historical($historical_available->To, $timestamp);
            $fiat_to = $this->get_fiat_historical($to, $timestamp);

            if (!empty($result)){
                $result = $result[0]['value'] / $fiat_db->Value_USD * $fiat_to->Value_USD;
            }
            else{
                $result = null;
            }
            return response()->json([
                "data" => $result
            ], 200);
        }
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


        $values = $this->do_get_value_time_range($start, $end, $exchange, $range, $from, $to, $historical_available, $coin_info);

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

        $results = DB::table('historical_available')->where('Exchange_id', $exchange)->get();

        $from_data = array();
        $to_data = array();
        $already_in = array();
        foreach ($results as $result){
            array_push($from_data, array(
               'value'=>$result->From,
                'text'=>strtoupper($result->From)
            ));

            $item = array(
                'value'=>$result->To,
                'text'=>strtoupper($result->To)
            );

            if (!(in_array($item, $to_data))){
                array_push($already_in, $result->To);
                array_push($to_data, $item);
            }
        }

        $fiats = DB::table('fiats')->get("Fiat_id");
        foreach ($fiats as $fiat){
            if (!(in_array($fiat->Fiat_id, $already_in))){
                array_push($to_data, array(
                    'value'=>$fiat->Fiat_id,
                    'text'=>strtoupper($fiat->Fiat_id)
                ));
            }
        }


        return response()->json([
            "from" => $from_data,
            "to"=>$to_data
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
        $from_req = $this->coin_cap_coins_config[$from];
        $to_req = $this->coin_cap_coins_config[$to];

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

    public function crypto_asset_value(Request $request, $crypto_id, $convert_to_id, $start, $end, $range=null)
    {
        try {
            $this->check_availability_of_asset_data($crypto_id, $start, $end);
            $this->check_coin($crypto_id);
            $convert_to_info = $this->check_coin($convert_to_id);

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
            \"mt\".\"interval_alias\" = \"st\".\"interval_alias\"
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
                    \"mt\".\"interval_alias\" = \"st\".\"interval_alias\"
                GROUP BY
                    \"st\".\"interval_alias\"
                ORDER BY \"st\".\"interval_alias\";

        "));
    }
}
