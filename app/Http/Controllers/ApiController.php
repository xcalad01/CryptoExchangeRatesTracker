<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exchange;
use App\Historical_available;
use App\Historical_five_min;
use App\Cryptocurrencies;
use App\Crypto_exchange_pair;

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
        foreach ($request['items'] as $item){
            if (!Exchange::where('Exchange_id', $item['Exchange_id'])->one_or_none()){
                return response()->json([
                    "message"=>"Exchange does not exits"
                ], 404);
            }
            elseif (!Cryptocurrencies::where('Crypto_id', $item['From'])->one_or_none() and !Cryptocurrencies::where('Crypto_id', $item['To'])->one_or_none()){
                return response()->json([
                    "message"=>"Crypto does not exits"
                ], 404);
            }

            $crypto_pair = Crypto_exchange_pair::where('Exchange_id', $item['Exchange_id']
                ->where('From', $item['From'])
                ->where('To', $item['To'])->one_or_none());

            if($crypto_pair){
                $crypto_pair->update(['Value'=>$item['Value']]);
                return response()->json([
                    "message"=>"Crypto pair updated"
                ], 200);
            }

            $to_save = new Crypto_exchange_pair;
            $to_save->Value = $item['Value'];
            $to_save->Exchange_id = $item['Exchange_id'];
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
}
