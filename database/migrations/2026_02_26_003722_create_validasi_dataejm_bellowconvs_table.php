<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validasi_dataejm_bellowconvs', function (Blueprint $table) {
            $table->id();

            // Basic identification
            $table->unsignedInteger('size'); // NB (100,125,etc)
            $table->unsignedInteger('noc');  // Number of Convolution
            $table->string('naming', 50);

            // Dimension validation data
            $table->decimal('oalb_mm', 10, 2); // Overall Length Bellows
            $table->decimal('bl_mm', 10, 2);   // Bellows Length

            $table->timestamps();

            // Index untuk performa estimator
            $table->index(['size', 'noc']);
            $table->unique(['size', 'noc']); // satu size tidak boleh duplicate noc
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_dataejm_bellowconvs');
    }
};