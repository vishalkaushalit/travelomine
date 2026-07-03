<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentStatusHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_status_id')->constrained('payment_statuses')->onDelete('cascade');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('changed_by_role')->nullable(); // agent, admin, mis, chargeback, etc.
            $table->text('remarks')->nullable();
            $table->json('metadata')->nullable(); // For additional data
            $table->timestamps();
            
            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_status_histories');
    }
}