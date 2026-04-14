<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            // Make airport fields nullable so they're optional
            $table->string('from_airport', 10)->nullable()->default(null)->change();
            $table->string('to_airport', 10)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->string('from_airport', 10)->nullable(false)->change();
            $table->string('to_airport', 10)->nullable(false)->change();
        });
    }
};
