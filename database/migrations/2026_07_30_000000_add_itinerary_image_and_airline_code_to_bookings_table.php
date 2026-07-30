<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'itinerary_image')) {
                $table->string('itinerary_image', 255)->nullable()->after('agent_remarks');
            }
            if (!Schema::hasColumn('bookings', 'airline_code')) {
                $table->string('airline_code', 10)->nullable()->after('airline_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'itinerary_image')) {
                $table->dropColumn('itinerary_image');
            }
            if (Schema::hasColumn('bookings', 'airline_code')) {
                $table->dropColumn('airline_code');
            }
        });
    }
};
