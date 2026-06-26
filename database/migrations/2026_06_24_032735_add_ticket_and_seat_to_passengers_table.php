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
        Schema::table('passengers', function (Blueprint $table) {
            if (!Schema::hasColumn('passengers', 'ticket_number')) {
                $table->string('ticket_number')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('passengers', 'seat_number')) {
                $table->string('seat_number')->nullable()->after('ticket_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            if (Schema::hasColumn('passengers', 'ticket_number')) {
                $table->dropColumn('ticket_number');
            }
            if (Schema::hasColumn('passengers', 'seat_number')) {
                $table->dropColumn('seat_number');
            }
        });
    }
};
