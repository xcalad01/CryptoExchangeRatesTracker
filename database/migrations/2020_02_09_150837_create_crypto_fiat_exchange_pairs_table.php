<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoFiatExchangePairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_fiat_exchange_pairs', function (Blueprint $table) {
            $table->string("Value");

            $table->string("From");
            $table->string("To");
            $table->string("Exchange_id");

            $table->foreign("From")->references("Crypto_id")->on("cryptocurrencies");
            $table->foreign("To")->references("Fiat_id")->on("fiats");
            $table->foreign("Exchange_id")->references("Exchange_id")->on("exchanges");
            $table->primary(["From", "Exchange_id", "To"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto_fiat_exchange_pairs');
    }
}
