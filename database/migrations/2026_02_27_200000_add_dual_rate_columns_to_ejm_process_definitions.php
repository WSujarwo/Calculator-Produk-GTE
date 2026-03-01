<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('ejm_process_definitions')) {
            return;
        }

        Schema::table('ejm_process_definitions', function (Blueprint $table) {
            if (! Schema::hasColumn('ejm_process_definitions', 'rate_inner_per_hour')) {
                $table->decimal('rate_inner_per_hour', 12, 2)->nullable()->after('has_inner_outer');
            }
            if (! Schema::hasColumn('ejm_process_definitions', 'rate_outer_per_hour')) {
                $table->decimal('rate_outer_per_hour', 12, 2)->nullable()->after('rate_inner_per_hour');
            }
        });

        if (Schema::hasColumn('ejm_process_definitions', 'rate_per_hour')) {
            DB::statement('UPDATE ejm_process_definitions SET rate_inner_per_hour = COALESCE(rate_inner_per_hour, rate_per_hour), rate_outer_per_hour = COALESCE(rate_outer_per_hour, rate_per_hour)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('ejm_process_definitions')) {
            return;
        }

        Schema::table('ejm_process_definitions', function (Blueprint $table) {
            if (Schema::hasColumn('ejm_process_definitions', 'rate_outer_per_hour')) {
                $table->dropColumn('rate_outer_per_hour');
            }
            if (Schema::hasColumn('ejm_process_definitions', 'rate_inner_per_hour')) {
                $table->dropColumn('rate_inner_per_hour');
            }
        });
    }
};
