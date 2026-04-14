<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // MySQL doesn't support removing ENUM values easily, so we need to modify the column
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending', 
            'assigned_to_charging', 
            'auth_email_sent', 
            'payment_processing', 
            'confirmed', 
            'ticketed', 
            'failed', 
            'cancelled', 
            'hold', 
            'refund',
            'charging_in_progress'
        ) DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending', 
            'assigned_to_charging', 
            'auth_email_sent', 
            'payment_processing', 
            'confirmed', 
            'ticketed', 
            'failed', 
            'cancelled', 
            'hold', 
            'refund'
        ) DEFAULT 'pending'");
    }
};