<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pce_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('pce_header_id');

            // nomor urut item di dalam 1 PCE (penting untuk UI / export / extractor)
            $table->unsignedSmallInteger('line_no')->default(1);
            $table->unsignedInteger('quantity')->default(1);

            // ===== INPUT =====
            $table->unsignedBigInteger('shape_id')->nullable();
            $table->unsignedBigInteger('type_config_id')->nullable(); // product_type_configs.id
            $table->unsignedInteger('size_nb')->nullable(); // dropdown dari validasi NB
            $table->unsignedInteger('noc')->nullable();
            $table->unsignedBigInteger('validation_id')->nullable(); // validasi_dataejm_expansion_joint.id

            // ===== AUTO (hasil lookup standard) =====
            $table->decimal('id_mm', 10, 3)->nullable();
            $table->decimal('od_mm', 10, 3)->nullable();
            $table->decimal('thk_mm', 10, 3)->nullable();
            $table->decimal('ply', 8, 2)->nullable();

            // ===== MATERIAL INPUT =====
            $table->unsignedBigInteger('material_bellow_id')->nullable();
            $table->unsignedBigInteger('material_flange_id')->nullable();
            $table->unsignedBigInteger('material_pipe_end_id')->nullable();

            // item-level status (opsional, tapi berguna)
            $table->string('status', 30)->default('DRAFT');

            $table->timestamps();

            // indexes
            $table->unique(['pce_header_id', 'line_no'], 'uk_pcei_hdr_line');
            $table->index('pce_header_id', 'idx_pcei_hdr');
            $table->index(['shape_id', 'type_config_id', 'size_nb'], 'idx_pcei_sts');
            $table->index('validation_id', 'idx_pcei_validation');
            $table->index('status', 'idx_pcei_status');

            // FK
            $table->foreign('pce_header_id', 'fk_pcei_hdr')
                ->references('id')->on('pce_headers')
                ->cascadeOnDelete();

            $table->foreign('shape_id', 'fk_pcei_shape')
                ->references('id')->on('shapes')
                ->nullOnDelete();

            $table->foreign('type_config_id', 'fk_pcei_type')
                ->references('id')->on('product_type_configs')
                ->nullOnDelete();

            $table->foreign('validation_id', 'fk_pcei_validation')
                ->references('id')->on('validasi_dataejm_expansion_joint')
                ->nullOnDelete();

            $table->foreign('material_bellow_id', 'fk_pcei_mb')
                ->references('id')->on('materials')
                ->nullOnDelete();

            $table->foreign('material_flange_id', 'fk_pcei_mf')
                ->references('id')->on('materials')
                ->nullOnDelete();

            $table->foreign('material_pipe_end_id', 'fk_pcei_mpe')
                ->references('id')->on('materials')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pce_items');
    }
};
