<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add missing columns if they don't exist
            
            if (!Schema::hasColumn('bookings', 'infant_in_lap')) {
                $table->tinyInteger('infant_in_lap')->unsigned()->default(0)->after('infants');
            }
            
            if (!Schema::hasColumn('bookings', 'airline_merchant_id')) {
                $table->bigInteger('airline_merchant_id')->unsigned()->nullable()->after('airline_merchant');
            }
            
            if (!Schema::hasColumn('bookings', 'airline_merchant_name')) {
                $table->string('airline_merchant_name', 255)->nullable()->after('airline_merchant_id');
            }
            
            if (!Schema::hasColumn('bookings', 'payment_type')) {
                $table->enum('payment_type', ['full', 'split'])->nullable()->after('payment_card_details');
            }
            
            if (!Schema::hasColumn('bookings', 'manager_remark')) {
                $table->text('manager_remark')->nullable()->after('mis_remarks');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'infant_in_lap',
                'airline_merchant_id',
                'airline_merchant_name',
                'payment_type',
                'manager_remark'
            ]);
        });
    }
};