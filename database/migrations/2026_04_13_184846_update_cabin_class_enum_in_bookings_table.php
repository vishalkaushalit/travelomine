<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `cabin_class` VARCHAR(50) NOT NULL DEFAULT 'Economy'");

        DB::statement("UPDATE `bookings` SET `cabin_class` = 'Economy' WHERE `cabin_class` = 'economy'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'Basic-Economy' WHERE `cabin_class` = 'basic_economy'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'Premium-Economy' WHERE `cabin_class` = 'premium_economy'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'Business' WHERE `cabin_class` = 'business'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'First' WHERE `cabin_class` = 'first'");

        DB::statement("
            ALTER TABLE `bookings`
            MODIFY COLUMN `cabin_class` ENUM(
                'Economy',
                'Basic-Economy',
                'Premium-Economy',
                'Business',
                'First'
            ) NOT NULL DEFAULT 'Economy'
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `cabin_class` VARCHAR(50) NOT NULL DEFAULT 'economy'");

        DB::statement("UPDATE `bookings` SET `cabin_class` = 'economy' WHERE `cabin_class` = 'Economy'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'basic_economy' WHERE `cabin_class` = 'Basic-Economy'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'premium_economy' WHERE `cabin_class` = 'Premium-Economy'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'business' WHERE `cabin_class` = 'Business'");
        DB::statement("UPDATE `bookings` SET `cabin_class` = 'first' WHERE `cabin_class` = 'First'");

        DB::statement("
            ALTER TABLE `bookings`
            MODIFY COLUMN `cabin_class` ENUM(
                'economy',
                'premium_economy',
                'business',
                'first'
            ) NOT NULL DEFAULT 'economy'
        ");
    }
};