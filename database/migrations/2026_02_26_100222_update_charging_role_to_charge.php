<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update 'charging' to 'charge' for all existing users
        DB::table('users')
            ->where('role', 'charging')
            ->update(['role' => 'charge']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback 'charge' to 'charging' if needed
        DB::table('users')
            ->where('role', 'charge')
            ->update(['role' => 'charging']);
    }
};
