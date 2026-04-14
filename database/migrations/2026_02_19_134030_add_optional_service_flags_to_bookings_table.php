<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('hotel_required')->default(false)->after('agent_remarks');
            $table->boolean('cab_required')->default(false)->after('hotel_required');
            $table->boolean('insurance_required')->default(false)->after('cab_required');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['hotel_required', 'cab_required', 'insurance_required']);
        });
    }
};
