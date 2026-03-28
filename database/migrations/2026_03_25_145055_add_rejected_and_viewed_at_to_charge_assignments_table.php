<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectedAndViewedAtToChargeAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charge_assignments', function (Blueprint $table) {
            // Add rejected_at column after accepted_at
            if (!Schema::hasColumn('charge_assignments', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            }
            
            // Add viewed_at column after rejected_at
            if (!Schema::hasColumn('charge_assignments', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('rejected_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charge_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('charge_assignments', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            
            if (Schema::hasColumn('charge_assignments', 'viewed_at')) {
                $table->dropColumn('viewed_at');
            }
        });
    }
}