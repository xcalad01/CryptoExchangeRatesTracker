<?php

namespace App\Http\Controllers;
require __DIR__ . "/../../Stats.php";

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Exchange;
use App\Historical_available;
use App\Historical_five_min;
use App\Cryptocurrencies;
use App\Crypto_exchange_pair;
use App\Crypto_fiat_exchange_pair;
use App\Fiat;
use Stats\Stats;


class ApiController extends Controller
{
    private $statsd;

    public function __construct()
    {
        $this->statsd = new Stats();
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

        try {
            $available->save();
            return true;
        }
        catch (QueryException $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 501);
        }
    }

    private function add_crypto_historical_five($data){
        $historical = new Historical_five_min;
        $historical->id = $data['id'];
        $historical->Timestamp = $data['timestamp'];
        $historical->Open = $data['historical'][1];
        $historical->High = $data['historical'][2];
        $historical->Low = $data['historical'][3];
        $historical->Close = $data['historical'][4];
        $historical->Volume = $data['historical'][5];

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"add_crypto_historical_five"));

        try {
            $historical->save();
            return true;
        }
        catch (QueryException $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 501);
        }
    }

    public function crypto_add_historical_five(Request $request) {
        $exchange_id = $request['Exchange_id'];
        $available = Historical_available::where('Exchange_id', $exchange_id)->first();
        if (!$available) {
            $data = array(
                "from" => $request['From'],
                "to" => $request['To'],
                "exchange_id" => $request['Exchange_id']
            );

            self::create_available($data);
        }

        $available = Historical_available::where('Exchange_id', $exchange_id)->first();
        $data = array(
            "id"=>$available->id,
            'timestamp'=>$request['Timestamp'],
            "historical"=>$request['Historical']
        );

        self::add_crypto_historical_five($data);


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
	            echo "crypto", "\n";
                $pair = Crypto_exchange_pair::where(['Exchange_id' => $item['Exchange'], 'From' => $item['From'], 'To' => $item['To']])->first();
            }
            else {
                echo "fiat", "\n";
                $pair = Crypto_fiat_exchange_pair::where(['Exchange_id' => $item['Exchange'], 'From' => $item['From'], 'To' => $item['To']])->first();

            }
            if($pair){
		    echo $pair, "\n";
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

    public function create_fiat(Request $request){
        $fiat = new Fiat;
        $fiat->Fiat_id = $request["Id"];
        $fiat->Name = $request["Name"];
        $fiat->Value_USD = $request["Value"];

        $this->statsd->statsd->increment("db.connections", 1, array("function"=>"create_fiat"));

        try {
            $fiat->save();
        }
        catch (QueryException $e){

            return response()->json([
                "message" => $e->getMessage()
            ], 501);
        }

        return response()->json([
            "message" => "Fiat added"
        ], 200);
    }
}
