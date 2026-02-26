<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValidasiDataEjmBellowconvSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/Book1_2.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV file not found at: " . $path);
        }

        $file = fopen($path, 'r');

        $rowNumber = 0;
        $insertData = [];

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {

            $rowNumber++;

            // Skip header rows (row 1 & 2 dari file)
            if ($rowNumber <= 2) {
                continue;
            }

            // Skip empty rows
            if (empty($row[0])) {
                continue;
            }

            $insertData[] = [
                'size'     => (int) $row[0],
                'noc'      => (int) $row[1],
                'naming'   => $row[2],
                'oalb_mm'  => (float) $row[3],
                'bl_mm'    => (float) $row[4],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($file);

        // Hapus dulu jika mau clean insert
        DB::table('validasi_dataejm_bellowconvs')->truncate();

        DB::table('validasi_dataejm_bellowconvs')->insert($insertData);
    }
}