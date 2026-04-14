<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAirlineAndAgencyMerchantToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('airline_merchant_id')->nullable();
            $table->string('airline_merchant_name')->nullable();

            $table->unsignedBigInteger('agency_merchant_id')->nullable();
            $table->string('agency_merchant_name')->nullable();

            $table->foreign('airline_merchant_id')
                ->references('id')->on('merchants');

            $table->foreign('agency_merchant_id')
                ->references('id')->on('merchants');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['airline_merchant_id']);
            $table->dropForeign(['agency_merchant_id']);

            $table->dropColumn([
                'airline_merchant_id',
                'airline_merchant_name',
                'agency_merchant_id',
                'agency_merchant_name',
            ]);
        });
    }
}
