<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('charge_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('charger_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade'); // who assigned the booking
            $table->foreignId('merchant_id')->nullable()->constrained(); // optional merchant association
            $table->enum('status', ['pending', 'accepted', 'completed', 'rejected'])->default('pending');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Ensure one booking is only assigned once
            $table->unique('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charge_assignments');
    }
};
