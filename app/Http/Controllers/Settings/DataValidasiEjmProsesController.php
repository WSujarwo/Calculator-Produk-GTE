<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\DataValidasiEjmProses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use ZipArchive;

class DataValidasiEjmProsesController extends Controller
{
    private const ACTIVE_TAB = 'proses';

    private const EXPORT_HEADERS = [
        'component_type',
        'process_name',
        'nb',
        'tube_inner',
        'price_tube_inner',
        'tube_outer',
        'price_tube_outer',
        'unit',
        'notes',
    ];

    public function __construct()
    {
        $this->middleware('permission:settings.ejm-validation.view')->only(['index']);
        $this->middleware('permission:settings.ejm-validation.create')->only(['create', 'store']);
        $this->middleware('permission:settings.ejm-validation.edit')->only(['update']);
        $this->middleware('permission:settings.ejm-validation.delete')->only(['destroy']);
        $this->middleware('permission:settings.ejm-validation.import')->only(['import']);
        $this->middleware('permission:settings.ejm-validation.export')->only(['templateCsv', 'templateExcel', 'exportCsv', 'exportExcel']);
    }

    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $query = DataValidasiEjmProses::query();

        if ($request->filled('component_type')) {
            $query->where('component_type', trim((string) $request->query('component_type')));
        }
        if ($request->filled('process_name')) {
            $query->where('process_name', trim((string) $request->query('process_name')));
        }
        if ($request->filled('nb') && is_numeric($request->query('nb'))) {
            $query->where('nb', (int) $request->query('nb'));
        }

        $rows = $query
            ->orderBy('component_type')
            ->orderBy('process_name')
            ->orderByRaw('COALESCE(nb, 0) asc')
            ->paginate(100)
            ->withQueryString();

        $editing = null;
        if ($request->filled('edit')) {
            $editing = DataValidasiEjmProses::find((int) $request->query('edit'));
        }

        $payload = [
            'rows' => $rows,
            'editing' => $editing?->toArray(),
            'openCreateModal' => $request->boolean('create'),
            'validationMenus' => [
                ['key' => 'actual', 'label' => 'Actual Design Calculation', 'url' => route('setting.ejm-validation.index', ['tab' => 'actual'])],
                ['key' => 'can-length', 'label' => 'Calculation of CAN Length', 'url' => route('setting.ejm-validation.index', ['tab' => 'can-length'])],
                ['key' => self::ACTIVE_TAB, 'label' => 'Validasi Proses', 'url' => route('setting.ejm-validation-proses.index')],
                ['key' => 'bellowconv', 'label' => 'Bellowconv', 'url' => route('setting.ejm-validation-bellowconv.index')],
                ['key' => 'expansion-joint', 'label' => 'Expansion Joint', 'url' => route('setting.ejm-expansion-joint.index')],
                ['key' => 'material', 'label' => 'Validasi Material EJM', 'url' => route('setting.ejm-validation-material.index')],
            ],
            'activeTab' => self::ACTIVE_TAB,
        ];

        if (view()->exists('settings.ejm-validation-proses')) {
            return view('settings.ejm-validation-proses', $payload);
        }

