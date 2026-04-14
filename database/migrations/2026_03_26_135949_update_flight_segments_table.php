<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            // Remove duplicate cabin_type column
            $table->dropColumn('cabin_type');

            // Rename pnr -> airline_pnr
            $table->renameColumn('pnr', 'airline_pnr');
        });

        // Backfill segment_pnr from booking->gk_pnr for existing rows
        DB::statement('
            UPDATE flight_segments fs
            JOIN bookings b ON b.id = fs.booking_id
            SET fs.segment_pnr = b.gk_pnr
            WHERE (fs.segment_pnr IS NULL OR fs.segment_pnr = "")
              AND b.gk_pnr IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->string('cabin_type')->nullable();
            $table->renameColumn('airline_pnr', 'pnr');
        });
    }
};
