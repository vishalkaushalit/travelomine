<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // e.g. Travel Earth, TravelAsia
            $table->string('code', 50)->nullable();         // Short code e.g. TEARTH
            $table->string('account_number')->nullable();   // Merchant account/gateway ID
            $table->string('currency', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
