<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('validasi_dataejm_expansion_joint', function (Blueprint $table) {
            $table->id();

            // ===== Optional: versioning (kalau kamu punya tabel versioning) =====
            // $table->foreignId('standard_version_id')->nullable()->constrained('standard_versions');
            $table->unsignedBigInteger('standard_version_id')->nullable()->index();

            // ===== Identitas standar =====
            $table->string('shape_code', 30)->default('RND')->index(); // RND / SQ (atau lainnya)
            $table->string('size_code', 80)->nullable()->index();     // contoh: RND_NB125 / SQ_200x200 (bebas format)

            // ===== Dimensi ukuran (ikuti kebutuhan RND vs SQ) =====
            $table->unsignedInteger('nb')->nullable()->index();        // untuk rounded (NB)
            $table->decimal('width_mm', 10, 2)->nullable()->index();   // untuk square (W)
            $table->decimal('length_mm', 10, 2)->nullable()->index();  // untuk square (L)

            // ===== Actual Detail Calculation (komponen rumus) =====
            $table->decimal('tl_per_side_mm', 10, 2)->nullable();      // TL (per sisi)
            $table->unsignedSmallInteger('tl_qty')->default(2);        // biasanya 2 sisi
            $table->decimal('spacer_width_mm', 10, 2)->nullable();     // spacer width
            $table->unsignedSmallInteger('spacer_qty')->nullable();    // contoh 14
            $table->decimal('tool_radius_mm', 10, 2)->nullable();      // tool radius
            $table->unsignedSmallInteger('tool_radius_qty')->nullable();// contoh 13

            // Pitch standar
            $table->decimal('pitch_ejma_mm', 10, 2)->nullable();
            $table->decimal('pitch_gte_mm', 10, 2)->nullable();

            // ===== Hasil standar (opsional disimpan agar mudah audit) =====
            $table->decimal('total_tl_mm', 12, 2)->nullable();               // tl_per_side * tl_qty
            $table->decimal('total_spacer_mm', 12, 2)->nullable();           // spacer_width * spacer_qty
            $table->decimal('total_tool_radius_mm', 12, 2)->nullable();      // tool_radius * tool_radius_qty
            $table->decimal('tl_spacer_tool_total_mm', 12, 2)->nullable();   // total_tl + total_spacer + total_tool_radius

            $table->decimal('gap_mm', 10, 2)->nullable();                    // contoh 10
            $table->decimal('can_length_mm', 12, 2)->nullable();             // hasil akhir (total + gap)

            // ===== Effective period =====
            $table->date('effective_from')->nullable()->index();
            $table->date('effective_to')->nullable()->index();

            // ===== Status & Catatan =====
            $table->boolean('is_active')->default(true)->index();
            $table->string('notes', 255)->nullable();

            $table->timestamps();

            // Unik untuk mencegah dobel standar dalam 1 versi + shape + size
            $table->unique(
                ['standard_version_id', 'shape_code', 'size_code'],
                'uk_validasi_ejm_ver_shape_size'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_dataejm_expansion_joint');
    }
};