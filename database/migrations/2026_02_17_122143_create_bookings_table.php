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
        Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // The Agent
    $table->string('agent_custom_id'); // e.g. AG1225
    $table->date('booking_date');
    $table->string('call_type'); // Meta, Cap
    $table->string('service_provided'); // Flight, Hotel, etc.
    $table->string('service_type'); // New, Change, Update
    $table->string('booking_portal')->default('website'); // website, gds
    $table->boolean('email_auth_taken')->default(false);
    $table->string('flight_type'); // One-way, Round, Multi
    $table->string('cabin_type'); 
    
    // Primary Customer Info
    $table->string('customer_email');
    $table->string('customer_phone');
    $table->string('billing_phone');

    // Financials
    $table->string('currency')->default('USD');
    $table->decimal('amount_charged', 12, 2);
    $table->decimal('amount_paid_airline', 12, 2);
    $table->decimal('total_mco', 12, 2);
    
    $table->string('status')->default('pending'); // For MIS to charge
    $table->text('agent_remarks')->nullable();
    $table->text('mis_remarks')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
