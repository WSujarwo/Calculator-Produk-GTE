<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('part_number', 120)->unique();
            $table->text('description')->nullable();
            $table->string('naming', 255)->nullable();
            $table->string('code1', 120)->nullable();
            $table->string('code2', 120)->nullable();
            $table->string('code3', 120)->nullable();
            $table->string('thk', 60)->nullable();
            $table->string('quality', 120)->nullable();
            $table->decimal('price_sqm', 18, 4)->nullable();
            $table->decimal('price_kg', 18, 4)->nullable();
            $table->decimal('price_gram', 18, 6)->nullable();
            $table->decimal('berat_gr', 18, 6)->nullable();
            $table->decimal('panjang_meter', 18, 6)->nullable();
            $table->decimal('berat_per_meter_gr', 18, 6)->nullable();
            $table->timestamps();

            $table->index('quality');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
