<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataValidasiEjmActualDesainCalculationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [

            [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 4,
                'nb' => 100,
                'tl_width' => 40,
                'tl_qty' => 2,
                'tl_total' => 80,
                'spacer_width' => 22.2,
                'spacer_qty' => 14,
                'spacer_total' => 310.8,
                'pitch_ejma' => 12.20,
                'pitch_gte' => 12.00,
                'tool_radius_qty' => 13,
                'tool_radius_total' => 156.00,
                'tl_spacer_tool_total' => 546.80,
                'gap' => 10,
                'can_length' => 556.80,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 5,
                'nb' => 125,
                'tl_width' => 40,
                'tl_qty' => 2,
                'tl_total' => 80,
                'spacer_width' => 22.0,
                'spacer_qty' => 14,
                'spacer_total' => 308.0,
                'pitch_ejma' => 12.90,
                'pitch_gte' => 12.60,
                'tool_radius_qty' => 13,
                'tool_radius_total' => 163.80,
                'tl_spacer_tool_total' => 551.80,
                'gap' => 10,
                'can_length' => 561.80,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 6,
                'nb' => 150,
                'tl_width' => 40,
                'tl_qty' => 2,
                'tl_total' => 80,
                'spacer_width' => 25.2,
                'spacer_qty' => 14,
                'spacer_total' => 352.8,
                'pitch_ejma' => 14.70,
                'pitch_gte' => 14.15,
                'tool_radius_qty' => 13,
                'tool_radius_total' => 183.95,
                'tl_spacer_tool_total' => 616.75,
                'gap' => 10,
                'can_length' => 626.75,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 8,
                'nb' => 200,
                'tl_width' => 40,
                'tl_qty' => 2,
                'tl_total' => 80,
                'spacer_width' => 29.6,
                'spacer_qty' => 14,
                'spacer_total' => 414.4,
                'pitch_ejma' => 17.90,
                'pitch_gte' => 21.40,
                'tool_radius_qty' => 13,
                'tool_radius_total' => 278.20,
                'tl_spacer_tool_total' => 772.60,
                'gap' => 10,
                'can_length' => 782.60,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===============================
            // Tambahan ukuran 10 - 32
            // ===============================

            [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 10,
                'nb' => 250,
                'tl_width' => 60,
                'tl_qty' => 2,
                'tl_total' => 120,
                'spacer_width' => 32.3,
                'spacer_qty' => 13,
                'spacer_total' => 419.9,
                'pitch_ejma' => 19.30,
                'pitch_gte' => 19.60,
                'tool_radius_qty' => 12,
                'tool_radius_total' => 235.20,
                'tl_spacer_tool_total' => 775.10,
                'gap' => 10,
                'can_length' => 785.10,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 12,
                'nb' => 300,
                'tl_width' => 60,
                'tl_qty' => 2,
                'tl_total' => 120,
                'spacer_width' => 37.8,
                'spacer_qty' => 11,
                'spacer_total' => 415.8,
                'pitch_ejma' => 23.40,
                'pitch_gte' => 23.00,
                'tool_radius_qty' => 10,
                'tool_radius_total' => 230.00,
                'tl_spacer_tool_total' => 765.80,
                'gap' => 10,
                'can_length' => 775.80,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 14,
                'nb' => 350,
                'tl_width' => 60,
                'tl_qty' => 2,
                'tl_total' => 120,
                'spacer_width' => 38.8,
                'spacer_qty' => 11,
                'spacer_total' => 426.8,
                'pitch_ejma' => 23.40,
                'pitch_gte' => 23.40,
                'tool_radius_qty' => 10,
                'tool_radius_total' => 234.00,
                'tl_spacer_tool_total' => 780.80,
                'gap' => 10,
                'can_length' => 790.80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
                        [
                'product_id' => 1,
                'shape_id' => 1,
                'product_shapes_id' => 1,
                'size_inch' => 16,
                'nb' => 400,
                'tl_width' => 60,
                'tl_qty' => 2,
                'tl_total' => 120,
                'spacer_width' => 39.8,
                'spacer_qty' => 11,
                'spacer_total' => 437.8,
                'pitch_ejma' => 23.40,
                'pitch_gte' => 22.60,
                'tool_radius_qty' => 10,
                'tool_radius_total' => 226.00,
                'tl_spacer_tool_total' => 783.80,
                'gap' => 10,
                'can_length' => 790.80,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 16 - 32 bisa lanjut dengan pola yang sama
        ];

        foreach ($rows as $row) {
            DB::table('validasi_dataejm_can_length_calculations')->updateOrInsert(
                ['nb' => $row['nb']],
                array_merge($row, ['updated_at' => now()])
            );
        }
    }
}
