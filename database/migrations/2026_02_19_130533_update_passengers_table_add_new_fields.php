<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('passengers', function (Blueprint $table) {

            // ✅ Add passenger_type after booking_id
            $table->enum('passenger_type', ['ADT', 'CHD', 'INF', 'INL'])
                  ->default('ADT')
                  ->after('booking_id')
                  ->comment('ADT=Adult, CHD=Child, INF=Infant on seat, INL=Infant on lap');

            // ✅ Add title after passenger_type
            $table->enum('title', ['Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Master'])
                  ->default('Mr')
                  ->after('passenger_type');

            // ✅ Rename sex → gender ENUM (properly typed)
            $table->enum('gender', ['male', 'female', 'other'])
                  ->default('male')
                  ->after('last_name')
                  ->comment('Replaces old sex column');

            // ✅ Optional travel document fields
            $table->string('passport_number', 50)->nullable()->after('dob');
            $table->date('passport_expiry')->nullable()->after('passport_number');
            $table->string('nationality', 100)->nullable()->after('passport_expiry');

            // ✅ Optional preference fields
            $table->string('seat_preference', 50)->nullable()->after('nationality');
            $table->string('meal_preference', 50)->nullable()->after('seat_preference');
            $table->text('special_assistance')->nullable()->after('meal_preference');
        });

        // ✅ Copy old `sex` data into new `gender` column then drop `sex`
        \DB::statement("UPDATE passengers SET gender = LOWER(sex)");

        Schema::table('passengers', function (Blueprint $table) {
            $table->dropColumn('sex');
        });
    }

    public function down(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            // Restore sex column from gender before dropping
            $table->string('sex')->nullable()->after('last_name');
        });

        \DB::statement("UPDATE passengers SET sex = gender");

        Schema::table('passengers', function (Blueprint $table) {
            $table->dropColumn([
                'passenger_type',
                'title',
                'gender',
                'passport_number',
                'passport_expiry',
                'nationality',
                'seat_preference',
                'meal_preference',
                'special_assistance',
            ]);
        });
    }
};
