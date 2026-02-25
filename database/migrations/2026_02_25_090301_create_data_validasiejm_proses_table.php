<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_validasiejm_proses', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Primary descriptors
            $table->string('component_type', 80)->index(); // e.g. Bellows, Collar, Pipe End
            $table->string('process_name', 120)->index();  // e.g. Cutting Shearing, Rolling, Welding

            // Size & dimensions
            $table->integer('nb')->nullable()->index();

            // Tube / pricing columns (nullable because not all rows have values)
            $table->integer('tube_inner')->nullable()->comment('Tube Inner (mm) or nominal value');
            $table->decimal('price_tube_inner', 12, 2)->nullable()->comment('harga Tube Inner (currency)');

            $table->integer('tube_outer')->nullable()->comment('Tube Outer (mm) or nominal value');
            $table->decimal('price_tube_outer', 12, 2)->nullable()->comment('harga Tube Outer (currency)');

            // meta
            $table->string('unit', 20)->nullable()->default('mm');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['component_type', 'process_name', 'nb'], 'uq_component_process_nb');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_validasiejm_proses');
    }
};