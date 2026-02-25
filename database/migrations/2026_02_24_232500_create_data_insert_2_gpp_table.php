<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_insert_2_gpp', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_no')->unique();
            $table->string('component_raw', 120)->nullable();
            $table->string('component_group', 120);
            $table->string('data_value', 180);
            $table->string('code', 40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['component_group', 'code'], 'idx_data_insert_2_gpp_group_code');
            $table->index('code', 'idx_data_insert_2_gpp_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_insert_2_gpp');
    }
};
