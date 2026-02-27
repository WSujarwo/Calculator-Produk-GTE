<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ejm_special_materials', function (Blueprint $table) {
            // Ini akan menghasilkan BIGINT UNSIGNED
            $table->id(); 
            $table->string('component', 100)->index();
            $table->string('material', 150)->nullable()->index();
            $table->decimal('thk_mm', 10, 3)->nullable();
            $table->unsignedInteger('ply')->nullable();
            $table->string('size_in', 30)->nullable()->index();
            $table->string('sch', 30)->nullable()->index();
            $table->string('type', 50)->nullable()->index();
            $table->string('part_number', 100)->nullable()->index();
            $table->text('description')->nullable();
            $table->string('naming', 200)->nullable();
            $table->string('code1', 50)->nullable();
            $table->string('code2', 50)->nullable();
            $table->string('code3', 50)->nullable();
            $table->string('thk_text', 50)->nullable();
            $table->string('quality', 50)->nullable();
            $table->decimal('price_sqm', 18, 4)->nullable();
            $table->decimal('price_kg', 18, 4)->nullable();
            $table->decimal('price_gram', 18, 6)->nullable();
            $table->decimal('weight_gr', 18, 4)->nullable();
            $table->decimal('length_m', 18, 6)->nullable();
            $table->decimal('weight_per_meter_gr', 18, 4)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            // Natural Key Unique
            $table->unique(
                ['component', 'material', 'thk_mm', 'ply', 'size_in', 'sch', 'type'],
                'uq_ejm_special_materials_natural'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejm_special_materials');
    }
};