<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ejm_detail_tubes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pce_item_id')->constrained('pce_items')->cascadeOnDelete();

            $table->string('nama_barang', 150)->nullable();
            $table->string('part_number_plate', 120)->nullable();
            $table->text('description_plate')->nullable();

            $table->decimal('mesin_roll_minute', 10, 2)->nullable();
            $table->decimal('seam_welding_minute', 10, 2)->nullable();
            $table->decimal('welding_machine_minute', 10, 2)->nullable();
            $table->decimal('welding_rod_minute', 10, 2)->nullable();
            $table->decimal('manpower', 10, 2)->nullable();
            $table->decimal('penetrant', 10, 2)->nullable();

            $table->decimal('rate_mesin_roll', 12, 2)->nullable();
            $table->decimal('rate_seam_welding', 12, 2)->nullable();
            $table->decimal('rate_welding_machine', 12, 2)->nullable();
            $table->decimal('rate_welding_rod', 12, 2)->nullable();

            $table->decimal('harga_material', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();

            $table->string('part_number', 120)->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique('pce_item_id', 'uq_ejm_detail_tubes_pce_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejm_detail_tubes');
    }
};
