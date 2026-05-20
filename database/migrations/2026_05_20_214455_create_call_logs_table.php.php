<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('city');
            $table->boolean('follow_up')->default(false);
            $table->text('call_detail');
            $table->text('remark')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index('agent_id');
            $table->index('follow_up');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_logs');
    }
};