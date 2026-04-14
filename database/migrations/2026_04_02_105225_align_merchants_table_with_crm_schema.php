<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            if (! Schema::hasColumn('merchants', 'merchant_code')) {
                $table->string('merchant_code', 50)->nullable()->after('name');
            }

            if (! Schema::hasColumn('merchants', 'security_key')) {
                $table->string('security_key')->nullable()->after('merchant_code');
            }

            if (! Schema::hasColumn('merchants', 'api_url')) {
                $table->string('api_url')->nullable()->after('security_key');
            }

            if (! Schema::hasColumn('merchants', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('api_url');
            }

            if (! Schema::hasColumn('merchants', 'support_mail')) {
                $table->string('support_mail')->nullable()->after('contact_number');
            }

            if (! Schema::hasColumn('merchants', 'wallet_balance')) {
                $table->decimal('wallet_balance', 12, 2)->default(0)->after('support_mail');
            }
        });

        // Backfill merchant_code from old code column if present
        if (Schema::hasColumn('merchants', 'code') && Schema::hasColumn('merchants', 'merchant_code')) {
            DB::table('merchants')
                ->whereNull('merchant_code')
                ->update([
                    'merchant_code' => DB::raw('code')
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $drop = [];

            foreach ([
                'merchant_code',
                'security_key',
                'api_url',
                'contact_number',
                'support_mail',
                'wallet_balance',
            ] as $column) {
                if (Schema::hasColumn('merchants', $column)) {
                    $drop[] = $column;
                }
            }

            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};