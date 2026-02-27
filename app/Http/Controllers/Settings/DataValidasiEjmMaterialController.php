<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use ZipArchive;

class DataValidasiEjmMaterialController extends Controller
{
    private const TABLE = 'validasi_dataejm_materials';

    public function __construct()
    {
        $this->middleware('permission:settings.ejm-validation.view')->only(['index']);
        $this->middleware('permission:settings.ejm-validation.import')->only(['import']);
        $this->middleware('permission:settings.ejm-validation.export')->only(['templateCsv', 'templateExcel']);
    }

    public function index(Request $request): View
    {
        $query = DB::table(self::TABLE);

        if ($request->filled('material_role')) {
            $query->where('material_role', strtoupper(trim((string) $request->query('material_role'))));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->query('q'));
            $query->where(function ($inner) use ($q) {
                $inner->where('material_name', 'like', '%' . $q . '%')
                    ->orWhere('part_number', 'like', '%' . $q . '%')
                    ->orWhere('naming', 'like', '%' . $q . '%');
            });
        }

        $rows = $query->orderBy('material_role')->orderBy('material_name')->paginate(100)->withQueryString();

        $validationMenus = [
            ['key' => 'actual', 'label' => 'Actual Design Calculation', 'url' => route('setting.ejm-validation.index', ['tab' => 'actual'])],
            ['key' => 'can-length', 'label' => 'Calculation of CAN Length', 'url' => route('setting.ejm-validation.index', ['tab' => 'can-length'])],
            ['key' => 'proses', 'label' => 'Validasi Proses', 'url' => route('setting.ejm-validation-proses.index')],
            ['key' => 'bellowconv', 'label' => 'Bellowconv', 'url' => route('setting.ejm-validation-bellowconv.index')],
            ['key' => 'expansion-joint', 'label' => 'Expansion Joint', 'url' => route('setting.ejm-expansion-joint.index')],
            ['key' => 'material', 'label' => 'Material EJM', 'url' => route('setting.ejm-validation-material.index')],
        ];

