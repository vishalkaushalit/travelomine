<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_reference')->after('id')->unique()->nullable();
            $table->string('gk_pnr')->after('flight_type')->nullable();
            $table->string('airline_pnr')->after('gk_pnr')->nullable();
            $table->string('customer_name')->after('agent_custom_id')->nullable();
            $table->string('departure_city')->after('flight_type')->nullable();
            $table->string('arrival_city')->after('departure_city')->nullable();
            $table->date('departure_date')->after('arrival_city')->nullable();
            $table->date('return_date')->after('departure_date')->nullable();
            $table->string('airline_name')->after('return_date')->nullable();
            $table->string('flight_number')->after('airline_name')->nullable();
            $table->enum('cabin_class', ['economy', 'premium_economy', 'business', 'first'])->after('flight_number')->default('economy');
            $table->unsignedTinyInteger('total_passengers')->after('cabin_class')->default(1);
            $table->unsignedTinyInteger('adults')->after('total_passengers')->default(1);
            $table->unsignedTinyInteger('children')->after('adults')->default(0);
            $table->unsignedTinyInteger('infants')->after('children')->default(0);
            $table->text('billing_address')->after('billing_phone')->nullable();
            $table->timestamp('auth_email_sent_at')->after('email_auth_taken')->nullable();
            $table->timestamp('payment_confirmed_at')->after('auth_email_sent_at')->nullable();
            $table->timestamp('ticketed_at')->after('payment_confirmed_at')->nullable();
            $table->text('charging_remarks')->after('mis_remarks')->nullable();
        });

        // 1. Data Patch: Fill existing rows so they don't violate the constraint
        // We set a dummy PNR for existing records to allow the migration to pass
        DB::table('bookings')->whereNull('gk_pnr')->whereNull('airline_pnr')->update([
            'gk_pnr' => 'LEGACY',
            'booking_reference' => DB::raw("CONCAT('BTK', id, CHAR(FLOOR(65 + (RAND() * 26))))")
        ]);

        // 2. Now apply the constraints safely
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT chk_pnr_required CHECK (gk_pnr IS NOT NULL OR airline_pnr IS NOT NULL)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT chk_max_passengers CHECK (total_passengers <= 9)');
        
        // 3. Update Status Enum
        // Note: Some DB drivers require raw SQL for changing ENUMs if they are complex
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'assigned_to_charging', 'auth_email_sent', 'payment_processing', 'confirmed', 'ticketed', 'failed', 'cancelled', 'hold', 'refund') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS chk_pnr_required');
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS chk_max_passengers');
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_reference', 'gk_pnr', 'airline_pnr', 'customer_name',
                'departure_city', 'arrival_city', 'departure_date', 'return_date',
                'airline_name', 'flight_number', 'cabin_class', 'total_passengers',
                'adults', 'children', 'infants', 'billing_address',
                'auth_email_sent_at', 'payment_confirmed_at', 'ticketed_at',
                'charging_remarks'
            ]);
        });
    }
};
