<?php
require __DIR__ . "/../app/Modules/Stats.php";
use App\Modules\Stats;
$statsd = new Stats();

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

        $percentage = $percentage / 3;
        print_r("AVG perc diff: {$percentage}\n");
        $statsd->statsd->histogram("avwp_avg_perc_diff", $percentage, 1);

        $dollar_diff = $dollar_diff / 3;
        print_r("AVG dollar diff: {$dollar_diff}\n");
        $statsd->statsd->histogram("avwp_avg_dollar_diff", $dollar_diff, 1);

        $dollar_diff_apis = $dollar_diff_apis / 3;
        print_r("AVG other api combined dollar diff: {$dollar_diff_apis}\n\n");
        $statsd->statsd->histogram("avwp_avg_api_combined_dollar_diff", $dollar_diff_apis, 1);
    }
    fclose($handle);
}
