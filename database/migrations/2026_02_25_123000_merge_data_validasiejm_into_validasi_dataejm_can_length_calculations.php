<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private string $from = 'data_validasiejm_actual_desain_calculation';
    private string $to = 'validasi_dataejm_can_length_calculations';

    public function up(): void
    {
        if (! Schema::hasTable($this->from) || ! Schema::hasTable($this->to)) {
            return;
        }

        DB::statement("
            INSERT INTO {$this->to}
            (
                product_id, shape_id, product_shapes_id, size_inch, nb,
                tl_width, tl_qty, tl_total, spacer_width, spacer_qty, spacer_total,
                pitch_ejma, pitch_gte, tool_radius_qty, tool_radius_total,
                tl_spacer_tool_total, gap, can_length,
                is_active, notes, created_by, updated_by, created_at, updated_at
            )
            SELECT
                o.product_id, o.shape_id, o.product_shapes_id, o.size_inch, o.nb,
                o.tl_width, o.tl_qty, o.tl_total, o.spacer_width, o.spacer_qty, o.spacer_total,
                o.pitch_ejma, o.pitch_gte, o.tool_radius_qty, o.tool_radius_total,
                o.tl_spacer_tool_total, o.gap, o.can_length,
                o.is_active, o.notes, o.created_by, o.updated_by, o.created_at, o.updated_at
            FROM {$this->from} o
            ON DUPLICATE KEY UPDATE
                size_inch = VALUES(size_inch),
                tl_width = VALUES(tl_width),
                tl_qty = VALUES(tl_qty),
                tl_total = VALUES(tl_total),
                spacer_width = VALUES(spacer_width),
                spacer_qty = VALUES(spacer_qty),
                spacer_total = VALUES(spacer_total),
                pitch_ejma = VALUES(pitch_ejma),
                pitch_gte = VALUES(pitch_gte),
                tool_radius_qty = VALUES(tool_radius_qty),
                tool_radius_total = VALUES(tool_radius_total),
                tl_spacer_tool_total = VALUES(tl_spacer_tool_total),
                gap = VALUES(gap),
                can_length = VALUES(can_length),
                is_active = VALUES(is_active),
                notes = VALUES(notes),
                updated_by = VALUES(updated_by),
                updated_at = VALUES(updated_at)
        ");

        Schema::dropIfExists($this->from);
    }

    public function down(): void
    {
        // Intentionally left empty: source table is deprecated after merge.
    }
};