        return view('settings.ejm-validation-material', [
            'rows' => $rows,
            'validationMenus' => $validationMenus,
            'activeTab' => 'material',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $ext = strtolower((string) $validated['file']->getClientOriginalExtension());
        if ($ext === 'xlsx' && ! class_exists(ZipArchive::class)) {
            return redirect()->route('setting.ejm-validation-material.index')
                ->with('error', 'Import XLSX membutuhkan ekstensi PHP zip (ZipArchive).');
        }

        $rows = $this->readRows($validated['file']);
        if (empty($rows)) {
            return redirect()->route('setting.ejm-validation-material.index')
                ->with('error', 'Tidak ada data valid pada file import.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $role = $this->normalizeRole($row['material_role'] ?? null);
                $materialName = $this->toNullString($row['material_name'] ?? null);
                if ($role === null || $materialName === null) {
                    $skipped++;
                    continue;
                }

                $thkMm = $this->toDecimal($row['thk_mm'] ?? null, 3);
                $jumlahPly = $this->toInt($row['jumlah_ply'] ?? null);
                $sizeIn = $this->toNullString($row['size_in'] ?? null);
                $sch = $this->toNullString($row['sch'] ?? null);
                $type = $this->toNullString($row['type'] ?? null);
                $quality = $this->toNullString($row['quality'] ?? null);
                $priceSqm = $this->toDecimal($row['price_sqm'] ?? null, 4);
                $priceKg = $this->toDecimal($row['price_kg'] ?? null, 4);
                $priceGram = $this->toDecimal($row['price_gram'] ?? null, 6);
                $partNumber = $this->toNullString($row['part_number'] ?? null);
                $description = $this->toNullString($row['description'] ?? null);
                $naming = $this->toNullString($row['naming'] ?? null);

                $material = $this->upsertMasterMaterial(
                    $partNumber,
                    $description,
                    $naming,
                    $quality,
                    $thkMm,
                    $priceSqm,
                    $priceKg,
                    $priceGram,
                    $role,
                    $materialName
                );

                $payload = [
                    'material_role' => $role,
                    'material_name' => $materialName,
                    'thk_mm' => $thkMm,
                    'jumlah_ply' => $jumlahPly,
                    'size_in' => $sizeIn,
                    'sch' => $sch,
                    'type' => $type,
                    'material_id' => $material->id,
                    'part_number' => $material->part_number,
                    'description' => $description,
                    'naming' => $naming,
                    'quality' => $quality,
                    'price_sqm' => $priceSqm,
                    'price_kg' => $priceKg,
                    'price_gram' => $priceGram,
                    'is_active' => true,
                    'updated_at' => now(),
                ];

                $existing = DB::table(self::TABLE)
                    ->where('part_number', $material->part_number)
                    ->first();

                if ($existing) {
                    DB::table(self::TABLE)->where('id', $existing->id)->update($payload);
                    $updated++;
                    continue;
                }

                DB::table(self::TABLE)->insert(array_merge($payload, ['created_at' => now()]));
                $created++;
            }
        });

        return redirect()->route('setting.ejm-validation-material.index')
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    public function templateCsv()
    {
        $headers = ['material_role', 'material_name', 'thk_mm', 'jumlah_ply', 'size_in', 'sch', 'type', 'part_number', 'description', 'naming', 'quality', 'price_sqm', 'price_kg', 'price_gram'];
        $rows = [
            ['BELLOW', 'SA240 304', '0.500', '2', '', '', '', 'BELLOW-SA240304-05-2PLY', 'Dummy bellow', 'Bellow SA240 304 0.5', 'SS304', '1236450', '0', '0'],
            ['PIPE_NIPPLE', 'SA312 TP304', '', '', '4', 'STD', 'Seamless', 'PIPE-SA312TP304-4-STD-SEA', 'Dummy pipe nipple', 'Pipe Nipple SA312 TP304 4', 'SS304', '0', '0', '0'],
            ['FLANGE', 'SS304', '', '', '4', '150', 'SO', 'FLANGE-SS304-4-150-SO', 'Dummy flange', 'Flange SS304 4 #150 SO', 'SS304', '0', '0', '0'],
        ];

        $lines = [implode(',', $headers)];
        foreach ($rows as $row) {
            $cells = [];
            foreach ($row as $cell) {
                $cells[] = '"' . str_replace('"', '""', (string) $cell) . '"';
            }
            $lines[] = implode(',', $cells);
        }

        $content = "\xEF\xBB\xBF" . implode("\n", $lines) . "\n";
        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validasi_material_template.csv',
        ]);
    }

    public function templateExcel()
    {
        $rows = [
            ['material_role' => 'BELLOW', 'material_name' => 'SA240 304', 'thk_mm' => '0.500', 'jumlah_ply' => '2', 'size_in' => '', 'sch' => '', 'type' => '', 'part_number' => 'BELLOW-SA240304-05-2PLY', 'description' => 'Dummy bellow', 'naming' => 'Bellow SA240 304 0.5', 'quality' => 'SS304', 'price_sqm' => '1236450', 'price_kg' => '', 'price_gram' => ''],
            ['material_role' => 'PIPE_NIPPLE', 'material_name' => 'SA312 TP304', 'thk_mm' => '', 'jumlah_ply' => '', 'size_in' => '4', 'sch' => 'STD', 'type' => 'Seamless', 'part_number' => 'PIPE-SA312TP304-4-STD-SEA', 'description' => 'Dummy pipe nipple', 'naming' => 'Pipe Nipple SA312 TP304 4', 'quality' => 'SS304', 'price_sqm' => '', 'price_kg' => '', 'price_gram' => ''],
            ['material_role' => 'FLANGE', 'material_name' => 'SS304', 'thk_mm' => '', 'jumlah_ply' => '', 'size_in' => '4', 'sch' => '150', 'type' => 'SO', 'part_number' => 'FLANGE-SS304-4-150-SO', 'description' => 'Dummy flange', 'naming' => 'Flange SS304 4 #150 SO', 'quality' => 'SS304', 'price_sqm' => '', 'price_kg' => '', 'price_gram' => ''],
        ];

        $html = view('settings.ejm-validation-material-template-xls', compact('rows'))->render();
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validasi_material_template.xls',
        ]);
    }

    private function readRows(UploadedFile $file): array
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());
        $rawRows = match ($ext) {
            'csv', 'txt' => $this->readCsvRows($file->getRealPath()),
            'xlsx' => $this->readXlsxRows($file->getRealPath()),
            default => [],
        };
        if (count($rawRows) < 2) {
            return [];
        }

        $headers = array_map(fn ($v) => $this->normalizeHeader($v), $rawRows[0]);
        $indices = $this->resolveColumnMap($headers);

        $rows = [];
        foreach (array_slice($rawRows, 1) as $row) {
            $rows[] = [
                'material_role' => $this->readCellByIndex($row, $indices['material_role']),
                'material_name' => $this->readCellByIndex($row, $indices['material_name']),
                'thk_mm' => $this->readCellByIndex($row, $indices['thk_mm']),
                'jumlah_ply' => $this->readCellByIndex($row, $indices['jumlah_ply']),
                'size_in' => $this->readCellByIndex($row, $indices['size_in']),
                'sch' => $this->readCellByIndex($row, $indices['sch']),
                'type' => $this->readCellByIndex($row, $indices['type']),
                'part_number' => $this->readCellByIndex($row, $indices['part_number']),
                'description' => $this->readCellByIndex($row, $indices['description']),
                'naming' => $this->readCellByIndex($row, $indices['naming']),
                'quality' => $this->readCellByIndex($row, $indices['quality']),
                'price_sqm' => $this->readCellByIndex($row, $indices['price_sqm']),
                'price_kg' => $this->readCellByIndex($row, $indices['price_kg']),
                'price_gram' => $this->readCellByIndex($row, $indices['price_gram']),
            ];
        }

        return $rows;
    }

    private function resolveColumnMap(array $headers): array
    {
        $aliases = [
            'material_role' => ['h1', 'materialrole', 'role', 'kategori'],
            'material_name' => ['material', 'materialname'],
            'thk_mm' => ['thkmm', 'thk'],
            'jumlah_ply' => ['jumlahply', 'ply', 'jumlahply'],
            'size_in' => ['sizein', 'size'],
            'sch' => ['sch'],
            'type' => ['type'],
            'part_number' => ['partnumber'],
            'description' => ['description'],
            'naming' => ['naming'],
            'quality' => ['quality'],
            'price_sqm' => ['pricesqm'],
            'price_kg' => ['pricekg'],
            'price_gram' => ['pricegram'],
        ];

        $map = array_fill_keys(array_keys($aliases), null);
        foreach ($headers as $i => $header) {
            foreach ($aliases as $field => $possible) {
                if ($map[$field] !== null) {
                    continue;
                }
                if (in_array($header, $possible, true)) {
                    $map[$field] = $i;
                }
            }
        }

        return $map;
    }

    private function readCsvRows(string $path): array
    {
        $rows = [];
        $file = new \SplFileObject($path);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        foreach ($file as $row) {
            if (! is_array($row)) {
                continue;
            }
            if (count($row) === 1 && trim((string) ($row[0] ?? '')) === '') {
                continue;
            }
            $rows[] = array_map(fn ($cell) => is_string($cell) ? trim($cell) : $cell, $row);
        }
        return $rows;
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
                    $sharedStrings[] = isset($si->t) ? (string) $si->t : '';
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
                $cells[$colIndex] = trim((string) $value);
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
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }
        return $index - 1;
    }

    private function normalizeHeader(mixed $value): string
    {
        $header = strtolower(trim((string) $value));
        return preg_replace('/[^a-z0-9]/', '', $header) ?? '';
    }

    private function readCellByIndex(array $row, ?int $index): mixed
    {
        if ($index === null) {
            return null;
        }
        return $row[$index] ?? null;
    }

    private function normalizeRole(mixed $value): ?string
    {
        $v = strtoupper(trim((string) ($value ?? '')));
        return match ($v) {
            'BELLOW' => 'BELLOW',
            'PIPE - NIPPLE', 'PIPE-NIPPLE', 'PIPE NIPPLE', 'PIPENIPPLE', 'PIPE_END' => 'PIPE_NIPPLE',
            'FLANGE' => 'FLANGE',
            'COLLAR' => 'COLLAR',
            default => null,
        };
    }

    private function toNullString(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' ? null : $v;
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        return is_numeric($value) ? (int) $value : null;
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
            $normalized = $commaPos > $dotPos
                ? str_replace(',', '.', str_replace('.', '', $raw))
                : str_replace(',', '', $raw);
        } elseif ($commaPos !== false) {
            $normalized = str_replace(',', '.', str_replace('.', '', $raw));
        } else {
            $normalized = str_replace(',', '', $raw);
        }

        if (! is_numeric($normalized)) {
            return null;
        }
        return number_format((float) $normalized, $scale, '.', '');
    }

    private function upsertMasterMaterial(
        ?string $partNumber,
        ?string $description,
        ?string $naming,
        ?string $quality,
        ?string $thkMm,
        ?string $priceSqm,
        ?string $priceKg,
        ?string $priceGram,
        string $role,
        string $materialName
    ): Material {
        $partNumber = $partNumber ?: strtoupper(substr($role, 0, 3) . '-' . preg_replace('/[^A-Z0-9]/', '', strtoupper($materialName)) . '-' . substr(sha1($materialName . '|' . $role . '|' . ($thkMm ?? '')), 0, 6));

        $material = Material::query()->where('part_number', $partNumber)->first();
        if (! $material) {
            $material = new Material();
            $material->part_number = $partNumber;
        }

        $material->description = $description;
        $material->naming = $naming ?: $materialName;
        $material->thk = $thkMm;
        $material->quality = $quality;
        $material->price_sqm = $priceSqm;
        $material->price_kg = $priceKg;
        $material->price_gram = $priceGram;
        $material->save();

        return $material;
    }
}
