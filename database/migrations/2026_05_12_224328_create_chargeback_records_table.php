<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chargeback_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // support agent
            $table->string('status'); // Alert, RDR, Retrieval, Chargeback, Refund, Resolved
            $table->string('time_remaining')->nullable(); // e.g. "48:00" only when status=Alert
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('booking_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chargeback_records');
    }
};