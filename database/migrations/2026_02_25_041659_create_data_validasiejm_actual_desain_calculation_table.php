<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('data_validasiejm_actual_desain_calculation')) {
            return;
        }

        Schema::create('data_validasiejm_actual_desain_calculation', function (Blueprint $table) {
            $table->id();

            // Relasi konteks data (sesuai request)
            $table->unsignedBigInteger('product_id');         // default di seeder: 1 (EJM)
            $table->unsignedBigInteger('shape_id');           // default di seeder: 1 (circular)
            $table->unsignedBigInteger('product_shapes_id');  // default di seeder: 1 (Product EJM + Shape Circular)

            // Size
            $table->unsignedSmallInteger('size_inch');        // 4,5,6,...32
            $table->unsignedSmallInteger('nb');               // 100,125,...800

            // TL
            $table->decimal('tl_width', 10, 2);               // 40 / 60
            $table->unsignedSmallInteger('tl_qty');           // 2
            $table->decimal('tl_total', 12, 2);               // 80 / 120

            // Spacer
            $table->decimal('spacer_width', 10, 2);           // 22.2 dst
            $table->unsignedSmallInteger('spacer_qty');       // 14/13/11/10 dst
            $table->decimal('spacer_total', 12, 2);           // 310.8 dst

            // Tool Radius (pitch * qty)
            $table->decimal('pitch_ejma', 10, 2);             // 12.20 dst
            $table->decimal('pitch_gte', 10, 2);              // 12.00 dst
            $table->unsignedSmallInteger('tool_radius_qty');  // 13/12/10 dst
            $table->decimal('tool_radius_total', 12, 2);      // 156.00 dst

            // Result
            $table->decimal('tl_spacer_tool_total', 12, 2);   // TL + Spacer + Tool Radius
            $table->decimal('gap', 10, 2);                    // 10
            $table->decimal('can_length', 12, 2);             // final CAN Length

            // optional (kalau mau)
            $table->boolean('is_active')->default(true);
            $table->string('notes', 255)->nullable();

            // LOG (opsional, lihat penjelasan di bawah)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Index biar cepat untuk lookup validasi
            $table->unique(['product_shapes_id', 'size_inch'], 'uk_pshape_size_inch');
            $table->index(['product_id', 'shape_id'], 'idx_prod_shape');
            $table->index(['nb'], 'idx_nb');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('data_validasiejm_actual_desain_calculation')) {
            Schema::drop('data_validasiejm_actual_desain_calculation');
        }
    }
};
