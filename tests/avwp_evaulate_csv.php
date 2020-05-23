<?php
require __DIR__ . "/../app/Modules/Stats.php";
use App\Modules\Stats;
$statsd = new Stats();

function price_diffs(){
    $row = 1;
    if (($handle = fopen("tests/avwp_price.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $api_prices = array($data[2], $data[3], $data[4]);
            $internal_price = $data[1];
            $do_continue = false;



            $percentage = 0;
            $dollar_diff = 0;
            foreach ($api_prices as $price){
                if ($price == 0){
                    $do_continue = true;
                }
                $percentage += abs($internal_price - $price) / ($internal_price + $price) * 100;
                $dollar_diff += abs($internal_price - $price);
            }

            if ($do_continue){
                continue;
            }

            $dollar_diff_apis = 0;
            for ($i = 0; $i < count($api_prices); $i++){
                for ($j = $i+1; $j < count($api_prices); $j++){
                    print_r($api_prices[$i]."\n");
                    print_r($api_prices[$j]."\n");
                    $dollar_diff_apis += abs($api_prices[$i] - $api_prices[$j]);
                }
            }


            $dollar_diff = $dollar_diff / 3;

            $dollar_diff_apis = $dollar_diff_apis / 3;


            $file_handle = fopen('tests/dollar_diffs.csv', 'a');
            fputcsv($file_handle, array($dollar_diff, $dollar_diff_apis));
        }
        fclose($handle);
    }
}

function price_development(){
    if (($handle = fopen("tests/avwp_price.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $prices_array = array(date('Y-m-d H:i', intval($data[0])), $data[1], $data[2], $data[3], $data[4]);
            print_r($prices_array);
            if(in_array(0, $prices_array)){
                continue;
            }
            $file_handle = fopen('tests/prices_development.csv', 'a');
            fputcsv($file_handle, $prices_array);
        }
        fclose($handle);
    }
}

price_development();
