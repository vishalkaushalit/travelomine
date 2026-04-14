<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            // Rename 'code' → 'merchant_code'
            $table->renameColumn('code', 'merchant_code');

            // Remove 'account_number'
            $table->dropColumn('account_number');

            // Remove 'currency'
            $table->dropColumn('currency');

            // Add new columns
            $table->string('contact_number', 20)->after('merchant_code');
            $table->string('support_mail')->after('contact_number');
            $table->decimal('wallet_balance', 12, 2)->nullable()->after('support_mail');
            
            // 'is_active' and 'notes' stay the same → no action needed
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            // Reverse: drop new columns
            $table->dropColumn(['contact_number', 'support_mail', 'wallet_balance']);

            // Reverse: add back removed columns (with original types)
            $table->string('account_number')->nullable()->after('merchant_code');
            $table->string('currency', 10)->default('USD')->after('account_number');

            // Reverse rename
            $table->renameColumn('merchant_code', 'code');
        });
    }
};