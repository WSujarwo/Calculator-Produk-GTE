<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ejm_detail_flanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pce_item_id')->constrained('pce_items')->cascadeOnDelete();

            $table->string('left_material', 150)->nullable();
            $table->string('left_class', 50)->nullable();
            $table->string('left_type', 50)->nullable();
            $table->string('left_part_number', 120)->nullable();
            $table->text('left_description')->nullable();
            $table->decimal('left_qty', 8, 2)->nullable();

            $table->string('right_material', 150)->nullable();
            $table->string('right_class', 50)->nullable();
            $table->string('right_type', 50)->nullable();
            $table->string('right_part_number', 120)->nullable();
            $table->text('right_description')->nullable();
            $table->decimal('right_qty', 8, 2)->nullable();

            $table->decimal('left_flange_price', 15, 2)->nullable();
            $table->decimal('left_grinding_painting', 15, 2)->nullable();
            $table->decimal('left_total', 15, 2)->nullable();

            $table->decimal('right_flange_price', 15, 2)->nullable();
            $table->decimal('right_grinding_painting', 15, 2)->nullable();
            $table->decimal('right_total', 15, 2)->nullable();

            $table->decimal('rate_per_hour', 12, 2)->nullable();
            $table->decimal('manpower_qty', 8, 2)->nullable();
            $table->decimal('total_cost_manpower', 15, 2)->nullable();

            $table->decimal('total_price', 15, 2)->nullable();
            $table->timestamps();

            $table->unique('pce_item_id', 'uq_ejm_detail_flanges_pce_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejm_detail_flanges');
    }
};

