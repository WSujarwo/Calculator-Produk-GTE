<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('durasi_proses_bobbin_full_gpp', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('ply')->unique();
            $table->decimal('berat_kg', 10, 4);
            $table->decimal('panjang_m', 12, 6);
            $table->decimal('waktu_dtk', 12, 6);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('durasi_proses_faktor_banding_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('mesin_code', 20)->unique();
            $table->decimal('diagonal_sudut', 10, 4);
            $table->decimal('diagonal_tengah', 10, 4);
            $table->decimal('corner', 10, 4);
            $table->decimal('core', 10, 4);
            $table->decimal('inner', 10, 4);
            $table->decimal('core_cord', 10, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('durasi_proses_biaya_gulung_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('proses', 60)->unique();
            $table->decimal('biaya_per_jam', 14, 4)->nullable();
            $table->decimal('biaya_per_detik', 14, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('durasi_proses_turunan_mesin_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('mesin_code', 20)->unique();
            $table->decimal('panjang_m', 10, 4);
            $table->decimal('waktu_dtk', 12, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('durasi_proses_braiding_per_meter_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('mesin_code', 20)->unique();
            $table->decimal('panjang_m', 10, 4);
            $table->decimal('waktu_dtk', 12, 4);
            $table->decimal('biaya_per_jam', 14, 4)->nullable();
            $table->decimal('biaya_per_detik', 14, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('durasi_proses_gp_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('proses', 60)->unique();
            $table->decimal('panjang_m', 10, 4)->nullable();
            $table->decimal('durasi_dtk', 12, 4)->nullable();
            $table->decimal('biaya_per_jam', 14, 4)->nullable();
            $table->decimal('biaya_per_detik', 14, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('durasi_proses_rate_gpp', function (Blueprint $table) {
            $table->id();
            $table->decimal('man_power', 10, 4)->nullable();
            $table->decimal('rate_per_detik', 12, 6)->nullable();
            $table->decimal('spare_rm', 10, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('durasi_proses_dropdown_mesin_size_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('list_type', 20);
            $table->string('mesin_code', 20);
            $table->string('size_code', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['list_type', 'mesin_code', 'size_code'], 'uk_durasi_proses_dropdown_mesin_size_gpp');
            $table->index(['mesin_code', 'size_code'], 'idx_durasi_proses_dropdown_mesin_size_gpp');
        });

        Schema::create('durasi_proses_spare_panjang_gpp', function (Blueprint $table) {
            $table->id();
            $table->string('mesin_code', 20)->unique();
            $table->decimal('spare_m', 10, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('durasi_proses_spare_panjang_gpp');
        Schema::dropIfExists('durasi_proses_dropdown_mesin_size_gpp');
        Schema::dropIfExists('durasi_proses_rate_gpp');
        Schema::dropIfExists('durasi_proses_gp_gpp');
        Schema::dropIfExists('durasi_proses_braiding_per_meter_gpp');
        Schema::dropIfExists('durasi_proses_turunan_mesin_gpp');
        Schema::dropIfExists('durasi_proses_biaya_gulung_gpp');
        Schema::dropIfExists('durasi_proses_faktor_banding_gpp');
        Schema::dropIfExists('durasi_proses_bobbin_full_gpp');
    }
};
