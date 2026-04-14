<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_hotels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('hotel_name');
            $table->text('hotel_location')->nullable();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('room_type', 100)->nullable()
                  ->comment('e.g. Standard, Deluxe, Suite');
            $table->unsignedTinyInteger('number_of_rooms')->default(1);
            $table->decimal('hotel_cost', 10, 2)->default(0.00);
            $table->text('hotel_remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_hotels');
    }
};
