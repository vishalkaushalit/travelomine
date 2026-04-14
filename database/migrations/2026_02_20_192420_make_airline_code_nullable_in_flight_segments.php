<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            // Make airline_code nullable since controller doesn't send it
            $table->string('airline_code', 255)->nullable()->change();
            
            // Also make cabin_type nullable (it's duplicate of cabin_class)
            $table->string('cabin_type', 255)->nullable()->change();
            
            // Make pnr nullable (segment_pnr is the one being used)
            $table->string('pnr', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->string('airline_code', 255)->nullable(false)->change();
            $table->string('cabin_type', 255)->nullable(false)->change();
            $table->string('pnr', 255)->nullable(false)->change();
        });
    }
};
