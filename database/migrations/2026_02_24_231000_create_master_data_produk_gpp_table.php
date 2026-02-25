<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_data_produk_gpp', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_no')->unique();
            $table->unsignedInteger('no_urut');
            $table->string('brand', 40);
            $table->string('model_angka', 40);
            $table->string('special_code', 40);
            $table->string('model_code', 120)->unique();
            $table->string('description', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['brand', 'model_angka', 'special_code'], 'idx_master_data_produk_gpp_brand_model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data_produk_gpp');
    }
};
