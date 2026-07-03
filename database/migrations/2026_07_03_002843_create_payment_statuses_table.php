<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentStatusesTable extends Migration
{
    public function up()
    {
        // ✅ CHECK IF TABLE EXISTS - AGAR HAI TOH SKIP KARO
        if (!Schema::hasTable('payment_statuses')) {
            Schema::create('payment_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->string('color')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // ✅ ADD COLUMN TO BOOKINGS - AGAR NAHI HAI TOH ADD KARO
        if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings', 'payment_status_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('payment_status_id')
                      ->nullable()
                      ->after('status')
                      ->constrained('payment_statuses')
                      ->onDelete('set null');
                
                $table->index('payment_status_id');
            });
        }

        // ✅ ADD COLUMNS TO BOOKING_CARDS - AGAR NAHI HAIN TOH ADD KARO
        if (Schema::hasTable('booking_cards')) {
            Schema::table('booking_cards', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_cards', 'payment_status')) {
                    $table->string('payment_status')->nullable()->after('charge_amount');
                }
                if (!Schema::hasColumn('booking_cards', 'payment_processed_at')) {
                    $table->timestamp('payment_processed_at')->nullable()->after('payment_status');
                }
                if (!Schema::hasColumn('booking_cards', 'payment_transaction_id')) {
                    $table->string('payment_transaction_id')->nullable()->after('payment_processed_at');
                }
            });
        }
    }

    public function down()
    {
        // ✅ REMOVE COLUMNS FROM BOOKINGS
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'payment_status_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['payment_status_id']);
                $table->dropColumn('payment_status_id');
            });
        }

        // ✅ REMOVE COLUMNS FROM BOOKING_CARDS
        if (Schema::hasTable('booking_cards')) {
            Schema::table('booking_cards', function (Blueprint $table) {
                if (Schema::hasColumn('booking_cards', 'payment_status')) {
                    $table->dropColumn('payment_status');
                }
                if (Schema::hasColumn('booking_cards', 'payment_processed_at')) {
                    $table->dropColumn('payment_processed_at');
                }
                if (Schema::hasColumn('booking_cards', 'payment_transaction_id')) {
                    $table->dropColumn('payment_transaction_id');
                }
            });
        }

        // ✅ DROP TABLE - BUT ONLY IF EXISTS
        if (Schema::hasTable('payment_statuses')) {
            Schema::dropIfExists('payment_statuses');
        }
    }
}