<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ejm_detail_bellows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pce_item_id')->constrained('pce_items')->cascadeOnDelete();

            $table->decimal('width_inner', 12, 3)->nullable();
            $table->decimal('width_outer', 12, 3)->nullable();
            $table->decimal('length_inner', 12, 3)->nullable();
            $table->decimal('length_outer', 12, 3)->nullable();
            $table->decimal('square_inner_sqm', 12, 4)->nullable();
            $table->decimal('square_outer_sqm', 12, 4)->nullable();

            $table->decimal('time_cutting_inner', 10, 2)->nullable();
            $table->decimal('time_cutting_outer', 10, 2)->nullable();
            $table->decimal('time_roll_inner', 10, 2)->nullable();
            $table->decimal('time_roll_outer', 10, 2)->nullable();
            $table->decimal('time_welding_inner', 10, 2)->nullable();
            $table->decimal('time_welding_outer', 10, 2)->nullable();
            $table->decimal('time_hydroforming_inner', 10, 2)->nullable();
            $table->decimal('time_hydroforming_outer', 10, 2)->nullable();
            $table->decimal('total_time_minute', 10, 2)->nullable();

            $table->string('raw_material', 150)->nullable();
            $table->string('raw_material_code', 120)->nullable();
            $table->decimal('raw_material_price_sqm', 15, 4)->nullable();
            $table->decimal('cost_raw_material', 15, 2)->nullable();

            $table->decimal('machine_rate_per_minute', 12, 2)->nullable();
            $table->decimal('machine_cost', 15, 2)->nullable();
            $table->decimal('total_cost_raw', 15, 2)->nullable();

            $table->decimal('partner_hour_rate', 12, 2)->nullable();
            $table->decimal('manpower_qty', 8, 2)->nullable();
            $table->decimal('total_cost_manpower', 15, 2)->nullable();

            $table->decimal('total_price', 15, 2)->nullable();
            $table->timestamps();

            $table->unique('pce_item_id', 'uq_ejm_detail_bellows_pce_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejm_detail_bellows');
    }
};

