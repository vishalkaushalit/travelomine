<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            if (! Schema::hasColumn('merchants', 'smtp_host')) {
                $table->string('smtp_host')->nullable()->after('support_mail');
            }

            if (! Schema::hasColumn('merchants', 'smtp_port')) {
                $table->unsignedSmallInteger('smtp_port')->nullable()->after('smtp_host');
            }

            if (! Schema::hasColumn('merchants', 'smtp_username')) {
                $table->string('smtp_username')->nullable()->after('smtp_port');
            }

            if (! Schema::hasColumn('merchants', 'smtp_password')) {
                $table->text('smtp_password')->nullable()->after('smtp_username');
            }

            if (! Schema::hasColumn('merchants', 'smtp_encryption')) {
                $table->string('smtp_encryption', 20)->nullable()->after('smtp_password');
            }

            if (! Schema::hasColumn('merchants', 'from_email')) {
                $table->string('from_email')->nullable()->after('smtp_encryption');
            }

            if (! Schema::hasColumn('merchants', 'from_name')) {
                $table->string('from_name')->nullable()->after('from_email');
            }

            if (! Schema::hasColumn('merchants', 'reply_to_email')) {
                $table->string('reply_to_email')->nullable()->after('from_name');
            }

            if (! Schema::hasColumn('merchants', 'reply_to_name')) {
                $table->string('reply_to_name')->nullable()->after('reply_to_email');
            }

            if (! Schema::hasColumn('merchants', 'is_smtp_active')) {
                $table->boolean('is_smtp_active')->default(false)->after('reply_to_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn([
                'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password',
                'smtp_encryption',
                'from_email',
                'from_name',
                'reply_to_email',
                'reply_to_name',
                'is_smtp_active',
            ]);
        });
    }
};