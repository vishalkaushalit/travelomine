<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // ✅ Add phone after email
            $table->string('phone', 20)->nullable()->after('email');

            // ✅ Add role ENUM (replaces Spatie for basic role tracking)
            $table->enum('role', [
                'admin',
                'manager',
                'agent',
                'charging_team',
                'mis_team',
                'cs_support',
            ])->default('agent')->after('password');

            // ✅ Account status flags
            $table->boolean('is_active')->default(true)->after('role');
            $table->boolean('is_blocked')->default(false)->after('is_active');

            // ✅ Last login tracker
            $table->timestamp('last_login')->nullable()->after('is_blocked');

            // ✅ Audit: who created this user
            $table->unsignedBigInteger('created_by')->nullable()->after('last_login');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'phone',
                'role',
                'is_active',
                'is_blocked',
                'last_login',
                'created_by',
            ]);
        });
    }
};
