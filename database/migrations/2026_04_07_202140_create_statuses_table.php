<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade');

            $table->string('booking_reference')->nullable();

            // Captured, Refund, Void, Pending
            // if blank, treat as Pending
            $table->enum('transaction_status', ['Captured', 'Refund', 'Void', 'Pending'])
                ->nullable();

            // Cancelled, Sent, Not sent
            $table->enum('ticket_status', ['Cancelled', 'Sent', 'Not sent'])
                ->nullable();

            // fetched from bookings.status
            $table->string('booking_status')->nullable();

            $table->timestamps();

            $table->unique('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status');
    }
};