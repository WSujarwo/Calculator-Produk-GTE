<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DurasiProsesValidationGppSeeder extends Seeder
{
    private function decodeRows(string $json): array
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if ($decoded === null) {
            return [];
        }

        $isAssoc = array_keys($decoded) !== range(0, count($decoded) - 1);

        return $isAssoc ? [$decoded] : $decoded;
    }

    public function run(): void
    {
        $now = now();

        $bobbinJson = <<<'JSON'
[{"ply":1,"berat_kg":0.75,"panjang_m":360,"waktu_dtk":1500},{"ply":2,"berat_kg":0.75,"panjang_m":180,"waktu_dtk":750},{"ply":3,"berat_kg":0.75,"panjang_m":120,"waktu_dtk":499.99999999999994},{"ply":4,"berat_kg":0.75,"panjang_m":90,"waktu_dtk":374.99999999999994},{"ply":5,"berat_kg":0.75,"panjang_m":72,"waktu_dtk":300},{"ply":6,"berat_kg":0.75,"panjang_m":60,"waktu_dtk":250.00000000000003},{"ply":7,"berat_kg":0.75,"panjang_m":51.428571428571431,"waktu_dtk":214.28571428571433},{"ply":8,"berat_kg":0.75,"panjang_m":45,"waktu_dtk":187.50000000000003}]
JSON;

        $faktorJson = <<<'JSON'
[{"mesin_code":"08","diagonal_sudut":1.8,"diagonal_tengah":1.8,"corner":1,"core":1,"inner":1,"core_cord":1},{"mesin_code":"18","diagonal_sudut":1.8,"diagonal_tengah":1.8,"corner":1,"core":1,"inner":1,"core_cord":1},{"mesin_code":"24","diagonal_sudut":1.8,"diagonal_tengah":1.8,"corner":1,"core":1,"inner":1,"core_cord":1},{"mesin_code":"32","diagonal_sudut":1.8,"diagonal_tengah":1.8,"corner":1,"core":1,"inner":1,"core_cord":1}]
JSON;

        $biayaGulungJson = <<<'JSON'
[{"proses":"Gulung","biaya_per_jam":6300,"biaya_per_detik":1.75}]
JSON;

        $turunanJson = <<<'JSON'
[{"mesin_code":"08","panjang_m":2,"waktu_dtk":600},{"mesin_code":"18","panjang_m":3,"waktu_dtk":900},{"mesin_code":"24","panjang_m":4,"waktu_dtk":1200},{"mesin_code":"32","panjang_m":4,"waktu_dtk":900}]
JSON;

        $braidingJson = <<<'JSON'
[{"mesin_code":"08","panjang_m":1,"waktu_dtk":300,"biaya_per_jam":18200,"biaya_per_detik":5.0555555555555554},{"mesin_code":"18","panjang_m":1,"waktu_dtk":300,"biaya_per_jam":24900,"biaya_per_detik":6.916666666666667},{"mesin_code":"24","panjang_m":1,"waktu_dtk":300,"biaya_per_jam":29500,"biaya_per_detik":8.1944444444444446},{"mesin_code":"32","panjang_m":1,"waktu_dtk":225,"biaya_per_jam":34400,"biaya_per_detik":9.5555555555555554}]
JSON;

        $gpJson = <<<'JSON'
[{"proses":"Press","panjang_m":1,"durasi_dtk":150,"biaya_per_jam":13000,"biaya_per_detik":3.6111111111111112},{"proses":"Gulung","panjang_m":1,"durasi_dtk":150,"biaya_per_jam":6300,"biaya_per_detik":1.75},{"proses":"Packing Box","panjang_m":1,"durasi_dtk":120,"biaya_per_jam":null,"biaya_per_detik":null}]
JSON;

        $rateJson = <<<'JSON'
[{"man_power":1,"rate_per_detik":8.98,"spare_rm":1.3}]
JSON;

        $dropdownFullJson = <<<'JSON'
[{"mesin_code":"08","size_code":"04"},{"mesin_code":"08","size_code":"06"},{"mesin_code":"08","size_code":"08"},{"mesin_code":"18","size_code":"06"},{"mesin_code":"18","size_code":"08"},{"mesin_code":"18","size_code":"10"},{"mesin_code":"18","size_code":"12"},{"mesin_code":"24","size_code":"08"},{"mesin_code":"24","size_code":"10"},{"mesin_code":"24","size_code":"12"},{"mesin_code":"24","size_code":"14"},{"mesin_code":"24","size_code":"16"},{"mesin_code":"24","size_code":"19"},{"mesin_code":"24","size_code":"22"},{"mesin_code":"24","size_code":"25"},{"mesin_code":"32","size_code":"16"},{"mesin_code":"32","size_code":"19"},{"mesin_code":"32","size_code":"22"},{"mesin_code":"32","size_code":"25"}]
JSON;

        $dropdownDefaultJson = <<<'JSON'
[{"mesin_code":"08","size_code":"08"},{"mesin_code":"18","size_code":"10"},{"mesin_code":"24","size_code":"12"},{"mesin_code":"32","size_code":"14"}]
JSON;

        $spareJson = <<<'JSON'
[{"mesin_code":"08","spare_m":0.5},{"mesin_code":"18","spare_m":0.5},{"mesin_code":"24","spare_m":0.5},{"mesin_code":"32","spare_m":0.5}]
JSON;

        $withAudit = static fn (array $row) => [
            ...$row,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('durasi_proses_bobbin_full_gpp')->upsert(
            array_map($withAudit, $this->decodeRows($bobbinJson)),
            ['ply'],
            ['berat_kg', 'panjang_m', 'waktu_dtk', 'is_active', 'updated_at']
        );

        DB::table('durasi_proses_faktor_banding_gpp')->upsert(
            array_map($withAudit, $this->decodeRows($faktorJson)),
            ['mesin_code'],
            ['diagonal_sudut', 'diagonal_tengah', 'corner', 'core', 'inner', 'core_cord', 'is_active', 'updated_at']
        );

        DB::table('durasi_proses_biaya_gulung_gpp')->upsert(
            array_map($withAudit, $this->decodeRows($biayaGulungJson)),
            ['proses'],
            ['biaya_per_jam', 'biaya_per_detik', 'is_active', 'updated_at']
        );

        DB::table('durasi_proses_turunan_mesin_gpp')->upsert(
            array_map($withAudit, $this->decodeRows($turunanJson)),
            ['mesin_code'],
            ['panjang_m', 'waktu_dtk', 'is_active', 'updated_at']
        );

        DB::table('durasi_proses_braiding_per_meter_gpp')->upsert(
            array_map($withAudit, $this->decodeRows($braidingJson)),
            ['mesin_code'],
            ['panjang_m', 'waktu_dtk', 'biaya_per_jam', 'biaya_per_detik', 'is_active', 'updated_at']
        );

        DB::table('durasi_proses_gp_gpp')->upsert(
            array_map($withAudit, $this->decodeRows($gpJson)),
            ['proses'],
            ['panjang_m', 'durasi_dtk', 'biaya_per_jam', 'biaya_per_detik', 'is_active', 'updated_at']
        );

        DB::table('durasi_proses_rate_gpp')->truncate();
        DB::table('durasi_proses_rate_gpp')->insert(array_map($withAudit, $this->decodeRows($rateJson)));

        $dropdownRows = [];
        foreach ($this->decodeRows($dropdownFullJson) as $row) {
            $dropdownRows[] = $withAudit([
                'list_type' => 'full',
                'mesin_code' => $row['mesin_code'],
                'size_code' => $row['size_code'],
            ]);
        }
        foreach ($this->decodeRows($dropdownDefaultJson) as $row) {
            $dropdownRows[] = $withAudit([
                'list_type' => 'default',
                'mesin_code' => $row['mesin_code'],
                'size_code' => $row['size_code'],
            ]);
        }

        DB::table('durasi_proses_dropdown_mesin_size_gpp')->upsert(
            $dropdownRows,
            ['list_type', 'mesin_code', 'size_code'],
            ['is_active', 'updated_at']
        );

        DB::table('durasi_proses_spare_panjang_gpp')->upsert(
            array_map($withAudit, $this->decodeRows($spareJson)),
            ['mesin_code'],
            ['spare_m', 'is_active', 'updated_at']
        );
    }
}
