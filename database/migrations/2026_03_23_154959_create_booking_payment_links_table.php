<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_payment_links', function (Blueprint $table) {
            $table->id();

            // Link back to booking and merchant
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();

            // Unique token for public URL /pay/{token}
            $table->uuid('token')->unique();

            // Copy of customer/billing info at the time link was created
            $table->string('customer_name');          // from booking customername
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_address')->nullable();

            // Amount to charge (can be edited vs total_mco)
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');

            // Status: pending / paid / failed / expired / cancelled
            $table->string('status')->default('pending');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Optional meta: auth email id / charge user id / notes
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_payment_links');
    }
};
