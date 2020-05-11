<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalColumnsToExchange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->string('Year')->nullable(true);
            $table->string('Country')->nullable(true);
            $table->boolean('Centralized')->nullable(true);
            $table->string('Accepted_payment_methods')->nullable(true);
            $table->string('Facebook')->nullable(true);
            $table->string('Reddit')->nullable(true);
            $table->string('Twitter')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->dropColumn('Year');
            $table->dropColumn('Country');
            $table->dropColumn('Centralized');
            $table->dropColumn('Accepted_payment_methods');
            $table->dropColumn('Facebook');
            $table->dropColumn('Reddit');
            $table->dropColumn('Twitter');
        });
    }
}
