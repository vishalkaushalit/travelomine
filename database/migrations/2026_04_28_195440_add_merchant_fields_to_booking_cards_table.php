<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_cards', function (Blueprint $table) {
            $table->string('merchantname')->nullable();
            $table->string('merchanttype')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_cards', function (Blueprint $table) {
            $table->dropColumn(['merchantname', 'merchanttype']);
        });
    }
};