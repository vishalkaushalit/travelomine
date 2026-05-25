<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement(<<<'SQL'
            ALTER TABLE booking_remarks
            MODIFY remark_type ENUM('general','payment','modification','customer_request','followup','changes_team') NOT NULL DEFAULT 'general';
        SQL);
    }

    public function down()
    {
        \Illuminate\Support\Facades\DB::statement(<<<'SQL'
            ALTER TABLE booking_remarks
            MODIFY remark_type ENUM('general','payment','modification','customer_request','followup') NOT NULL DEFAULT 'general';
        SQL);
    }
};
