<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->time('departure_time')->nullable()->after('departure_date');
            $table->time('arrival_time')->nullable()->after('departure_time');
        });
    }

    public function down()
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->dropColumn(['departure_time', 'arrival_time']);
        });
    }
};