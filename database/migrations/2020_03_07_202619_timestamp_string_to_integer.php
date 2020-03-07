<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TimestampStringToInteger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fiat_historicals', function (Blueprint $table) {
            DB::statement("ALTER TABLE fiat_historicals ALTER \"Date\" TYPE INT USING \"Date\"::integer ");
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fiat_historicals', function (Blueprint $table) {
            //
        });
    }
}
