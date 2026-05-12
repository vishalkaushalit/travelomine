<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('booking_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('assigned_by'); // agent who assigned
            $table->unsignedBigInteger('assigned_to')->nullable(); // changes team member who accepts
            $table->string('status')->default('pending'); // pending, accepted, rejected, completed
            $table->text('message'); // agent's message about required changes
            $table->text('response_message')->nullable(); // changes team response
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users');
            $table->foreign('assigned_to')->references('id')->on('users');
            
            $table->index(['booking_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_assignments');
    }
}