<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_cards', function (Blueprint $table) {
            $table->id();

            // ✅ FK to bookings
            $table->foreignId('booking_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // ✅ FK to merchant (nullable until merchant is saved)
            $table->foreignId('merchant_id')
                  ->nullable()
                  ->constrained('merchants')
                  ->nullOnDelete()
                  ->comment('Which merchant/gateway processed this card charge');

            // ✅ Card holder info
            $table->string('card_holder_name');

            // ✅ Card details (store encrypted in production)
            $table->string('card_number')->nullable()
                  ->comment('Encrypted card number - never store raw');
            $table->string('card_type', 50)->nullable()
                  ->comment('VISA, MASTERCARD, AMEX, DISCOVER');
            $table->string('card_last_four', 4)->nullable()
                  ->comment('Last 4 digits only, unencrypted for display');
            $table->string('expiration_month', 2)->nullable();
            $table->string('expiration_year', 4)->nullable();
            $table->string('cvv')->nullable()
                  ->comment('Encrypted CVV - clear after charge');

            // ✅ Billing details
            $table->text('billing_address')->nullable();
            $table->string('billing_phone', 20)->nullable();
            $table->string('billing_email')->nullable();

            // ✅ Charge details
            $table->decimal('charge_amount', 10, 2)->default(0.00)
                  ->comment('Amount to charge on this specific card');
            $table->boolean('is_charged')->default(false);
            $table->timestamp('charged_at')->nullable();
            $table->string('transaction_id', 100)->nullable()
                  ->comment('Gateway transaction reference');
            $table->enum('payment_status', ['pending', 'success', 'failed', 'refunded'])
                  ->default('pending');

            // ✅ Card order (if booking split across multiple cards)
            $table->unsignedTinyInteger('card_order')->default(1)
                  ->comment('1 = primary card, 2 = secondary, etc.');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cards');
    }
};
