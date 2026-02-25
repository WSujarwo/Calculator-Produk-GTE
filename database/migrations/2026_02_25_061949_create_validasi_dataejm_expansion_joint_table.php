<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('validasi_dataejm_expansion_joint', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('standard_version_id')->nullable()->index();
            $table->string('shape_code', 30)->default('RND')->index();
            $table->string('size_code', 80)->nullable()->index();

            // SIZE
            $table->unsignedSmallInteger('inch')->nullable()->index();
            $table->unsignedInteger('nb')->nullable()->index();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();

            // EXPANSION JOINT METAL
            $table->decimal('id_mm', 10, 2)->nullable();
            $table->decimal('od_mm', 10, 2)->nullable();
            $table->decimal('thk', 8, 2)->nullable();
            $table->decimal('ly', 8, 2)->nullable();
            $table->unsignedSmallInteger('noc')->nullable();
            $table->decimal('lc', 10, 2)->nullable();
            $table->decimal('tc', 10, 2)->nullable();
            $table->decimal('p', 10, 2)->nullable();
            $table->decimal('tr', 10, 2)->nullable();
            $table->decimal('r', 10, 2)->nullable();
            $table->decimal('oal_b', 12, 2)->nullable();
            $table->decimal('bl', 12, 2)->nullable();
            $table->decimal('tl', 12, 2)->nullable();
            $table->decimal('slc', 12, 2)->nullable();
            $table->decimal('lpe', 12, 2)->nullable();
            $table->decimal('pres', 10, 2)->nullable();
            $table->string('temp_c', 20)->nullable();
            $table->string('axial_m', 20)->nullable();
            $table->decimal('lsr_n_per', 12, 3)->nullable();
            $table->decimal('mp_ci_mpa', 12, 3)->nullable();
            $table->decimal('mp_ii_mpa', 12, 3)->nullable();
            $table->decimal('mlc', 12, 3)->nullable();
            $table->decimal('gpf', 10, 2)->nullable();
            $table->decimal('oal', 12, 2)->nullable();
            $table->decimal('al', 10, 2)->nullable();

            // CIRCUMFERENCE + RESULT
            $table->decimal('width1', 12, 2)->nullable();
            $table->decimal('width2', 12, 2)->nullable();
            $table->decimal('spare', 10, 2)->nullable();
            $table->decimal('can_length', 12, 2)->nullable();
            $table->decimal('circumference_collar', 12, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->string('notes', 255)->nullable();

            $table->timestamps();

            $table->unique(
                ['standard_version_id', 'shape_code', 'size_code'],
                'uk_validasi_ejm_ver_shape_size'
            );
            $table->index(['shape_code', 'nb'], 'idx_validasi_ejm_shape_nb');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_dataejm_expansion_joint');
    }
};
