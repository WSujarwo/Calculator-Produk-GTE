<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\CostProduct;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class CostProductController extends Controller
{
    private const TEMPLATE_HEADERS = ['DLABORNO', 'COST', 'GLACCOUNT', 'STATUS', 'ACCOUNTNAME', 'Statuse', 'DESCRIPTION'];

    public function __construct()
    {
        $this->middleware('permission:master.cost-products.view')->only(['index']);
        $this->middleware('permission:master.cost-products.create')->only(['create', 'store', 'import']);
        $this->middleware('permission:master.cost-products.edit')->only(['edit', 'update']);
        $this->middleware('permission:master.cost-products.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q = CostProduct::query();

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $q->where(function ($w) use ($s) {
                $w->where('dlaborno', 'like', "%{$s}%")
                    ->orWhere('glaccount', 'like', "%{$s}%")
                    ->orWhere('accountname', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $q->where('status', (int) $request->status);
        }

        $costProducts = $q->orderBy('dlaborno')->paginate(20)->withQueryString();

        return view('master.cost_products.index', compact('costProducts'));
    }

    public function create()
    {
        return view('master.cost_products.create');
    }

    public function store(Request $request)
    {
        $payload = $this->validatedPayload($request);
        CostProduct::create($payload);

        return redirect()->route('master.cost-products.index')->with('success', 'Cost Product created.');
    }

    public function edit(CostProduct $cost_product)
    {
        return view('master.cost_products.edit', ['costProduct' => $cost_product]);
    }

    public function update(Request $request, CostProduct $cost_product)
    {
        $payload = $this->validatedPayload($request, $cost_product->id);
        $cost_product->update($payload);

        return redirect()->route('master.cost-products.index')->with('success', 'Cost Product updated.');
    }

    public function destroy(CostProduct $cost_product)
    {
        $cost_product->delete();
        return redirect()->route('master.cost-products.index')->with('success', 'Cost Product deleted.');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $rows = $this->readRows($validated['file']);

        if (empty($rows)) {
            return redirect()->route('master.cost-products.index')
                ->with('error', 'Tidak ada data yang bisa diimport.');
        }

        $created = 0;
        $updated = 0;
        $unchanged = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$unchanged, &$skipped) {
            foreach ($rows as $row) {
                $dlaborno = trim((string) ($row['dlaborno'] ?? ''));
                if ($dlaborno === '') {
                    $skipped++;
                    continue;
                }

                $payload = [
                    'cost' => $this->toDecimal($row['cost'] ?? null),
                    'glaccount' => $this->cleanString($row['glaccount'] ?? null),
                    'status' => $this->toInteger($row['status'] ?? null),
                    'accountname' => $this->cleanString($row['accountname'] ?? null),
                    'statuse' => $this->cleanString($row['statuse'] ?? null),
                    'description' => $this->cleanString($row['description'] ?? null),
                ];

                $record = CostProduct::firstOrNew(['dlaborno' => $dlaborno]);
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

        return redirect()->route('master.cost-products.index')
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Unchanged: {$unchanged}, Skipped: {$skipped}.");
    }

    public function template()
    {
        $header = implode(',', self::TEMPLATE_HEADERS) . "\n";
        $sample = "BR-M-Braiding_18,24900,5.00.00.007,1,Biaya Overhead,Aktif,Braiding-Mesin Braiding 18 per Jam\n";
        $content = "\xEF\xBB\xBF" . $header . $sample;

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=cost_product_template.csv',
        ]);
    }

    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:cost_products,dlaborno';
        if ($ignoreId !== null) {
            $unique .= ',' . $ignoreId;
        }

        $data = $request->validate([
            'dlaborno' => ['required', 'string', 'max:120', $unique],
            'cost' => ['nullable', 'string', 'max:100'],
            'glaccount' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'string', 'max:30'],
            'accountname' => ['nullable', 'string', 'max:255'],
            'statuse' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
        ]);

        return [
            'dlaborno' => trim($data['dlaborno']),
            'cost' => $this->toDecimal($data['cost'] ?? null),
            'glaccount' => $this->cleanString($data['glaccount'] ?? null),
            'status' => $this->toInteger($data['status'] ?? null),
            'accountname' => $this->cleanString($data['accountname'] ?? null),
            'statuse' => $this->cleanString($data['statuse'] ?? null),
            'description' => $this->cleanString($data['description'] ?? null),
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

        if ($target['dlaborno'] === null) {
            return [];
        }

        $rows = [];
        foreach (array_slice($rawRows, 1) as $row) {
            $rows[] = [
                'dlaborno' => $this->readCellByIndex($row, $target['dlaborno']),
                'cost' => $this->readCellByIndex($row, $target['cost']),
                'glaccount' => $this->readCellByIndex($row, $target['glaccount']),
                'status' => $this->readCellByIndex($row, $target['status']),
                'accountname' => $this->readCellByIndex($row, $target['accountname']),
                'statuse' => $this->readCellByIndex($row, $target['statuse']),
                'description' => $this->readCellByIndex($row, $target['description']),
            ];
        }

        return $rows;
    }

    private function resolveTargetColumns(array $headers): array
    {
        $aliases = [
            'dlaborno' => ['dlaborno', 'dlborno'],
            'cost' => ['cost'],
            'glaccount' => ['glaccount'],
            'status' => ['status'],
            'accountname' => ['accountname'],
            'statuse' => ['statuse'],
            'description' => ['description'],
        ];

        $target = [
            'dlaborno' => null,
            'cost' => null,
            'glaccount' => null,
            'status' => null,
            'accountname' => null,
            'statuse' => null,
            'description' => null,
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
        return $v === '' ? null : $v;
    }

    private function toDecimal(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = preg_replace('/[^0-9,.\-]/', '', trim((string) $value)) ?? '';
        if ($raw === '') {
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
            // IDR biasanya tanpa desimal, titik dianggap pemisah ribuan.
            $normalized = str_replace(',', '', $raw);
            if (substr_count($normalized, '.') > 1) {
                $normalized = str_replace('.', '', $normalized);
            } elseif (substr_count($normalized, '.') === 1) {
                [$left, $right] = explode('.', $normalized, 2);
                if (strlen($right) === 3) {
                    $normalized = $left . $right;
                }
            }
        }

        if (! is_numeric($normalized)) {
            return null;
        }

        return number_format((float) $normalized, 2, '.', '');
    }

    private function toInteger(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (is_numeric($raw)) {
            return (int) $raw;
        }

        $rawLower = strtolower($raw);
        if (in_array($rawLower, ['active', 'aktif'], true)) {
            return 1;
        }

        if (in_array($rawLower, ['inactive', 'nonaktif', 'non-aktif'], true)) {
            return 0;
        }

        return null;
    }
}
