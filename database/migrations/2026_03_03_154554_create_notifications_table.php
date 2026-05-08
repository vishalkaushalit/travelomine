<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('message');
                $table->json('target_roles')->nullable();
                $table->string('target_type')->default('all');
                $table->timestamp('start_date')->nullable();
                $table->timestamp('expiry_date')->nullable();
                $table->string('priority')->default('info');
                $table->boolean('is_active')->default(true);
                $table->boolean('can_dismiss')->default(true);
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_notification_reads')) {
            Schema::create('user_notification_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notification_id')->constrained('admin_notifications')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('read_at')->nullable(); // Changed to nullable for read-log design
                $table->timestamps();
                $table->unique(['notification_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_reads');
        Schema::dropIfExists('admin_notifications');
    }
};