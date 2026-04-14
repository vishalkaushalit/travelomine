<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Safe way to alter an enum in MySQL by completely redefining the list of allowed values
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending', 'assigned_to_charging', 'auth_email_sent', 'payment_processing', 
            'confirmed', 'ticketed', 'failed', 'cancelled', 'hold', 'refund', 'charging_in_progress',
            'Alert', 'RDR', 'retrieval', 'chargeback', 'charged'
        ) DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert to old enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending', 'assigned_to_charging', 'auth_email_sent', 'payment_processing', 
            'confirmed', 'ticketed', 'failed', 'cancelled', 'hold', 'refund', 'charging_in_progress'
        ) DEFAULT 'pending'");
    }
};


