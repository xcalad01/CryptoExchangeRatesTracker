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

    public function __construct()
    {
        $this->statsd = new Stats();
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

    private function add_crypto_historical_five($data){
        $historical = new CryptoHistorical;
        $historical->id = $data['id'];
        $historical->Timestamp = $data['timestamp'];
        $historical->Open = $data['historical'][1];
        $historical->High = $data['historical'][2];
        $historical->Low = $data['historical'][3];
        $historical->Close = $data['historical'][4];
        $historical->Volume = $data['historical'][5];

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"add_crypto_historical_five"));

        $historical->save();
    }

    public function crypto_add_historical(Request $request) {
        $exchange_id = $request['Exchange_id'];
        $available = Historical_available::where('Exchange_id', $exchange_id)->first();
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

        $available = Historical_available::where('Exchange_id', $exchange_id)->first();
        $data = array(
            "id"=>$available->id,
            'timestamp'=>$request['Timestamp'],
            "historical"=>$request['Historical']
        );

        try {
            self::add_crypto_historical_five($data);
        }
        catch (QueryException $e){
            return response()->json([
                "message" => $e->getMessage()
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

    private function get_timestamp($date){
        return strtotime($date . '16:05:00');
    }

    private function insert_fiat($item){
        $fiat = Fiat::where("Fiat_id", $item['Id'])->first();
        if (!$fiat){
            $fiat = new Fiat;
            $fiat->Fiat_id = $item["Id"];
            $fiat->Name = $item["Name"];
        }
        $fiat_hist = new Fiat_historical();
        $fiat_hist->Fiat_id = $fiat->Fiat_id;
        $fiat_hist->Value_USD = $item['Value'];
        $fiat_hist->Date = $this->get_timestamp($item["Key"]);


        $fiat->save();
        $fiat_hist->save();

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_update_fiat"));
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

    public function get_crypto_value_time_range(Request $request, $start, $end, $exchange, $convert_to = null){
        try {
            $this->check_exchange($exchange);
            if ($convert_to){
                $this->check_fiat($convert_to);
            }
            else{
                $convert_to = "usd";
            }
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        $result = DB::table('historical_available')
            ->select('Exchange_id', DB::raw('AVG(("Open"+"Close")/2*"Value_USD")'), DB::raw('"Fiat_id" as "Converted to"'))
            ->join('crypto_historical', 'historical_available.id', '=', 'crypto_historical.id')
            ->join('fiat_historicals', 'Fiat_id', '=', DB::raw("'{$convert_to}'"))
            ->where([
                ['Exchange_id', '=', DB::raw("'{$exchange}'")]
            ])
            ->whereBetween('Timestamp', [$start, $end])
            ->whereBetween('Timestamp', [ DB::raw('"Date"'), DB::raw('"Date" + 86399')])
            ->groupBy(['Exchange_id', 'Fiat_id'])->get();


        return response()->json([
            "message" => $result
        ], 200);


    }

    public function get_crypto_ohlc_time_range(Request $request, $start, $end, $exchange, $convert_to = null){
        try {
            $this->check_exchange($exchange);
            if ($convert_to){
                $this->check_fiat($convert_to);
            }
            else{
                $convert_to = "usd";
            }
        }
        catch (\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        }

        $result = DB::table('historical_available')
            ->select('Exchange_id', DB::raw('"Open"*"Value_USD" as "Open"'), DB::raw('"High"*"Value_USD" as "High"'), DB::raw('"Low"*"Value_USD" as "Low"'), DB::raw('"Close"*"Value_USD" as "Close"'), DB::raw('"Volume"*"Value_USD" as "Volume"'), DB::raw('"Fiat_id" as "Converted to"'))
            ->join('crypto_historical', 'historical_available.id', '=', 'crypto_historical.id')
            ->join('fiat_historicals', 'Fiat_id', '=', DB::raw("'{$convert_to}'"))
            ->where([
                ['Exchange_id', '=', DB::raw("'{$exchange}'")]
            ])
            ->whereBetween('Timestamp', [$start, $end])
            ->whereBetween('Timestamp', [ DB::raw('"Date"'), DB::raw('"Date" + 86399')])->get();

        return response()->json([
            "message" => $result
        ], 200);

    }


}
