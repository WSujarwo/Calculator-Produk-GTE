<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class MaterialController extends Controller
{
    private const TEMPLATE_HEADERS = [
        'Part Number',
        'Description',
        'Naming',
        'Code1',
        'Code2',
        'Code3',
        'Thk',
        'Quality',
        'PriceSQM',
        'PriceKG',
        'PriceGram',
        'Berat (gr)',
        'Panjang (meter)',
        'Berat per Meter (gr)',
    ];

    public function __construct()
    {
        $this->middleware('permission:master.materials.view')->only(['index']);
        $this->middleware('permission:master.materials.create')->only(['create', 'store', 'import']);
        $this->middleware('permission:master.materials.edit')->only(['edit', 'update']);
        $this->middleware('permission:master.materials.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q = Material::query();

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $q->where(function ($w) use ($s) {
                $w->where('part_number', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('naming', 'like', "%{$s}%")
                    ->orWhere('code1', 'like', "%{$s}%")
                    ->orWhere('code2', 'like', "%{$s}%")
                    ->orWhere('code3', 'like', "%{$s}%")
                    ->orWhere('quality', 'like', "%{$s}%");
            });
        }

        $materials = $q->orderBy('part_number')->paginate(20)->withQueryString();

        return view('master.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('master.materials.create');
    }

    public function store(Request $request)
    {
        $payload = $this->validatedPayload($request);
        Material::create($payload);

        return redirect()->route('master.materials.index')->with('success', 'Material created.');
    }

    public function edit(Material $material)
    {
        return view('master.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $payload = $this->validatedPayload($request, $material->id);
        $material->update($payload);

        return redirect()->route('master.materials.index')->with('success', 'Material updated.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('master.materials.index')->with('success', 'Material deleted.');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $ext = strtolower((string) $validated['file']->getClientOriginalExtension());
        if ($ext === 'xlsx' && ! class_exists(ZipArchive::class)) {
            return redirect()->route('master.materials.index')
                ->with('error', 'Import XLSX butuh ekstensi PHP zip (ZipArchive). Aktifkan php_zip atau gunakan file CSV.');
        }

        $rows = $this->readRows($validated['file']);

        if (empty($rows)) {
            return redirect()->route('master.materials.index')
                ->with('error', 'Tidak ada data yang bisa diimport.');
        }

        $created = 0;
        $updated = 0;
        $unchanged = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$unchanged, &$skipped) {
            foreach ($rows as $row) {
                $partNumber = trim((string) ($row['part_number'] ?? ''));
                if ($partNumber === '') {
                    $skipped++;
                    continue;
                }

                $payload = [
                    'description' => $this->cleanString($row['description'] ?? null),
                    'naming' => $this->cleanString($row['naming'] ?? null),
                    'code1' => $this->cleanString($row['code1'] ?? null),
                    'code2' => $this->cleanString($row['code2'] ?? null),
                    'code3' => $this->cleanString($row['code3'] ?? null),
                    'thk' => $this->cleanString($row['thk'] ?? null),
                    'quality' => $this->cleanString($row['quality'] ?? null),
                    'price_sqm' => $this->toDecimal($row['price_sqm'] ?? null, 4),
                    'price_kg' => $this->toDecimal($row['price_kg'] ?? null, 4),
                    'price_gram' => $this->toDecimal($row['price_gram'] ?? null, 6),
                    'berat_gr' => $this->toDecimal($row['berat_gr'] ?? null, 6),
                    'panjang_meter' => $this->toDecimal($row['panjang_meter'] ?? null, 6),
                    'berat_per_meter_gr' => $this->toDecimal($row['berat_per_meter_gr'] ?? null, 6),
                ];

                $record = Material::firstOrNew(['part_number' => $partNumber]);
                $record->fill($payload);

                if (! $record->exists) {
                    $record->save();
                    $created++;
                    continue;
                }

                if ($record->isDirty()) {
                    $record->save();
                    $updated++;
                } else {
                    $unchanged++;
                }
            }
        });

        return redirect()->route('master.materials.index')
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Unchanged: {$unchanged}, Skipped: {$skipped}.");
    }

    public function template()
    {
        $header = implode(',', self::TEMPLATE_HEADERS) . "\n";
        $sample = "YP-01NG-NC-GTE-1P,Packing Yarn-Expanded Graphite SQ - No Coating,-,-,-,-,-,-,-,-,-,10,3.11,3.215434\n";
        $content = "\xEF\xBB\xBF" . $header . $sample;

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=materials_template.csv',
        ]);
    }

    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:materials,part_number';
        if ($ignoreId !== null) {
            $unique .= ',' . $ignoreId;
        }

        $data = $request->validate([
            'part_number' => ['required', 'string', 'max:120', $unique],
            'description' => ['nullable', 'string'],
            'naming' => ['nullable', 'string', 'max:255'],
            'code1' => ['nullable', 'string', 'max:120'],
            'code2' => ['nullable', 'string', 'max:120'],
            'code3' => ['nullable', 'string', 'max:120'],
            'thk' => ['nullable', 'string', 'max:60'],
            'quality' => ['nullable', 'string', 'max:120'],
            'price_sqm' => ['nullable', 'string', 'max:100'],
            'price_kg' => ['nullable', 'string', 'max:100'],
            'price_gram' => ['nullable', 'string', 'max:100'],
            'berat_gr' => ['nullable', 'string', 'max:100'],
            'panjang_meter' => ['nullable', 'string', 'max:100'],
            'berat_per_meter_gr' => ['nullable', 'string', 'max:100'],
        ]);

        return [
            'part_number' => trim((string) $data['part_number']),
            'description' => $this->cleanString($data['description'] ?? null),
            'naming' => $this->cleanString($data['naming'] ?? null),
            'code1' => $this->cleanString($data['code1'] ?? null),
            'code2' => $this->cleanString($data['code2'] ?? null),
            'code3' => $this->cleanString($data['code3'] ?? null),
            'thk' => $this->cleanString($data['thk'] ?? null),
            'quality' => $this->cleanString($data['quality'] ?? null),
            'price_sqm' => $this->toDecimal($data['price_sqm'] ?? null, 4),
            'price_kg' => $this->toDecimal($data['price_kg'] ?? null, 4),
            'price_gram' => $this->toDecimal($data['price_gram'] ?? null, 6),
            'berat_gr' => $this->toDecimal($data['berat_gr'] ?? null, 6),
            'panjang_meter' => $this->toDecimal($data['panjang_meter'] ?? null, 6),
            'berat_per_meter_gr' => $this->toDecimal($data['berat_per_meter_gr'] ?? null, 6),
        ];
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
        $target = $this->resolveTargetColumns($headers);

        if ($target['part_number'] === null) {
            return [];
        }

        $rows = [];
        foreach (array_slice($rawRows, 1) as $row) {
            $rows[] = [
                'part_number' => $this->readCellByIndex($row, $target['part_number']),
                'description' => $this->readCellByIndex($row, $target['description']),
                'naming' => $this->readCellByIndex($row, $target['naming']),
                'code1' => $this->readCellByIndex($row, $target['code1']),
                'code2' => $this->readCellByIndex($row, $target['code2']),
                'code3' => $this->readCellByIndex($row, $target['code3']),
                'thk' => $this->readCellByIndex($row, $target['thk']),
                'quality' => $this->readCellByIndex($row, $target['quality']),
                'price_sqm' => $this->readCellByIndex($row, $target['price_sqm']),
                'price_kg' => $this->readCellByIndex($row, $target['price_kg']),
                'price_gram' => $this->readCellByIndex($row, $target['price_gram']),
                'berat_gr' => $this->readCellByIndex($row, $target['berat_gr']),
                'panjang_meter' => $this->readCellByIndex($row, $target['panjang_meter']),
                'berat_per_meter_gr' => $this->readCellByIndex($row, $target['berat_per_meter_gr']),
            ];
        }

        return $rows;
    }

    private function resolveTargetColumns(array $headers): array
    {
        $aliases = [
            'part_number' => ['partnumber'],
            'description' => ['description'],
            'naming' => ['naming'],
            'code1' => ['code1'],
            'code2' => ['code2'],
            'code3' => ['code3'],
            'thk' => ['thk'],
            'quality' => ['quality'],
            'price_sqm' => ['pricesqm'],
            'price_kg' => ['pricekg'],
            'price_gram' => ['pricegram'],
            'berat_gr' => ['beratgr'],
            'panjang_meter' => ['panjangmeter'],
            'berat_per_meter_gr' => ['beratpermetergr'],
        ];

        $target = [
            'part_number' => null,
            'description' => null,
            'naming' => null,
            'code1' => null,
            'code2' => null,
            'code3' => null,
            'thk' => null,
            'quality' => null,
            'price_sqm' => null,
            'price_kg' => null,
            'price_gram' => null,
            'berat_gr' => null,
            'panjang_meter' => null,
            'berat_per_meter_gr' => null,
        ];

        foreach ($headers as $i => $header) {
            foreach ($aliases as $field => $possibleNames) {
                if ($target[$field] !== null) {
                    continue;
                }
                if (in_array($header, $possibleNames, true)) {
                    $target[$field] = $i;
                }
            }
        }

        return $target;
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
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                        continue;
                    }

                    $text = '';
                    foreach ($si->r as $run) {
                        $text .= (string) $run->t;
                    }
                    $sharedStrings[] = $text;
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
                $type = (string) $cell['t'];

                if ($type === 's' && $value !== '') {
                    $sharedIdx = (int) $value;
                    $value = $sharedStrings[$sharedIdx] ?? '';
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

    private function cleanString(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' || $v === '-' ? null : $v;
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
            if (substr_count($normalized, '.') > 1) {
                $normalized = str_replace('.', '', $normalized);
            }
        }

        if (! is_numeric($normalized)) {
            return null;
        }

        return number_format((float) $normalized, $scale, '.', '');
    }
}
