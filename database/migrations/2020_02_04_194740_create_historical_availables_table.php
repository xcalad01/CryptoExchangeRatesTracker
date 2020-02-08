<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalAvailablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_available', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('From');
            $table->string('To');
            $table->string('Exchange_id');

            $table->foreign('Exchange_id')->references('Exchange_id')->on('exchanges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_availables');
    }
}
