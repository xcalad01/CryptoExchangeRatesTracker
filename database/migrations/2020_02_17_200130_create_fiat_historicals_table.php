<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiatHistoricalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fiat_historicals', function (Blueprint $table) {
            $table->string('Date');
            $table->string('Fiat_id');
            $table->float('Value_USD');

            $table->foreign("Fiat_id")->references("Fiat_id")->on("fiats");
            $table->primary(["Fiat_id", "Date"]);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fiat_historicals');
    }
}
