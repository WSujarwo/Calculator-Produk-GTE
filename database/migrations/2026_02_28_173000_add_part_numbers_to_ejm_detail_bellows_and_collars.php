<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ejm_detail_bellows', function (Blueprint $table) {
            if (! Schema::hasColumn('ejm_detail_bellows', 'part_number_plate')) {
                $table->string('part_number_plate', 120)->nullable()->after('total_time_minute');
            }
            if (! Schema::hasColumn('ejm_detail_bellows', 'description_plate')) {
                $table->text('description_plate')->nullable()->after('part_number_plate');
            }
            if (! Schema::hasColumn('ejm_detail_bellows', 'part_number_tube')) {
                $table->string('part_number_tube', 120)->nullable()->after('description_plate');
            }
            if (! Schema::hasColumn('ejm_detail_bellows', 'description_tube')) {
                $table->text('description_tube')->nullable()->after('part_number_tube');
            }
            if (! Schema::hasColumn('ejm_detail_bellows', 'part_number_bellows')) {
                $table->string('part_number_bellows', 120)->nullable()->after('description_tube');
            }
            if (! Schema::hasColumn('ejm_detail_bellows', 'description_bellows')) {
                $table->text('description_bellows')->nullable()->after('part_number_bellows');
            }
        });

        Schema::table('ejm_detail_collars', function (Blueprint $table) {
            if (! Schema::hasColumn('ejm_detail_collars', 'part_number_plate')) {
                $table->string('part_number_plate', 120)->nullable()->after('total_time_minute');
            }
            if (! Schema::hasColumn('ejm_detail_collars', 'description_plate')) {
                $table->text('description_plate')->nullable()->after('part_number_plate');
            }
            if (! Schema::hasColumn('ejm_detail_collars', 'part_number_collar')) {
                $table->string('part_number_collar', 120)->nullable()->after('description_plate');
            }
            if (! Schema::hasColumn('ejm_detail_collars', 'description_collar')) {
                $table->text('description_collar')->nullable()->after('part_number_collar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ejm_detail_bellows', function (Blueprint $table) {
            foreach ([
                'part_number_plate',
                'description_plate',
                'part_number_tube',
                'description_tube',
                'part_number_bellows',
                'description_bellows',
            ] as $col) {
                if (Schema::hasColumn('ejm_detail_bellows', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('ejm_detail_collars', function (Blueprint $table) {
            foreach ([
                'part_number_plate',
                'description_plate',
                'part_number_collar',
                'description_collar',
            ] as $col) {
                if (Schema::hasColumn('ejm_detail_collars', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

