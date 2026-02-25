<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_data_insert_gpp', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_no')->unique();
            $table->string('category', 120);
            $table->string('form', 60);
            $table->string('category_detail', 150);
            $table->string('special_category', 150)->nullable();
            $table->string('code_1', 40)->nullable();
            $table->string('code_2', 40)->nullable();
            $table->string('code_3', 40)->nullable();
            $table->string('code_4', 40)->nullable();
            $table->string('part_no', 150)->unique();
            $table->string('description', 255)->nullable();
            $table->string('part_no_lama_1', 150)->nullable();
            $table->string('part_no_lama_2', 150)->nullable();
            $table->string('part_no_lama_3', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'form'], 'idx_master_data_insert_gpp_category_form');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data_insert_gpp');
    }
};
