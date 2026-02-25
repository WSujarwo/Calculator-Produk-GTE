<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_data_density_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('type_code', 120);
            $table->string('size_code', 20);
            $table->decimal('density', 10, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type_code', 'size_code'], 'uk_master_data_density_gpp');
            $table->index(['type_code', 'size_code'], 'idx_master_data_density_gpp');
        });

        Schema::create('durasi_proses_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('mesin_code', 20);
            $table->string('size_code', 20);
            $table->decimal('spare_panjang_m', 10, 4)->default(0.5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['mesin_code', 'size_code'], 'uk_durasi_proses_gpp');
            $table->index(['mesin_code', 'size_code'], 'idx_durasi_proses_gpp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('durasi_proses_gpp');
        Schema::dropIfExists('master_data_density_gpp');
    }
};
