<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ejm_detail_ejms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pce_item_id')->constrained('pce_items')->cascadeOnDelete();

            $table->decimal('material_bellows', 15, 2)->nullable();
            $table->decimal('material_pipe_end', 15, 2)->nullable();
            $table->decimal('material_flange', 15, 2)->nullable();

            $table->decimal('time_assembly_minute', 12, 2)->nullable();
            $table->decimal('time_painting_minute', 12, 2)->nullable();
            $table->decimal('time_finishing_minute', 12, 2)->nullable();

            $table->decimal('manpower_rate_per_hour', 12, 2)->nullable();
            $table->decimal('total_time_hour', 12, 4)->nullable();
            $table->decimal('manpower_cost', 15, 2)->nullable();

            $table->decimal('total_bellows', 15, 2)->nullable();
            $table->decimal('total_collar', 15, 2)->nullable();
            $table->decimal('total_metal_bellows', 15, 2)->nullable();
            $table->decimal('total_pipe_end', 15, 2)->nullable();
            $table->decimal('total_flange', 15, 2)->nullable();

            $table->decimal('total', 15, 2)->nullable();
            $table->decimal('margin_percent', 8, 2)->default(0);
            $table->decimal('margin_amount', 15, 2)->nullable();
            $table->decimal('grand_total', 15, 2)->nullable();
            $table->timestamps();

            $table->unique('pce_item_id', 'uq_ejm_detail_ejms_pce_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejm_detail_ejms');
    }
};

