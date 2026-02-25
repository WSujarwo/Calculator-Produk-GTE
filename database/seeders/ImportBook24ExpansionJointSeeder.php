<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class ImportBook24ExpansionJointSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'C:\\Users\\windows-11\\Downloads\\Book24.xlsx';
        if (! file_exists($path)) {
            $this->command?->error("File tidak ditemukan: {$path}");
            return;
        }

        $rawRows = $this->readXlsxRows($path);
        if (count($rawRows) < 2) {
            $this->command?->error('File kosong / tidak terbaca.');
            return;
        }

        $headerRowIndex = 0;
        foreach ($rawRows as $idx => $rawRow) {
            $normalized = array_map(fn ($v) => $this->normalizeHeader($v), $rawRow);
            if (in_array('inch', $normalized, true) && in_array('nb', $normalized, true)) {
                $headerRowIndex = $idx;
                break;
            }
        }

        $headers = array_map(fn ($v) => $this->normalizeHeader($v), $rawRows[$headerRowIndex] ?? []);
        $indices = $this->resolveColumnMap($headers);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rawRows, $headerRowIndex, $indices, &$created, &$updated, &$skipped) {
            foreach (array_slice($rawRows, $headerRowIndex + 1) as $row) {
                $inch = $this->toInt($this->cell($row, $indices['inch']));
                $nb = $this->toInt($this->cell($row, $indices['nb']));

                if ($inch === null && $nb === null) {
                    $skipped++;
                    continue;
                }

                $shapeCode = 'RND';
                $sizeCode = 'RND_NB' . ($nb ?? '0');

                $payload = [
                    'standard_version_id' => 1,
                    'shape_code' => $shapeCode,
                    'size_code' => $sizeCode,
                    'inch' => $inch,
                    'nb' => $nb,
                    'width' => $this->toDecimal($this->cell($row, $indices['width'])),
                    'length' => $this->toDecimal($this->cell($row, $indices['length'])),
                    'id_mm' => $this->toDecimal($this->cell($row, $indices['id_mm'])),
                    'od_mm' => $this->toDecimal($this->cell($row, $indices['od_mm'])),
                    'thk' => $this->toDecimal($this->cell($row, $indices['thk'])),
                    'ly' => $this->toDecimal($this->cell($row, $indices['ly'])),
                    'noc' => $this->toInt($this->cell($row, $indices['noc'])),
                    'lc' => $this->toDecimal($this->cell($row, $indices['lc'])),
                    'tc' => $this->toDecimal($this->cell($row, $indices['tc'])),
                    'p' => $this->toDecimal($this->cell($row, $indices['p'])),
                    'tr' => $this->toDecimal($this->cell($row, $indices['tr'])),
                    'r' => $this->toDecimal($this->cell($row, $indices['r'])),
                    'oal_b' => $this->toDecimal($this->cell($row, $indices['oal_b'])),
                    'bl' => $this->toDecimal($this->cell($row, $indices['bl'])),
                    'tl' => $this->toDecimal($this->cell($row, $indices['tl'])),
                    'slc' => $this->toDecimal($this->cell($row, $indices['slc'])),
                    'lpe' => $this->toDecimal($this->cell($row, $indices['lpe'])),
                    'pres' => $this->toDecimal($this->cell($row, $indices['pres'])),
                    'temp_c' => $this->toString($this->cell($row, $indices['temp_c'])),
                    'axial_m' => $this->toString($this->cell($row, $indices['axial_m'])),
                    'lsr_n_per' => $this->toDecimal($this->cell($row, $indices['lsr_n_per']), 3),
                    'mp_ci_mpa' => $this->toDecimal($this->cell($row, $indices['mp_ci_mpa']), 3),
                    'mp_ii_mpa' => $this->toDecimal($this->cell($row, $indices['mp_ii_mpa']), 3),
                    'mlc' => $this->toDecimal($this->cell($row, $indices['mlc']), 3),
                    'gpf' => $this->toDecimal($this->cell($row, $indices['gpf'])),
                    'oal' => $this->toDecimal($this->cell($row, $indices['oal'])),
                    'al' => $this->toDecimal($this->cell($row, $indices['al'])),
                    'width1' => $this->toDecimal($this->cell($row, $indices['width1'])),
                    'width2' => $this->toDecimal($this->cell($row, $indices['width2'])),
                    'spare' => $this->toDecimal($this->cell($row, $indices['spare'])),
                    'can_length' => $this->toDecimal($this->cell($row, $indices['can_length'])),
                    'circumference_collar' => $this->toDecimal($this->cell($row, $indices['circumference_collar'])),
                    'is_active' => 1,
                    'notes' => 'Imported from Book24.xlsx',
                    'updated_at' => now(),
                ];

                $existing = DB::table('validasi_dataejm_expansion_joint')
                    ->where('shape_code', $shapeCode)
                    ->where('size_code', $sizeCode)
                    ->first();

                if ($existing) {
                    DB::table('validasi_dataejm_expansion_joint')->where('id', $existing->id)->update($payload);
                    $updated++;
                } else {
                    DB::table('validasi_dataejm_expansion_joint')->insert(array_merge($payload, ['created_at' => now()]));
                    $created++;
                }
            }
        });

        $this->command?->info("Import Book24 selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    private function resolveColumnMap(array $headers): array
    {
        $aliases = [
            'inch' => ['inch'],
            'nb' => ['nb'],
            'width' => ['width'],
            'length' => ['length'],
            'id_mm' => ['id'],
            'od_mm' => ['od'],
            'thk' => ['thk'],
            'ly' => ['ly'],
            'noc' => ['noc', 'nocdefault'],
            'lc' => ['lc'],
            'tc' => ['tc'],
            'p' => ['p'],
            'tr' => ['tr'],
            'r' => ['r'],
            'oal_b' => ['oalb'],
            'bl' => ['bl'],
            'tl' => ['tl'],
            'slc' => ['slc'],
            'lpe' => ['lpe'],
            'pres' => ['pres', 'pressmpa'],
            'temp_c' => ['tempc'],
            'axial_m' => ['axialm', 'axialmm'],
            'lsr_n_per' => ['lsrnper', 'lsrnpermm'],
            'mp_ci_mpa' => ['mpcimpa'],
            'mp_ii_mpa' => ['mpiimpa'],
            'mlc' => ['mlc'],
            'gpf' => ['gpf'],
            'oal' => ['oal'],
            'al' => ['al'],
            'width1' => ['width1'],
            'width2' => ['width2'],
            'spare' => ['spare'],
            'can_length' => ['canlength', 'mm'],
            'circumference_collar' => ['circumferencecollar'],
        ];

        $map = array_fill_keys(array_keys($aliases), null);
        foreach ($headers as $i => $header) {
            foreach ($aliases as $key => $possible) {
                if ($map[$key] !== null) {
                    continue;
                }
                if (in_array($header, $possible, true)) {
                    $map[$key] = $i;
                }
            }
        }
        return $map;
    }

    private function readXlsxRows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $shared = simplexml_load_string($sharedXml);
            if ($shared !== false && isset($shared->si)) {
                foreach ($shared->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } else {
                        $text = '';
                        foreach ($si->r as $run) {
                            $text .= (string) $run->t;
                        }
                        $sharedStrings[] = $text;
                    }
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) {
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);
        if ($sheet === false || ! isset($sheet->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $colIndex = $this->columnIndexFromCellRef($ref);
                if ($colIndex < 0) {
                    continue;
                }
                $value = isset($cell->v) ? (string) $cell->v : '';
                if ((string) $cell['t'] === 's' && $value !== '') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }
                $cells[$colIndex] = trim($value);
            }
            if (empty($cells)) {
                continue;
            }
            ksort($cells);
            $max = (int) array_key_last($cells);
            $filled = [];
            for ($i = 0; $i <= $max; $i++) {
                $filled[] = $cells[$i] ?? '';
            }
            $rows[] = $filled;
        }
        return $rows;
    }

    private function columnIndexFromCellRef(string $ref): int
    {
        if (! preg_match('/^([A-Z]+)/', strtoupper($ref), $m)) {
            return -1;
        }
        $letters = $m[1];
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }
        return $index - 1;
    }

    private function normalizeHeader(mixed $value): string
    {
        $header = strtolower(trim((string) $value));
        return preg_replace('/[^a-z0-9]/', '', $header) ?? '';
    }

    private function cell(array $row, ?int $idx): mixed
    {
        if ($idx === null) {
            return null;
        }
        return $row[$idx] ?? null;
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        return is_numeric($value) ? (int) $value : null;
    }

    private function toString(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' ? null : $v;
    }

    private function toDecimal(mixed $value, int $scale = 2): ?string
    {
        if ($value === null) {
            return null;
        }
        $raw = preg_replace('/[^0-9,.\-]/', '', trim((string) $value)) ?? '';
        if ($raw === '' || $raw === '-') {
            return null;
        }
        $commaPos = strrpos($raw, ',');
        $dotPos = strrpos($raw, '.');
        if ($commaPos !== false && $dotPos !== false) {
            if ($commaPos > $dotPos) {
                $normalized = str_replace('.', '', $raw);
                $normalized = str_replace(',', '.', $normalized);
            } else {
                $normalized = str_replace(',', '', $raw);
            }
        } elseif ($commaPos !== false) {
            $normalized = str_replace('.', '', $raw);
            $normalized = str_replace(',', '.', $normalized);
        } else {
            $normalized = str_replace(',', '', $raw);
        }
        if (! is_numeric($normalized)) {
            return null;
        }
        return number_format((float) $normalized, $scale, '.', '');
    }
}
