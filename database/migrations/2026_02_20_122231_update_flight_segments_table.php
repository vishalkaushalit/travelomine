<?php
// database/migrations/2026_02_20_000001_update_flight_segments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            // rename columns to match new naming (if they don't exist yet)
            if (!Schema::hasColumn('flight_segments', 'from_city')) {
                $table->string('from_city', 100)->after('booking_id')->nullable();
            }
            if (!Schema::hasColumn('flight_segments', 'to_city')) {
                $table->string('to_city', 100)->after('from_city')->nullable();
            }
            if (!Schema::hasColumn('flight_segments', 'return_date')) {
                $table->date('return_date')->nullable()->after('departure_date');
            }
            if (!Schema::hasColumn('flight_segments', 'airline_name')) {
                $table->string('airline_name', 150)->nullable()->after('return_date');
            }
            if (!Schema::hasColumn('flight_segments', 'flight_number')) {
                $table->string('flight_number', 30)->nullable()->after('airline_name');
            }
            if (!Schema::hasColumn('flight_segments', 'segment_pnr')) {
                $table->string('segment_pnr', 30)->nullable()->after('flight_number');
            }
            // cabin_class (rename from cabin_type if needed)
            if (!Schema::hasColumn('flight_segments', 'cabin_class')) {
                $table->string('cabin_class', 50)->nullable()->after('segment_pnr');
            }
        });
    }

    public function down(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->dropColumn(['from_city','to_city','return_date','airline_name','flight_number','segment_pnr','cabin_class']);
        });
    }
};