<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalFiveMinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_five_min', function (Blueprint $table) {
            $table->string('Open');
            $table->string('High');
            $table->string('Low');
            $table->string('Close');
            $table->string('Volume');
            $table->string('Timestamp');
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
        Schema::dropIfExists('historical_five_mins');
    }
}
