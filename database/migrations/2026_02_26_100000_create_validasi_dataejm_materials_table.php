<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('validasi_dataejm_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_role', 30)->index(); // BELLOW, PIPE_NIPPLE, FLANGE, COLLAR
            $table->string('material_name', 120)->nullable();
            $table->decimal('thk_mm', 10, 3)->nullable();
            $table->unsignedSmallInteger('jumlah_ply')->nullable();
            $table->string('size_in', 30)->nullable();
            $table->string('sch', 30)->nullable();
            $table->string('type', 50)->nullable();

            $table->unsignedBigInteger('material_id')->nullable()->index(); // link ke master materials
            $table->string('part_number', 120)->nullable();
            $table->text('description')->nullable();
            $table->string('naming', 255)->nullable();
            $table->string('quality', 120)->nullable();
            $table->decimal('price_sqm', 18, 4)->nullable();
            $table->decimal('price_kg', 18, 4)->nullable();
            $table->decimal('price_gram', 18, 6)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['material_role', 'is_active'], 'idx_vdem_role_active');
            $table->unique(
                ['material_role', 'material_name', 'thk_mm', 'jumlah_ply', 'size_in', 'sch', 'type'],
                'uk_vdem_key'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_dataejm_materials');
    }
};
