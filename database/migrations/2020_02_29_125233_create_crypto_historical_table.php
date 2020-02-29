<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoHistoricalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_historical', function (Blueprint $table) {
            $table->float('Open');
            $table->float('High');
            $table->float('Low');
            $table->float('Close');
            $table->float('Volume');
            $table->integer('Timestamp');
            $table->bigInteger('id');

            $table->primary(['id', 'Timestamp']);
            $table->foreign('id')->references('id')->on('historical_available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto_historical');
    }
}
