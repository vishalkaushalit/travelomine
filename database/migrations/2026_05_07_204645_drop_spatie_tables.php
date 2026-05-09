<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop Spatie tables in correct order (respect foreign keys)
        // Schema::dropIfExists('model_has_permissions');
        // Schema::dropIfExists('model_has_roles');
        // Schema::dropIfExists('role_has_permissions');
        // Schema::dropIfExists('permissions');
        // Schema::dropIfExists('roles');
    }

    public function down()
    {
    }
};