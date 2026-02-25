<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataValidasiEjmExpansionJointSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['inch' => 4, 'nb' => 100, 'id' => 114, 'od' => 141, 'thk' => 0.5, 'ly' => 2, 'noc' => 14, 'p' => 12.2, 'tr' => 2.54, 'tl' => 40, 'gpf' => 8, 'oal' => 324],
            ['inch' => 5, 'nb' => 125, 'id' => 141, 'od' => 168, 'thk' => 0.5, 'ly' => 2, 'noc' => 14, 'p' => 12.9, 'tr' => 2.72, 'tl' => 40, 'gpf' => 8, 'oal' => 340],
            ['inch' => 6, 'nb' => 150, 'id' => 168, 'od' => 199, 'thk' => 0.5, 'ly' => 2, 'noc' => 14, 'p' => 14.7, 'tr' => 3.17, 'tl' => 40, 'gpf' => 8, 'oal' => 371],
            ['inch' => 8, 'nb' => 200, 'id' => 219, 'od' => 256, 'thk' => 0.5, 'ly' => 2, 'noc' => 14, 'p' => 17.9, 'tr' => 3.96, 'tl' => 40, 'gpf' => 8, 'oal' => 426],
            ['inch' => 10, 'nb' => 250, 'id' => 273, 'od' => 313, 'thk' => 0.5, 'ly' => 2, 'noc' => 13, 'p' => 19.3, 'tr' => 4.31, 'tl' => 60, 'gpf' => 8, 'oal' => 446],
            ['inch' => 12, 'nb' => 300, 'id' => 324, 'od' => 372, 'thk' => 0.8, 'ly' => 2, 'noc' => 11, 'p' => 23.4, 'tr' => 5.05, 'tl' => 60, 'gpf' => 8, 'oal' => 465],
            ['inch' => 14, 'nb' => 350, 'id' => 355, 'od' => 404, 'thk' => 0.8, 'ly' => 2, 'noc' => 11, 'p' => 23.4, 'tr' => 5.05, 'tl' => 60, 'gpf' => 8, 'oal' => 469],
            ['inch' => 16, 'nb' => 400, 'id' => 406, 'od' => 456, 'thk' => 0.8, 'ly' => 2, 'noc' => 11, 'p' => 23.4, 'tr' => 5.05, 'tl' => 60, 'gpf' => 8, 'oal' => 481],
            ['inch' => 18, 'nb' => 450, 'id' => 457, 'od' => 505, 'thk' => 0.8, 'ly' => 2, 'noc' => 11, 'p' => 23.4, 'tr' => 5.05, 'tl' => 60, 'gpf' => 8, 'oal' => 491],
            ['inch' => 20, 'nb' => 500, 'id' => 506, 'od' => 571, 'thk' => 0.8, 'ly' => 2, 'noc' => 10, 'p' => 30.0, 'tr' => 6.70, 'tl' => 60, 'gpf' => 8, 'oal' => 542],
            ['inch' => 22, 'nb' => 550, 'id' => 559, 'od' => 618, 'thk' => 0.8, 'ly' => 2, 'noc' => 10, 'p' => 30.0, 'tr' => 6.70, 'tl' => 60, 'gpf' => 8, 'oal' => 476],
            ['inch' => 24, 'nb' => 600, 'id' => 610, 'od' => 669, 'thk' => 0.8, 'ly' => 2, 'noc' => 10, 'p' => 30.0, 'tr' => 6.70, 'tl' => 60, 'gpf' => 8, 'oal' => 562],
            ['inch' => 26, 'nb' => 650, 'id' => 660, 'od' => 719, 'thk' => 0.8, 'ly' => 2, 'noc' => 10, 'p' => 30.0, 'tr' => 6.70, 'tl' => 60, 'gpf' => 8, 'oal' => 476],
            ['inch' => 28, 'nb' => 700, 'id' => 711, 'od' => 761, 'thk' => 1.0, 'ly' => 2, 'noc' => 10, 'p' => 30.0, 'tr' => 6.50, 'tl' => 60, 'gpf' => 8, 'oal' => 476],
            ['inch' => 30, 'nb' => 750, 'id' => 762, 'od' => 836, 'thk' => 1.0, 'ly' => 2, 'noc' => 10, 'p' => 30.0, 'tr' => 6.50, 'tl' => 60, 'gpf' => 8, 'oal' => 476],
            ['inch' => 32, 'nb' => 800, 'id' => 816, 'od' => 890, 'thk' => 1.0, 'ly' => 2, 'noc' => 10, 'p' => 30.0, 'tr' => 6.50, 'tl' => 60, 'gpf' => 8, 'oal' => 476],
        ];

        foreach ($rows as $row) {
            $sizeCode = 'RND_NB' . $row['nb'];
            $tlTotal = $row['tl'] * $row['ly'];
            $toolTotal = $row['tr'] * $row['noc'];

            $payload = [
                'standard_version_id' => 1,
                'shape_code' => 'RND',
                'size_code' => $sizeCode,
                'nb' => $row['nb'],
                'width_mm' => null,
                'length_mm' => null,
                'tl_per_side_mm' => number_format((float) $row['tl'], 2, '.', ''),
                'tl_qty' => $row['ly'],
                'spacer_width_mm' => null,
                'spacer_qty' => null,
                'tool_radius_mm' => number_format((float) $row['tr'], 2, '.', ''),
                'tool_radius_qty' => $row['noc'],
                'pitch_ejma_mm' => number_format((float) $row['p'], 2, '.', ''),
                'pitch_gte_mm' => null,
                'total_tl_mm' => number_format((float) $tlTotal, 2, '.', ''),
                'total_spacer_mm' => null,
                'total_tool_radius_mm' => number_format((float) $toolTotal, 2, '.', ''),
                'tl_spacer_tool_total_mm' => null,
                'gap_mm' => number_format((float) $row['gpf'], 2, '.', ''),
                'can_length_mm' => number_format((float) $row['oal'], 2, '.', ''),
                'effective_from' => null,
                'effective_to' => null,
                'is_active' => true,
                'notes' => sprintf(
                    'Book1.xlsx source | inch=%s, id=%s, od=%s, thk=%s',
                    $row['inch'],
                    $row['id'],
                    $row['od'],
                    $row['thk']
                ),
                'updated_at' => now(),
            ];

            DB::table('validasi_dataejm_expansion_joint')->updateOrInsert(
                ['shape_code' => 'RND', 'size_code' => $sizeCode],
                array_merge($payload, ['created_at' => now()])
            );
        }
    }
}
