<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ejm_detail_metal_bellows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pce_item_id')->constrained('pce_items')->cascadeOnDelete();

            $table->decimal('width', 12, 3)->nullable();
            $table->decimal('length', 12, 3)->nullable();
            $table->decimal('oal', 12, 3)->nullable();
            $table->decimal('noc', 10, 2)->nullable();

            $table->string('material', 150)->nullable();
            $table->string('part_number_bellows', 120)->nullable();
            $table->text('description_bellows')->nullable();
            $table->string('part_number_collar', 120)->nullable();
            $table->text('description_collar')->nullable();

            $table->decimal('welding_rod_qty', 12, 2)->nullable();
            $table->decimal('mesin_qty', 12, 2)->nullable();
            $table->decimal('manpower_qty', 12, 2)->nullable();
            $table->decimal('grinda_poles_qty', 12, 2)->nullable();
            $table->decimal('disc_poles_qty', 12, 2)->nullable();

            $table->decimal('harga_bellows', 15, 2)->nullable();
            $table->decimal('harga_collar', 15, 2)->nullable();

            $table->decimal('rate_welding_rod', 12, 2)->nullable();
            $table->decimal('rate_mesin', 12, 2)->nullable();
            $table->decimal('rate_manpower', 12, 2)->nullable();
            $table->decimal('rate_grinda_poles', 12, 2)->nullable();
            $table->decimal('rate_disc_poles', 12, 2)->nullable();

            $table->decimal('total', 15, 2)->nullable();
            $table->decimal('grand_total', 15, 2)->nullable();
            $table->string('part_number_metal_bellows', 120)->nullable();
            $table->text('description_metal_bellows')->nullable();

            $table->timestamps();
            $table->unique('pce_item_id', 'uq_ejm_detail_metal_bellows_pce_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejm_detail_metal_bellows');
    }
};

