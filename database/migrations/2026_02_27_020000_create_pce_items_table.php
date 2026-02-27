<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pce_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Header PCE (Nomor PCE ada di pce_headers)
            $table->foreignId('pce_header_id')
                ->constrained('pce_headers')
                ->cascadeOnDelete();

            // Identitas line item
            $table->string('plat_number', 120);
            $table->text('description')->nullable();
            $table->unsignedInteger('qty')->default(1);

            // Input user (index screen)
            $table->foreignId('shape_id')->nullable()->constrained('shapes')->nullOnDelete();
            $table->foreignId('type_config_id')->nullable()->constrained('product_type_configs')->nullOnDelete();
            $table->unsignedInteger('nb')->nullable();

            // Auto dari validasi EJM berdasarkan NB
            $table->unsignedSmallInteger('noc')->nullable();
            $table->decimal('id_mm', 10, 3)->nullable();
            $table->decimal('od_mm', 10, 3)->nullable();
            $table->decimal('thk_mm', 10, 3)->nullable();
            $table->unsignedSmallInteger('ply')->nullable();

            // Material special EJM
            $table->foreignId('material_bellow_id')->nullable()->constrained('ejm_special_materials')->nullOnDelete();
            $table->foreignId('material_flange_id')->nullable()->constrained('ejm_special_materials')->nullOnDelete();
            $table->foreignId('material_pipe_end_id')->nullable()->constrained('ejm_special_materials')->nullOnDelete();

            // Opsional: simpan id baris validasi yang dipakai saat auto lookup
            $table->foreignId('expansion_joint_validation_id')->nullable()
                ->constrained('validasi_dataejm_expansion_joint')
                ->nullOnDelete();

            $table->string('status', 30)->default('DRAFT');
            $table->timestamps();

            // 1 plat number tidak boleh double dalam 1 PCE
            $table->unique(['pce_header_id', 'plat_number'], 'uq_pce_items_header_plat');

            $table->index(['pce_header_id'], 'idx_pce_items_header');
            $table->index(['shape_id', 'type_config_id', 'nb'], 'idx_pce_items_shape_type_nb');
            $table->index(['nb'], 'idx_pce_items_nb');
            $table->index(['status'], 'idx_pce_items_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pce_items');
    }
};
