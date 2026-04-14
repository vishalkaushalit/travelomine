<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::table('bookings', function (Blueprint $table) {
        $table->string('card_last_four', 4)->after('billing_phone')->nullable();
        $table->dropColumn('cabin_type'); // Moved to segments
    });
    Schema::table('flight_segments', function (Blueprint $table) {
        $table->string('cabin_type')->after('airline_code')->nullable();
    });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
