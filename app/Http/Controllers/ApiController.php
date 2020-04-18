<?php

namespace App\Http\Controllers;
require __DIR__ . "/../../Modules/Stats.php";

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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
    }

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

    private function add_crypto_historical_five($data, $exchange_id){
        $historical = new CryptoHistorical;
        $historical->id = $data['id'];
        $historical->Timestamp = $data['timestamp'];
        $historical->Open = $data['historical'][1];
        $historical->High = $data['historical'][2];
        $historical->Low = $data['historical'][3];
        $historical->Close = $data['historical'][4];
        $historical->Volume = $data['historical'][5];

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"add_crypto_historical_five", "exchange_id"=>"{$exchange_id}"));

        $historical->save();
    }

    public function crypto_add_historical(Request $request) {
        print_r("Exchange_id: ");
        print_r($request['Exchange_id']);
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

    private function check_fiat($fiat){
        $fiat = DB::table('fiats')->where('Fiat_id', $fiat)->first();
        if (!$fiat){
            throw new \Exception('Fiat is not supported');
        }

        return $fiat;
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

    private function get_fiat_values_for_cached($hist_avail, $fiat_to_id, $fiat_prev_id, $fiat_actual, $fiat_prev, $start, $range){
        if ($hist_avail->To != $fiat_to_id){
            if ($fiat_actual) {
                if (!($fiat_actual->Date >= $start && $fiat_actual->Date <= $start + $range)) {
                    $fiat_actual = $this->get_fiat_historical($fiat_to_id, $start);
                    $fiat_prev = $this->get_fiat_historical($fiat_prev_id, $start);
                }
            } else {
                $fiat_actual = $this->get_fiat_historical($fiat_to_id, $start);
                $fiat_prev = $this->get_fiat_historical($fiat_prev_id, $start);
            }
        }
        else{
            if (!$fiat_prev or !($fiat_prev->Date >= $start && $fiat_prev->Date <= $start + $range)){
                $fiat_actual = $this->get_fiat_historical($fiat_to_id, $start);;
                $fiat_prev = $this->get_fiat_historical($fiat_prev_id, $start);
            }
        }
        return array($fiat_actual, $fiat_prev);
    }

    private function do_get_value_time_range($start, $end, $exchange, $range, $from, $to, $historical_available){
        $start = $start - ($start % $range); // Allign to right offset
        $end = $end - ($end % $end); // Allign to right offset

        $values = array();
        $fiat_actual = null;
        $fiat_prev  =  null;
        $fiat_prev_key = "value_fiat_prev_{$range}_{$exchange}_{$from}";
        $fiat_prev_id = Redis::get($fiat_prev_key);

        $fiat_db = null;
        $fiat_to = null;

        while ($start + $range <= $end){
            $x_value = $start;
            $redis_key = "crypto_value_{$x_value}_{$range}_{$exchange}_{$from}";

            $result = Redis::get($redis_key);
            if ($result){
                $fiat_data = $this->get_fiat_values_for_cached($historical_available, $to, $fiat_prev_id, $fiat_actual, $fiat_prev, $start, $range);
                $fiat_actual = $fiat_data[0];
                $fiat_prev = $fiat_data[1];



                if ($fiat_prev){
                    $value1 = $fiat_prev->Value_USD;
                }
                else{
                    $value1 = 1;
                }

                if ($fiat_actual){
                    $value2 = $fiat_actual->Value_USD;
                }
                else{
                    $value2 = 1;
                }

                array_push($values, array(
                    $start,
                    $result / $value1 * $value2
                ));

                Redis::set($redis_key, $result / $value1 * $value2);

                $start += $range;
                continue;
            }

            $result = DB::table('historical_available')
                ->select(DB::raw('AVG(("Open"+"Close")/2) as value'))
                ->join('crypto_historical', 'historical_available.id', '=', 'crypto_historical.id')
                ->where([
                    ['Exchange_id', '=', DB::raw("'{$exchange}'")],
                    ['From', '=', DB::raw("'{$historical_available->From}'")],
                    ['To', '=', DB::raw("'{$historical_available->To}'")]
                ])
                ->whereBetween('Timestamp', [$start, $start + $range])
                ->groupBy(['Exchange_id'])->get();

            $result = json_decode($result, true);

            if ($fiat_db == null){
                $fiat_db = $this->get_fiat_historical($historical_available->To, $start);
                $fiat_to = $this->get_fiat_historical($to, $start);
            }
            elseif (!($fiat_db->Date >= $start and $fiat_db->Date <= $start + $range - 1)){
                $fiat_db = $this->get_fiat_historical($historical_available->To, $start);
                $fiat_to = $this->get_fiat_historical($to, $start);
            }

            foreach ($result as $res){
                Redis::set($redis_key, $res['value'] / $fiat_db->Value_USD * $fiat_to->Value_USD);

                array_push($values, array(
                    $start,
                    $res['value'] / $fiat_db->Value_USD * $fiat_to->Value_USD
                ));
            }
            $start += $range;
        }

        if (!empty($values)){
            Redis::set($fiat_prev_key, $to);
        }

        return $values;
    }


    private function do_get_value_time_range_v2($start, $end, $exchange, $range, $from, $to, $historical_available){
        $start = $start - ($start % $range); // Allign to right offset
        $end = $end - ($end % $end); // Allign to right offset

        $values = array();

        $result = DB::table('historical_available')
            ->select(DB::raw('AVG(("Open"+"Close")/2 / "fh1"."Value_USD" * "fh2"."Value_USD") as value,
            to_timestamp(floor((extract(\'epoch\' from to_timestamp("Timestamp")) / 3600 )) * 3600)
            AT TIME ZONE \'UTC\' as "start_date"'))
            ->join(DB::raw('"fiat_historicals" AS "fh1"'), 'historical_available.To', '=', 'fh1.Fiat_id')
            ->join(DB::raw('"fiat_historicals" AS "fh2"'), DB::raw("'{$to}'"), '=', 'fh2.Fiat_id')
            ->join('crypto_historical', 'historical_available.id', '=', 'crypto_historical.id')
            ->where([
                ['Exchange_id', '=', DB::raw("'{$exchange}'")],
                ['From', '=', DB::raw("'{$historical_available->From}'")],
                ['To', '=', DB::raw("'{$historical_available->To}'")]
            ])
            ->whereBetween('Timestamp', [$start, $end - 1])
            ->whereBetween('fh1.Date', [DB::raw('"Timestamp" - 86400'), DB::raw('"Timestamp"')])
            ->whereBetween('fh2.Date', [DB::raw('"Timestamp" - 86400'), DB::raw('"Timestamp"')])
            ->groupBy('start_date')->get();

        return response()->json([
            "data" => $result
        ], 200);
//        foreach ($result->data as $data){
//            array_push($values, array(
//               $data['value'],
//                $data['start_date']
//            ));
//        }
    }

    public function get_crypto_value_timestamp(Request $request, $timestamp, $exchange, $from, $to, $init){
        $historical_available = null;

        try {
            $this->check_exchange($exchange);
            $this->check_fiat($to);

            $historical_available = $this->get_historical_available($exchange, $from);

        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        if($init == "true"){
            $result = $this->do_get_value_time_range($timestamp - 1080, $timestamp + 60, $exchange, 60, $from, $to, $historical_available);
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

            $this->check_fiat($to);

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

        $values = $this->do_get_value_time_range($start, $end, $exchange, $range, $from, $to, $historical_available);

        return response()->json([
            "data" => $values
        ], 200);


    }

    public function get_crypto_value_time_range_v2(Request $request, $start, $end, $exchange, $range, $from, $to){
        $historical_available = null;

        try {
            $this->check_exchange($exchange);

            $this->check_fiat($to);

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

        $values = $this->do_get_value_time_range_v2($start, $end, $exchange, $range, $from, $to, $historical_available);

        return response()->json([
            "data" => $values
        ], 200);


    }

    public function get_crypto_ohlc_time_range(Request $request, $start, $end, $exchange, $range, $from, $to){
        $historical_available = null;

        try {
            $this->check_exchange($exchange);

            $this->check_fiat($to);

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

        $start = $start - ($start % $range); // Allign to right offset
        $end = $end - ($end % $end); // Allign to right offset

        $ohlc_chart = array();
        $fiat_actual = null;
        $fiat_prev  =  null;
        $fiat_prev_key = "ohlc_fiat_prev_{$range}_{$exchange}_{$from}";
        $fiat_prev_id = Redis::get($fiat_prev_key);

        $fiat_db = null;
        $fiat_to = null;
        while ($start + $range <= $end){
            $x_value = $start;
            $redis_key_value = "ohlc_{$x_value}_{$range}_{$exchange}_{$from}";
            $result = Redis::hgetall($redis_key_value);
            if ($result){
                $fiat_data = $this->get_fiat_values_for_cached($historical_available, $to, $fiat_prev_id, $fiat_actual, $fiat_prev, $start, $range);
                $fiat_actual = $fiat_data[0];
                $fiat_prev = $fiat_data[1];

                if ($fiat_prev){
                    $value1 = $fiat_prev->Value_USD;
                }
                else{
                    $value1 = 1;
                }

                if ($fiat_actual){
                    $value2 = $fiat_actual->Value_USD;
                }
                else{
                    $value2 = 1;
                }

                array_push($ohlc_chart, array(
                    "x" => $start,
                    "y" => array(
                        $result[0] / $value1 * $value2,
                        $result[1] / $value1 * $value2,
                        $result[2] / $value1 * $value2,
                        $result[3] / $value1 * $value2,
                    )
                ));

                Redis::hmset($redis_key_value, array(
                    $result[0] / $value1 * $value2,
                    $result[1] / $value1 * $value2,
                    $result[2] / $value1 * $value2,
                    $result[3] / $value1 * $value2,
                ));

                $start += $range;
                continue;
        }

            $result = DB::table('historical_available')
                ->select(DB::raw('(array_agg("Open" ORDER BY "Timestamp" ASC))[1] as "Open", MAX("High") as "High", MIN("Low") as "Low", (array_agg("Close" ORDER BY "Timestamp" DESC))[1] as "Close"'))
                ->join('crypto_historical', 'historical_available.id', '=', 'crypto_historical.id')
                ->where([
                    ['Exchange_id', '=', DB::raw("'{$exchange}'")],
                    ['From', '=', DB::raw("'{$historical_available->From}'")],
                    ['To', '=', DB::raw("'{$historical_available->To}'")]
                ])
                ->whereBetween('Timestamp', [$start, $start + $range])
                ->get();

            $result = json_decode($result, true);

            if ($fiat_db == null){
                $fiat_db = $this->get_fiat_historical($historical_available->To, $start);
                $fiat_to = $this->get_fiat_historical($to, $start);
            }
            elseif (!($fiat_db->Date >= $start and $fiat_db->Date <= $start + $range - 1)){
                $fiat_db = $this->get_fiat_historical($historical_available->To, $start);
                $fiat_to = $this->get_fiat_historical($to, $start);
            }

            foreach ($result as $res){
                $y_value = array(
                    $res["Open"] / $fiat_db->Value_USD *  $fiat_to->Value_USD,
                    $res["High"] / $fiat_db->Value_USD *  $fiat_to->Value_USD,
                    $res["Low"]  / $fiat_db->Value_USD *  $fiat_to->Value_USD,
                    $res["Close"] / $fiat_db->Value_USD *  $fiat_to->Value_USD
                );

                Redis::hmset($redis_key_value, $y_value);

                array_push($ohlc_chart, array(
                    "x" => $start,
                    "y" => $y_value
                ));
            }

                $start += $range;
        }

        if (!empty($ohlc_chart)){
            Redis::set($fiat_prev_key, $to);
        }

        return response()->json([
            "data" => $ohlc_chart
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

            $this->check_fiat($to);

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

        $start = $start - ($start % $range); // Allign to right offset
        $end = $end - ($end % $end); // Allign to right offset

        $volume_data = array();
        $fiat_actual = null;
        $fiat_prev  =  null;
        $fiat_prev_key = "volume_fiat_prev_{$range}_{$exchange}_{$from}";
        $fiat_prev_id = Redis::get($fiat_prev_key);

        $fiat_db = null;
        $fiat_to = null;

        while ($start + $range <= $end){
            $x_value = $start + $range;
            $redis_key_value = "volume_{$x_value}_{$range}_{$exchange}_{$from}";
            $result = Redis::get($redis_key_value);
            if ($result){
                $fiat_data = $this->get_fiat_values_for_cached($historical_available, $to, $fiat_prev_id, $fiat_actual, $fiat_prev, $start, $range);
                $fiat_actual = $fiat_data[0];
                $fiat_prev = $fiat_data[1];

                if ($fiat_prev){
                    $value1 = $fiat_prev->Value_USD;
                }
                else{
                    $value1 = 1;
                }

                if ($fiat_actual){
                    $value2 = $fiat_actual->Value_USD;
                }
                else{
                    $value2 = 1;
                }

                array_push($volume_data, array(
                    "x" => $start,
                    "y" => $result / $value1 * $value2
                ));

                Redis::set($redis_key_value, $result / $value1 * $value2);

                $start += $range;
                continue;
            }

            $result = DB::table('historical_available')
                ->select(DB::raw('AVG("Volume") as "Volume"'))
                ->join('crypto_historical', 'historical_available.id', '=', 'crypto_historical.id')
                ->where([
                    ['Exchange_id', '=', DB::raw("'{$exchange}'")],
                    ['From', '=', DB::raw("'{$historical_available->From}'")],
                    ['To', '=', DB::raw("'{$historical_available->To}'")]
                ])
                ->whereBetween('Timestamp', [$start, $start + $range - 1])
                ->get();

            $result = json_decode($result, true);

            if ($fiat_db == null){
                $fiat_db = $this->get_fiat_historical($historical_available->To, $start);
                $fiat_to = $this->get_fiat_historical($to, $start);
            }
            elseif (!($fiat_db->Date >= $start and $fiat_db->Date <= $start + $range - 1)){
                $fiat_db = $this->get_fiat_historical($historical_available->To, $start);
                $fiat_to = $this->get_fiat_historical($to, $start);
            }

            foreach ($result as $res){
                $y_value = $res['Volume'] / $fiat_db->Value_USD * $fiat_to->Value_USD;

                Redis::set($redis_key_value, $y_value);

                array_push($volume_data, array(
                    "x" => $start + $range,
                    "y" => $y_value
                ));
            }

            $start += $range;
        }

        if (!empty($volume_data)){
            Redis::set($fiat_prev_key, $to);
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
}
