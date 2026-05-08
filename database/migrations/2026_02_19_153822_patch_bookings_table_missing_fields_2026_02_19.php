<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            // helper: add column only if missing
            $add = function(string $col) use ($table) {
                return !Schema::hasColumn('bookings', $col);
            };

            if ($add('booking_reference')) {
                $table->string('booking_reference')->nullable()->unique()->after('id');
            }

            if ($add('gk_pnr')) {
                $table->string('gk_pnr')->nullable()->after('flight_type');
            }

            if ($add('airline_pnr')) {
                $table->string('airline_pnr')->nullable()->after('gk_pnr');
            }

            if ($add('customer_name')) {
                $table->string('customer_name')->nullable()->after('agent_custom_id');
            }

            if ($add('billing_address')) {
                $table->text('billing_address')->nullable()->after('billing_phone');
            }

            if ($add('departure_city')) $table->string('departure_city')->nullable();
            if ($add('arrival_city')) $table->string('arrival_city')->nullable();
            if ($add('departure_date')) $table->date('departure_date')->nullable();
            if ($add('return_date')) $table->date('return_date')->nullable();
            if ($add('airline_name')) $table->string('airline_name')->nullable();
            if ($add('flight_number')) $table->string('flight_number')->nullable();
            if ($add('cabin_class')) $table->enum('cabin_class', ['economy','premium_economy','business','first'])->default('economy');

            if ($add('total_passengers')) $table->unsignedTinyInteger('total_passengers')->default(1);
            if ($add('adults')) $table->unsignedTinyInteger('adults')->default(1);
            if ($add('children')) $table->unsignedTinyInteger('children')->default(0);
            if ($add('infants')) $table->unsignedTinyInteger('infants')->default(0);

            if ($add('auth_email_sent_at')) $table->timestamp('auth_email_sent_at')->nullable();
            if ($add('payment_confirmed_at')) $table->timestamp('payment_confirmed_at')->nullable();
            if ($add('ticketed_at')) $table->timestamp('ticketed_at')->nullable();
            if ($add('charging_remarks')) $table->text('charging_remarks')->nullable();
        });

        // Patch legacy rows before adding constraints
        if (Schema::hasColumn('bookings', 'gk_pnr') && Schema::hasColumn('bookings', 'airline_pnr')) {
            DB::table('bookings')
                ->whereNull('gk_pnr')
                ->whereNull('airline_pnr')
                ->update(['gk_pnr' => 'LEGACY']);
        }

        // Add constraints only if not already present
        // (MySQL doesn't have a great "if not exists" for constraints, so we check via information_schema)
        $db = DB::getDatabaseName();

        $existsPnr = DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $db)
            ->where('table_name', 'bookings')
            ->where('constraint_name', 'chk_pnr_required')
            ->exists();

        if (!$existsPnr) {
            DB::statement("ALTER TABLE bookings ADD CONSTRAINT chk_pnr_required CHECK (gk_pnr IS NOT NULL OR airline_pnr IS NOT NULL)");
        }

        $existsMax = DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $db)
            ->where('table_name', 'bookings')
            ->where('constraint_name', 'chk_max_passengers')
            ->exists();

        if (!$existsMax) {
            DB::statement("ALTER TABLE bookings ADD CONSTRAINT chk_max_passengers CHECK (total_passengers <= 9)");
        }
    }

    public function down(): void
    {
        // Usually we don't drop patch columns in down() in production,
        // but you can add drop logic if you want.
    }
};