        return response()->json($payload);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('setting.ejm-validation-proses.index', ['create' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        DataValidasiEjmProses::create($payload);

        return redirect()->route('setting.ejm-validation-proses.index')
            ->with('success', 'Data validasi proses berhasil ditambahkan.');
    }

    public function update(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        $record = DataValidasiEjmProses::findOrFail($id);
        $payload = $this->validatedPayload($request, $id);
        $record->update($payload);

        return redirect()->route('setting.ejm-validation-proses.index')
            ->with('success', 'Data validasi proses berhasil diupdate.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        $record = DataValidasiEjmProses::findOrFail($id);
        $record->delete();

        return redirect()->route('setting.ejm-validation-proses.index')
            ->with('success', 'Data validasi proses berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $ext = strtolower((string) $validated['file']->getClientOriginalExtension());
        if ($ext === 'xlsx' && ! class_exists(ZipArchive::class)) {
            return redirect()->route('setting.ejm-validation-proses.index')
                ->with('error', 'Import XLSX membutuhkan ekstensi PHP zip (ZipArchive).');
        }

        $rows = $this->readRows($validated['file']);
        if (empty($rows)) {
            return redirect()->route('setting.ejm-validation-proses.index')
                ->with('error', 'Tidak ada data valid pada file import.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $componentType = $this->cleanString($row['component_type'] ?? null);
                $processName = $this->cleanString($row['process_name'] ?? null);
                if ($componentType === null || $processName === null) {
                    $skipped++;
                    continue;
                }

                $nb = $this->toInteger($row['nb'] ?? null);
                $payload = [
                    'component_type' => $componentType,
                    'process_name' => $processName,
                    'nb' => $nb,
                    'tube_inner' => $this->toInteger($row['tube_inner'] ?? null),
                    'price_tube_inner' => $this->toDecimal($row['price_tube_inner'] ?? null),
                    'tube_outer' => $this->toInteger($row['tube_outer'] ?? null),
                    'price_tube_outer' => $this->toDecimal($row['price_tube_outer'] ?? null),
                    'unit' => $this->cleanString($row['unit'] ?? null),
                    'notes' => $this->cleanString($row['notes'] ?? null),
                ];

                $existingQuery = DataValidasiEjmProses::query()
                    ->where('component_type', $componentType)
                    ->where('process_name', $processName);

                if ($nb === null) {
                    $existingQuery->whereNull('nb');
                } else {
                    $existingQuery->where('nb', $nb);
                }

                $existing = $existingQuery->first();
                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                    continue;
                }

                DataValidasiEjmProses::create($payload);
                $created++;
            }
        });

        return redirect()->route('setting.ejm-validation-proses.index')
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    public function templateCsv()
    {
        return $this->buildCsvResponse(
            $this->sampleRows(),
            'ejm_validasi_proses_template.csv'
        );
    }

    public function exportCsv(Request $request)
    {
        $rows = $this->exportRows($request);
        return $this->buildCsvResponse($rows, 'ejm_validasi_proses_export.csv');
    }

    public function templateExcel()
    {
        $html = $this->buildExcelHtml($this->sampleRows(), 'Template Validasi Proses EJM');
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validasi_proses_template.xls',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $html = $this->buildExcelHtml($this->exportRows($request), 'Export Validasi Proses EJM');
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validasi_proses_export.xls',
        ]);
    }

    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $nbInput = $request->input('nb');
        $processUnique = Rule::unique('data_validasiejm_proses', 'process_name')
            ->where(function ($query) use ($request, $nbInput) {
                $query->where('component_type', trim((string) $request->input('component_type')));
                if ($nbInput === null || $nbInput === '') {
                    $query->whereNull('nb');
                } else {
                    $query->where('nb', (int) $nbInput);
                }
            });

        if ($ignoreId !== null) {
            $processUnique = $processUnique->ignore($ignoreId);
        }

