<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCirculatingSuplyToCryptoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cryptocurrencies', function (Blueprint $table) {
            $table->float("CirculatingActual")->default(-1);
            $table->float("CirculatingCap")->default(-1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cryptocurrencies', function (Blueprint $table) {
            $table->dropColumn('CirculatingActual');
            $table->dropColumn('CirculatingCap');
        });
    }
}
