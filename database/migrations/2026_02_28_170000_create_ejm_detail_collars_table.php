<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ejm_detail_collars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pce_item_id')->constrained('pce_items')->cascadeOnDelete();

            $table->decimal('qty_kanan_kiri', 8, 2)->nullable();
            $table->decimal('width', 12, 3)->nullable();
            $table->decimal('length', 12, 3)->nullable();
            $table->decimal('square_sqm', 12, 4)->nullable();

            $table->decimal('time_cutting_minute', 10, 2)->nullable();
            $table->decimal('time_roll_minute', 10, 2)->nullable();
            $table->decimal('time_welding_minute', 10, 2)->nullable();
            $table->decimal('total_time_minute', 10, 2)->nullable();

            $table->string('raw_material', 150)->nullable();
            $table->string('raw_material_code', 120)->nullable();
            $table->decimal('price_sqm', 15, 4)->nullable();
            $table->decimal('cost_raw_material', 15, 2)->nullable();

            $table->decimal('price_validasi_machine', 12, 2)->nullable();
            $table->decimal('cost_machine_material', 15, 2)->nullable();

            $table->decimal('rate_per_hour', 12, 2)->nullable();
            $table->decimal('quantity', 8, 2)->nullable();
            $table->decimal('total_cost_manpower', 15, 2)->nullable();

            $table->decimal('total_price', 15, 2)->nullable();
            $table->timestamps();

            $table->unique('pce_item_id', 'uq_ejm_detail_collars_pce_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejm_detail_collars');
    }
};

