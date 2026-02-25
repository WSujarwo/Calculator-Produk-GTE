<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private string $newTable = 'validasi_dataejm_can_length_calculations';
    private string $oldTable = 'data_validasiejm_actual_desain_calculation';

    public function up(): void
    {
        if (! Schema::hasTable($this->newTable)) {
            Schema::create($this->newTable, function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('product_id')->default(1);
                $table->unsignedBigInteger('shape_id')->default(1);
                $table->unsignedBigInteger('product_shapes_id')->default(1);

                $table->unsignedSmallInteger('size_inch');
                $table->unsignedSmallInteger('nb');

                // Existing structure (compat)
                $table->decimal('tl_width', 10, 2)->nullable();
                $table->unsignedSmallInteger('tl_qty')->nullable();
                $table->decimal('tl_total', 12, 2)->nullable();
                $table->decimal('spacer_width', 10, 2)->nullable();
                $table->unsignedSmallInteger('spacer_qty')->nullable();
                $table->decimal('spacer_total', 12, 2)->nullable();
                $table->decimal('pitch_ejma', 10, 2)->nullable();
                $table->decimal('pitch_gte', 10, 2)->nullable();
                $table->unsignedSmallInteger('tool_radius_qty')->nullable();
                $table->decimal('tool_radius_total', 12, 2)->nullable();
                $table->decimal('tl_spacer_tool_total', 12, 2)->nullable();
                $table->decimal('gap', 10, 2)->nullable();
                $table->decimal('can_length', 12, 2)->nullable();

                // Additional columns based on latest EJM validation sheet
                $table->decimal('id_bellows', 12, 2)->nullable();
                $table->decimal('thk', 8, 2)->nullable();
                $table->decimal('ly', 8, 2)->nullable();
                $table->decimal('ejma_circm_1', 12, 2)->nullable();
                $table->decimal('ejma_circm_2', 12, 2)->nullable();
                $table->decimal('ejma_gap', 10, 2)->nullable();
                $table->decimal('manual_circm_1', 12, 2)->nullable();
                $table->decimal('manual_circm_2', 12, 2)->nullable();
                $table->decimal('manual_gap', 10, 2)->nullable();
                $table->decimal('correction_circm_1', 12, 2)->nullable();
                $table->decimal('correction_circm_2', 12, 2)->nullable();
                $table->decimal('correction_gap', 10, 2)->nullable();
                $table->decimal('correction_circm_2_actual', 12, 2)->nullable();
                $table->decimal('calculation_tl', 12, 2)->nullable();
                $table->decimal('can_length_actual', 12, 2)->nullable();

                $table->boolean('is_active')->default(true);
                $table->string('notes', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->unique(['product_shapes_id', 'nb'], 'uk_validasi_dataejm_pshape_nb');
                $table->index(['product_id', 'shape_id'], 'idx_vdejm_prod_shape');
                $table->index(['size_inch'], 'idx_vdejm_size');
            });
        }

        if (Schema::hasTable($this->oldTable)) {
            $existing = (int) DB::table($this->newTable)->count();
            if ($existing === 0) {
                DB::statement("
                    INSERT INTO {$this->newTable}
                    (
                        product_id, shape_id, product_shapes_id, size_inch, nb,
                        tl_width, tl_qty, tl_total, spacer_width, spacer_qty, spacer_total,
                        pitch_ejma, pitch_gte, tool_radius_qty, tool_radius_total,
                        tl_spacer_tool_total, gap, can_length,
                        is_active, notes, created_by, updated_by, created_at, updated_at
                    )
                    SELECT
                        product_id, shape_id, product_shapes_id, size_inch, nb,
                        tl_width, tl_qty, tl_total, spacer_width, spacer_qty, spacer_total,
                        pitch_ejma, pitch_gte, tool_radius_qty, tool_radius_total,
                        tl_spacer_tool_total, gap, can_length,
                        is_active, notes, created_by, updated_by, created_at, updated_at
                    FROM {$this->oldTable}
                ");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->newTable);
    }
};
