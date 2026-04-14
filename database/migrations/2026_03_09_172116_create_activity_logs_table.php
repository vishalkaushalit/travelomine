<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_activity_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('role')->nullable();

            $table->string('module'); // user, agent, charge, support, mis, manager
            $table->string('action'); // login, logout, create_booking, apply_charge etc
            $table->string('description');

            $table->string('subject_type')->nullable(); // Booking, Charge, User
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();

            $table->json('meta')->nullable();

            $table->timestamp('activity_at')->useCurrent();
            $table->timestamps();

            $table->index(['module', 'action']);
            $table->index(['user_id', 'activity_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
