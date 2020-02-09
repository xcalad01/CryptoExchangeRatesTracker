<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exchange;
use App\Historical_available;
use App\Historical_five_min;
use App\Cryptocurrencies;
use App\Crypto_exchange_pair;
use App\Crypto_fiat_exchange_pair;
use App\Fiat;

class ApiController extends Controller
{
    public function create_exchange(Request $request) {
        $exchange = new Exchange;
        $exchange->Exchange_id = $request['Exchange_id'];
        $exchange->Name = $request['Name'];
        $exchange->Url = $request['Url'];
        $exchange->Image = $request['Image'];
        $exchange->save();

        return response()->json([
            "message" => "exchange record created"
        ], 200);
    }

    private function create_available($data){
        $available = new Historical_available;
        $available->From = $data['from'];
        $available->To = $data['to'];
        $available->Exchange_id = $data['exchange_id'];

        return $available->save();
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

        return $historical->save();
    }

    public function crypto_add_historical_five(Request $request) {
        $exchange_id = $request['Exchange_id'];
        $available = Historical_available::where('Exchange_id', $exchange_id)->first();
        if (!$available):
            $data = array(
                "from"=>$request['From'],
                "to"=>$request['To'],
                "exchange_id"=>$request['Exchange_id']
            );

            if(!self::create_available($data)) {
                return response()->json([
                    "message"=>"Could not add to historical available table!!"
                ], 501);
            }
        endif;

        $available = Historical_available::where('Exchange_id', $exchange_id)->first();
        $data = array(
            "id"=>$available->id,
            'timestamp'=>$request['Timestamp'],
            "historical"=>$request['Historical']
        );

        if(!self::add_crypto_historical_five($data)) {
            return response()->json([
                "message" => "Could not add to historical five min. table!!"
            ], 501);
        }

        return response()->json([
            "message" => "Historical available saved"
        ], 200);
    }

    public function update_crypto_pair_value(Request $request){
        $is_fiat = $request['Fwiat'];
        echo $is_fiat;
        foreach ($request['items'] as $item){
            if (!Exchange::where('Exchange_id', $item['Exchange'])->first()){
                return response()->json([
                    "message"=>"Exchange does not exits"
                ], 404);
            }
            elseif (!Cryptocurrencies::where('Crypto_id', $item['From'])->first() and !Cryptocurrencies::where('Crypto_id', $item['To'])->first()){
                return response()->json([
                    "message"=>"Crypto does not exits"
                ], 404);
            }

            if ($is_fiat=="0") {
                $pair = Crypto_exchange_pair::where(['Exchange_id' => $item['Exchange'], 'From' => $item['From'], 'To' => $item['To']])->first();
            }
            else {
                $pair = Crypto_fiat_exchange_pair::where(['Exchange_id' => $item['Exchange'], 'From' => $item['From'], 'To' => $item['To']])->first();

            }
            if($pair){
                echo $pair;
                $pair->update(['Value'=>$item['Value']]);
                return response()->json([
                    "message"=>"Crypto pair updated"
                ], 200);
            }

            if ($is_fiat == "0") {
                $to_save = new Crypto_exchange_pair;
            }
            else {
                $to_save = new Crypto_fiat_exchange_pair;
            }

            $to_save->Value = $item['Value'];
            $to_save->Exchange_id = $item['Exchange'];
            $to_save->From = $item['From'];
            $to_save->To = $item['To'];

            if(!$to_save->save()){
                return response()->json([
                    "message" => "Could not update"
                ], 501);
            }
        }

        return response()->json([
            "message" => "Crypto pair created"
        ], 200);
    }

    public function create_crypto(Request $request){
        $crypto = new Cryptocurrencies;
        $crypto->Crypto_id = $request['Id'];
        $crypto->Name = $request['Name'];

        if (!$crypto->save()){
            return response()->json([
                "message" => "Could not add crypto"
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

        if (!$fiat->save()){
            return response()->json([
                "message" => "Could not add fiat"
            ], 501);
        }

        return response()->json([
            "message" => "Fiat added"
        ], 200);
    }
}
