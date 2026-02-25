<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rm_yarn_per_10_gr_gpp', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_no')->unique();
            $table->string('origin_pn', 120)->nullable();
            $table->string('new_pn', 160)->unique();
            $table->string('deskripsi', 255)->nullable();
            $table->decimal('berat_gr', 10, 4)->nullable();
            $table->decimal('panjang_meter', 12, 4)->nullable();
            $table->decimal('berat_per_meter_gr', 12, 6)->nullable();
            $table->string('lookup_key', 160)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('origin_pn', 'idx_rm_yarn_per_10_gr_gpp_origin_pn');
            $table->index('lookup_key', 'idx_rm_yarn_per_10_gr_gpp_lookup_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rm_yarn_per_10_gr_gpp');
    }
};
