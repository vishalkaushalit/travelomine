<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make read_at nullable in user_notification_reads table
        if (Schema::hasTable('user_notification_reads')) {
            Schema::table('user_notification_reads', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Revert the change
        if (Schema::hasTable('user_notification_reads')) {
            Schema::table('user_notification_reads', function (Blueprint $table) {
                $table->timestamp('read_at')->change();
            });
        }
    }
};
