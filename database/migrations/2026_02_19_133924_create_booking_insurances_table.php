<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_insurances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('insurance_type', 100)->nullable()
                  ->comment('e.g. Trip cancellation, Medical, Comprehensive');
            $table->string('insurance_provider', 255)->nullable();
            $table->decimal('coverage_amount', 12, 2)->default(0.00);
            $table->decimal('insurance_cost', 10, 2)->default(0.00);
            $table->string('policy_number', 100)->nullable();
            $table->text('insurance_remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_insurances');
    }
};
