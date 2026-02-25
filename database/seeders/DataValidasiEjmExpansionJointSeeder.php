<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataValidasiEjmExpansionJointSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['inch' => 4, 'nb' => 100, 'id_mm' => 114, 'od_mm' => 141, 'thk' => 0.5, 'ly' => 2, 'noc' => 14, 'lc' => 25, 'tc' => 1.5, 'p' => 12.2, 'tr' => 2.54, 'r' => 3.54, 'oal_b' => 220, 'bl' => 170, 'tl' => 40, 'slc' => 6.42, 'lpe' => 69, 'pres' => 1, 'temp_c' => '450', 'axial_m' => '±20', 'lsr_n_per' => 152, 'mp_ci_mpa' => 1.13, 'mp_ii_mpa' => 1.96, 'mlc' => 2.577, 'gpf' => 8, 'oal' => 324, 'al' => 5.6, 'width1' => 356.3, 'width2' => 361.0, 'spare' => 5, 'can_length' => 556.8, 'circumference_collar' => 366.3],
            ['inch' => 5, 'nb' => 125, 'id_mm' => 141, 'od_mm' => 168, 'thk' => 0.5, 'ly' => 2, 'noc' => 14, 'lc' => 25, 'tc' => 1.5, 'p' => 12.9, 'tr' => 2.72, 'r' => 3.72, 'oal_b' => 230, 'bl' => 180, 'tl' => 40, 'slc' => 6.06, 'lpe' => 72, 'pres' => 1, 'temp_c' => '450', 'axial_m' => '±20', 'lsr_n_per' => 240, 'mp_ci_mpa' => 1.29, 'mp_ii_mpa' => 1.93, 'mlc' => 2.578, 'gpf' => 8, 'oal' => 340, 'al' => 5.8, 'width1' => 441.11, 'width2' => 446.0, 'spare' => 5, 'can_length' => 561.8, 'circumference_collar' => 451.1],
            ['inch' => 6, 'nb' => 150, 'id_mm' => 168, 'od_mm' => 199, 'thk' => 0.5, 'ly' => 2, 'noc' => 14, 'lc' => 25, 'tc' => 1.5, 'p' => 14.7, 'tr' => 3.17, 'r' => 4.17, 'oal_b' => 255, 'bl' => 205, 'tl' => 40, 'slc' => 7.16, 'lpe' => 75, 'pres' => 0.8, 'temp_c' => '450', 'axial_m' => '±20', 'lsr_n_per' => 204, 'mp_ci_mpa' => 0.88, 'mp_ii_mpa' => 1.49, 'mlc' => 9.462, 'gpf' => 8, 'oal' => 371, 'al' => 6.6, 'width1' => 525.93, 'width2' => 531.0, 'spare' => 5, 'can_length' => 626.75, 'circumference_collar' => 535.9],
        ];

        foreach ($rows as $row) {
            $sizeCode = 'RND_NB' . $row['nb'];

            DB::table('validasi_dataejm_expansion_joint')->updateOrInsert(
                ['shape_code' => 'RND', 'size_code' => $sizeCode],
                array_merge($row, [
                    'standard_version_id' => 1,
                    'shape_code' => 'RND',
                    'size_code' => $sizeCode,
                    'is_active' => 1,
                    'notes' => 'Seed from EJM expansion joint master',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
