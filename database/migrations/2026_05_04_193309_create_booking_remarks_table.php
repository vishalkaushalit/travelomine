<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->text('remark_text');
            $table->enum('remark_type', [
                'general', 
                'payment', 
                'modification', 
                'customer_request',
                'followup'
            ])->default('general');
            $table->decimal('amount_changed', 10, 2)->nullable();
            $table->json('old_data')->nullable();  // Store old values for modifications
            $table->json('new_data')->nullable();  // Store new values
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_remarks');
    }
};