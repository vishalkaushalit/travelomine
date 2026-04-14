<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flight_segments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
    $table->string('from_airport');
    $table->string('to_airport');
    $table->date('departure_date');
    $table->string('pnr')->nullable();
    $table->string('airline_code');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_segments');
    }
};
