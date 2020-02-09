<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoExchangePairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_exchange_pairs', function (Blueprint $table) {
            $table->string("From");
            $table->string("To");
            $table->string("Value");

            $table->string("Crypto_id");
            $table->string("Exchange_id");

            $table->foreign("Crypto_id")->references("Crypto_id")->on("cryptocurrencies");
            $table->foreign("Exchange_id")->references("Exchange_id")->on("exchanges");
            $table->primary(["Crypto_id", "Exchange_id", "From", "To"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto_exchange_pairs');
    }
}