        $data = $request->validate([
            'component_type' => ['required', 'string', 'max:80'],
            'process_name' => ['required', 'string', 'max:120', $processUnique],
            'nb' => ['nullable', 'integer', 'min:0'],
            'tube_inner' => ['nullable', 'integer', 'min:0'],
            'price_tube_inner' => ['nullable', 'numeric'],
            'tube_outer' => ['nullable', 'integer', 'min:0'],
            'price_tube_outer' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        return [
            'component_type' => trim($data['component_type']),
            'process_name' => trim($data['process_name']),
            'nb' => $this->toInteger($data['nb'] ?? null),
            'tube_inner' => $this->toInteger($data['tube_inner'] ?? null),
            'price_tube_inner' => $this->toDecimal($data['price_tube_inner'] ?? null),
            'tube_outer' => $this->toInteger($data['tube_outer'] ?? null),
            'price_tube_outer' => $this->toDecimal($data['price_tube_outer'] ?? null),
            'unit' => $this->cleanString($data['unit'] ?? null),
            'notes' => $this->cleanString($data['notes'] ?? null),
        ];
    }

    private function exportRows(Request $request): array
    {
        $query = DataValidasiEjmProses::query();
        if ($request->filled('component_type')) {
            $query->where('component_type', trim((string) $request->query('component_type')));
        }
        if ($request->filled('process_name')) {
            $query->where('process_name', trim((string) $request->query('process_name')));
        }
        if ($request->filled('nb') && is_numeric($request->query('nb'))) {
            $query->where('nb', (int) $request->query('nb'));
        }

        return $query
            ->orderBy('component_type')
            ->orderBy('process_name')
            ->orderByRaw('COALESCE(nb, 0) asc')
            ->get(self::EXPORT_HEADERS)
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function buildCsvResponse(array $rows, string $filename)
    {
        $lines = [];
        $lines[] = implode(',', self::EXPORT_HEADERS);

        foreach ($rows as $row) {
            $line = [];
            foreach (self::EXPORT_HEADERS as $header) {
                $value = $row[$header] ?? '';
                $line[] = '"' . str_replace('"', '""', (string) $value) . '"';
            }
            $lines[] = implode(',', $line);
        }

        $content = "\xEF\xBB\xBF" . implode("\n", $lines) . "\n";
        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    private function buildExcelHtml(array $rows, string $title): string
    {
        $thead1 = '<tr style="background:#d9ead3;font-weight:700;">';
        foreach (self::EXPORT_HEADERS as $header) {
            $thead1 .= '<th>' . e(strtoupper($header)) . '</th>';
        }
        $thead1 .= '</tr>';

        $tbody = '';
        foreach ($rows as $row) {
            $tbody .= '<tr>';
            foreach (self::EXPORT_HEADERS as $header) {
                $tbody .= '<td>' . e((string) ($row[$header] ?? '')) . '</td>';
            }
            $tbody .= '</tr>';
        }

        return '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;}th,td{border:1px solid #777;padding:4px 6px;text-align:center;}</style></head><body><h3>' . e($title) . '</h3><table><thead>' . $thead1 . '</thead><tbody>' . $tbody . '</tbody></table></body></html>';
    }

    private function sampleRows(): array
    {
        return [
            [
                'component_type' => 'Bellows',
                'process_name' => 'Cutting Shearing',
                'nb' => 100,
                'tube_inner' => 30,
                'price_tube_inner' => 15000.00,
                'tube_outer' => 30,
                'price_tube_outer' => 15500.00,
                'unit' => 'menit',
                'notes' => null,
            ],
            [
                'component_type' => 'EJM PRODUCTION',
                'process_name' => 'Assembly',
                'nb' => 250,
                'tube_inner' => 2,
                'price_tube_inner' => 25000.00,
                'tube_outer' => 2,
                'price_tube_outer' => 27000.00,
                'unit' => 'satuan',
                'notes' => 'Khusus EJM production biasanya satuan',
            ],
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
        $indices = $this->resolveColumnMap($headers);
        if ($indices['component_type'] === null || $indices['process_name'] === null) {
            return [];
        }

        $rows = [];
        foreach (array_slice($rawRows, 1) as $row) {
            $rows[] = [
                'component_type' => $this->readCellByIndex($row, $indices['component_type']),
                'process_name' => $this->readCellByIndex($row, $indices['process_name']),
                'nb' => $this->readCellByIndex($row, $indices['nb']),
                'tube_inner' => $this->readCellByIndex($row, $indices['tube_inner']),
                'price_tube_inner' => $this->readCellByIndex($row, $indices['price_tube_inner']),
                'tube_outer' => $this->readCellByIndex($row, $indices['tube_outer']),
                'price_tube_outer' => $this->readCellByIndex($row, $indices['price_tube_outer']),
                'unit' => $this->readCellByIndex($row, $indices['unit']),
                'notes' => $this->readCellByIndex($row, $indices['notes']),
            ];
        }

        return $rows;
    }

    private function resolveColumnMap(array $headers): array
    {
        $aliases = [
            'component_type' => ['componenttype', 'component', 'table'],
            'process_name' => ['processname', 'process', 'proses'],
            'nb' => ['nb'],
            'tube_inner' => ['tubeinner'],
            'price_tube_inner' => ['pricetubeinner', 'hargatubeinner', 'harga1', 'price1'],
            'tube_outer' => ['tubeouter'],
            'price_tube_outer' => ['pricetubeouter', 'hargatubeouter', 'harga2', 'price2'],
            'unit' => ['unit', 'satuan'],
            'notes' => ['notes', 'keterangan'],
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

    private function toInteger(mixed $value): ?int
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
