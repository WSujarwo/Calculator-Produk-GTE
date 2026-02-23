<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // =========================
        // PRODUCTS (induk)
        // =========================
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 60)->unique(); // EJM, RTI, GPP, dll
            $table->string('product_name', 120);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =========================
        // SHAPES (global)
        // =========================
        Schema::create('shapes', function (Blueprint $table) {
            $table->id();
            $table->string('shape_code', 60)->unique();   // CIRCULAR, RECTANGULAR
            $table->string('shape_name', 120);            // Circular, Rectangular
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =========================
        // PRODUCT_SHAPES (produk mana punya shape apa)
        // =========================
        Schema::create('product_shapes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('shape_id')->constrained('shapes')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'shape_id'], 'uk_product_shapes');
            $table->index(['product_id', 'shape_id'], 'idx_product_shapes');
        });

        // =========================
        // PRODUCT_TYPE_CONFIGS (type/config per product + shape)
        // shape_id dibuat nullable supaya future produk bisa punya type tanpa shape
        // =========================
        Schema::create('product_type_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('shape_id')->nullable()->constrained('shapes')->nullOnDelete();

            $table->string('type_code', 120);      // internal code
            $table->string('type_name', 200);      // display name
            $table->text('notes')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['product_id', 'shape_id', 'type_code'], 'uk_product_type_configs');
            $table->index(['product_id', 'shape_id'], 'idx_product_type_configs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_type_configs');
        Schema::dropIfExists('product_shapes');
        Schema::dropIfExists('shapes');
        Schema::dropIfExists('products');
    }
};