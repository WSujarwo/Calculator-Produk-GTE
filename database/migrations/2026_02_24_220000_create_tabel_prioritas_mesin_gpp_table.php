<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tabel_prioritas_mesin_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('type_code', 120);
            $table->string('mesin_code', 20);
            $table->string('size_code', 20);
            $table->unsignedTinyInteger('priority_rank');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type_code', 'mesin_code', 'size_code'], 'uk_tabel_prioritas_mesin_gpp');
            $table->index(['type_code', 'mesin_code'], 'idx_tabel_prioritas_mesin_gpp_type_mesin');
            $table->index(['mesin_code', 'size_code'], 'idx_tabel_prioritas_mesin_gpp_mesin_size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabel_prioritas_mesin_gpp');
    }
};
