<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_cabs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('cab_type', ['pickup', 'drop', 'roundtrip'])
                  ->default('pickup');
            $table->text('pickup_location')->nullable();
            $table->text('drop_location')->nullable();
            $table->dateTime('pickup_datetime')->nullable();
            $table->decimal('cab_cost', 10, 2)->default(0.00);
            $table->text('cab_remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cabs');
    }
};
