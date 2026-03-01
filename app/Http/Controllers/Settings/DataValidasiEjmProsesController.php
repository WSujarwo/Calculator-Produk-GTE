<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use ZipArchive;

class DataValidasiEjmProsesController extends Controller
{
    private const ACTIVE_TAB = 'proses';
    private const TABLE_DEFINITIONS = 'ejm_process_definitions';
    private const TABLE_TIMES = 'ejm_process_times';

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
        $query = $this->baseRowsQuery($request);

        $rows = $query
            ->orderBy('component_type')
            ->orderBy('process_name')
            ->orderByRaw('COALESCE(nb, 0) asc')
            ->paginate(100)
            ->withQueryString();

        $editing = null;
        if ($request->filled('edit')) {
            $editing = DB::table(self::TABLE_TIMES . ' as t')
                ->join(self::TABLE_DEFINITIONS . ' as d', 'd.id', '=', 't.process_definition_id')
                ->where('t.id', (int) $request->query('edit'))
                ->select([
                    't.id',
                    'd.component_type',
                    'd.process_name',
                    't.nb',
                    DB::raw('t.minutes_inner as tube_inner'),
                    DB::raw($this->priceInnerSelectExpr() . ' as price_tube_inner'),
                    DB::raw('t.minutes_outer as tube_outer'),
                    DB::raw($this->priceOuterSelectExpr() . ' as price_tube_outer'),
                    'd.unit',
                    DB::raw('COALESCE(t.notes, d.notes) as notes'),
                ])
                ->first();
        }

        $payload = [
            'rows' => $rows,
            'editing' => $editing ? (array) $editing : null,
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

        DB::transaction(function () use ($payload) {
            $definitionId = $this->upsertDefinition($payload);
            $this->upsertTime($definitionId, $payload);
        });

        return redirect()->route('setting.ejm-validation-proses.index')
            ->with('success', 'Data validasi proses berhasil ditambahkan.');
    }

    public function update(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        $payload = $this->validatedPayload($request);

        DB::transaction(function () use ($id, $payload) {
            $existing = DB::table(self::TABLE_TIMES)->where('id', $id)->first();
            abort_if(! $existing, 404, 'Data validasi proses tidak ditemukan.');

            $oldDefinitionId = (int) $existing->process_definition_id;
            $targetDefinitionId = $this->upsertDefinition($payload);

            DB::table(self::TABLE_TIMES)
                ->where('id', $id)
                ->update([
                    'process_definition_id' => $targetDefinitionId,
                    'nb' => $payload['nb'],
                    'noc' => null,
                    'minutes_inner' => $payload['tube_inner'],
                    'minutes_outer' => $payload['tube_outer'],
                    'notes' => $payload['notes'],
                    'updated_at' => now(),
                ]);

            $this->cleanupOrphanDefinition($oldDefinitionId);
        });

        return redirect()->route('setting.ejm-validation-proses.index')
            ->with('success', 'Data validasi proses berhasil diupdate.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        DB::transaction(function () use ($id) {
            $existing = DB::table(self::TABLE_TIMES)->where('id', $id)->first();
            abort_if(! $existing, 404, 'Data validasi proses tidak ditemukan.');

            $definitionId = (int) $existing->process_definition_id;
            DB::table(self::TABLE_TIMES)->where('id', $id)->delete();
            $this->cleanupOrphanDefinition($definitionId);
        });

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
                $payload = [
                    'component_type' => $this->cleanString($row['component_type'] ?? null),
                    'process_name' => $this->cleanString($row['process_name'] ?? null),
                    'nb' => $this->toInteger($row['nb'] ?? null),
                    'tube_inner' => $this->toInteger($row['tube_inner'] ?? null),
                    'price_tube_inner' => $this->toDecimal($row['price_tube_inner'] ?? null),
                    'tube_outer' => $this->toInteger($row['tube_outer'] ?? null),
                    'price_tube_outer' => $this->toDecimal($row['price_tube_outer'] ?? null),
                    'unit' => $this->cleanString($row['unit'] ?? null) ?? 'menit',
                    'notes' => $this->cleanString($row['notes'] ?? null),
                ];
                [$payload['price_tube_inner'], $payload['price_tube_outer']] = $this->normalizeRates(
                    $payload['price_tube_inner'],
                    $payload['price_tube_outer']
                );

                if ($payload['component_type'] === null || $payload['process_name'] === null) {
                    $skipped++;
                    continue;
                }

                $definitionId = $this->upsertDefinition($payload);

                $existingQuery = DB::table(self::TABLE_TIMES)
                    ->where('process_definition_id', $definitionId)
                    ->whereNull('noc');
                if ($payload['nb'] === null) {
                    $existingQuery->whereNull('nb');
                } else {
                    $existingQuery->where('nb', $payload['nb']);
                }

                $existingTime = $existingQuery->first();
                if ($existingTime) {
                    DB::table(self::TABLE_TIMES)->where('id', $existingTime->id)->update([
                        'minutes_inner' => $payload['tube_inner'],
                        'minutes_outer' => $payload['tube_outer'],
                        'notes' => $payload['notes'],
                        'updated_at' => now(),
                    ]);
                    $updated++;
                } else {
                    DB::table(self::TABLE_TIMES)->insert([
                        'process_definition_id' => $definitionId,
                        'nb' => $payload['nb'],
                        'noc' => null,
                        'minutes_inner' => $payload['tube_inner'],
                        'minutes_outer' => $payload['tube_outer'],
                        'notes' => $payload['notes'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $created++;
                }
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

    private function validatedPayload(Request $request): array
    {
        $data = $request->validate([
            'component_type' => ['required', 'string', 'max:80'],
            'process_name' => ['required', 'string', 'max:120'],
            'nb' => ['nullable', 'integer', 'min:0'],
            'tube_inner' => ['nullable', 'integer', 'min:0'],
            'price_tube_inner' => ['nullable', 'numeric'],
            'tube_outer' => ['nullable', 'integer', 'min:0'],
            'price_tube_outer' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $priceInner = $this->toDecimal($data['price_tube_inner'] ?? null);
        $priceOuter = $this->toDecimal($data['price_tube_outer'] ?? null);
        [$priceInner, $priceOuter] = $this->normalizeRates($priceInner, $priceOuter);

        return [
            'component_type' => trim($data['component_type']),
            'process_name' => trim($data['process_name']),
            'nb' => $this->toInteger($data['nb'] ?? null),
            'tube_inner' => $this->toInteger($data['tube_inner'] ?? null),
            'price_tube_inner' => $priceInner,
            'tube_outer' => $this->toInteger($data['tube_outer'] ?? null),
            'price_tube_outer' => $priceOuter,
            'unit' => $this->cleanString($data['unit'] ?? null) ?? 'menit',
            'notes' => $this->cleanString($data['notes'] ?? null),
        ];
    }

    private function baseRowsQuery(Request $request)
    {
        $query = DB::table(self::TABLE_TIMES . ' as t')
            ->join(self::TABLE_DEFINITIONS . ' as d', 'd.id', '=', 't.process_definition_id')
            ->select([
                't.id',
                'd.component_type',
                'd.process_name',
                't.nb',
                DB::raw('t.minutes_inner as tube_inner'),
                DB::raw($this->priceInnerSelectExpr() . ' as price_tube_inner'),
                DB::raw('t.minutes_outer as tube_outer'),
                DB::raw($this->priceOuterSelectExpr() . ' as price_tube_outer'),
                'd.unit',
                DB::raw('COALESCE(t.notes, d.notes) as notes'),
            ]);

        if ($request->filled('component_type')) {
            $query->where('d.component_type', trim((string) $request->query('component_type')));
        }
        if ($request->filled('process_name')) {
            $query->where('d.process_name', trim((string) $request->query('process_name')));
        }
        if ($request->filled('nb') && is_numeric($request->query('nb'))) {
            $query->where('t.nb', (int) $request->query('nb'));
        }

        return $query;
    }

    private function exportRows(Request $request): array
    {
        return $this->baseRowsQuery($request)
            ->orderBy('component_type')
            ->orderBy('process_name')
            ->orderByRaw('COALESCE(nb, 0) asc')
            ->get(self::EXPORT_HEADERS)
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function upsertDefinition(array $payload): int
    {
        $componentType = $payload['component_type'];
        $processName = $payload['process_name'];

        $rateInner = $payload['price_tube_inner'];
        $rateOuter = $payload['price_tube_outer'];
        $hasInnerOuter = $payload['tube_outer'] !== null;

        $existing = DB::table(self::TABLE_DEFINITIONS)
            ->where('component_type', $componentType)
            ->where('process_name', $processName)
            ->first();

        if ($existing) {
            $updatePayload = [
                'has_inner_outer' => $hasInnerOuter,
                'unit' => $payload['unit'] ?? $existing->unit,
                'notes' => $payload['notes'] ?? $existing->notes,
                'updated_at' => now(),
            ];
            if ($this->hasDualRateColumns()) {
                $updatePayload['rate_inner_per_hour'] = $rateInner ?? $existing->rate_inner_per_hour ?? '52500.00';
                $updatePayload['rate_outer_per_hour'] = $rateOuter ?? $existing->rate_outer_per_hour ?? '52500.00';
            } elseif ($this->hasLegacyRateColumn()) {
                $updatePayload['rate_per_hour'] = $rateInner ?? $rateOuter ?? $existing->rate_per_hour ?? '52500.00';
            }
            DB::table(self::TABLE_DEFINITIONS)->where('id', $existing->id)->update($updatePayload);

            return (int) $existing->id;
        }

        $sequence = (int) DB::table(self::TABLE_DEFINITIONS)
            ->where('component_type', $componentType)
            ->max('sequence') + 1;

        $insertPayload = [
            'component_type' => $componentType,
            'process_name' => $processName,
            'sequence' => max($sequence, 1),
            'has_inner_outer' => $hasInnerOuter,
            'currency' => 'IDR',
            'unit' => $payload['unit'],
            'notes' => $payload['notes'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if ($this->hasDualRateColumns()) {
            $insertPayload['rate_inner_per_hour'] = $rateInner ?? '52500.00';
            $insertPayload['rate_outer_per_hour'] = $rateOuter ?? '52500.00';
        } elseif ($this->hasLegacyRateColumn()) {
            $insertPayload['rate_per_hour'] = $rateInner ?? $rateOuter ?? '52500.00';
        }

        return (int) DB::table(self::TABLE_DEFINITIONS)->insertGetId($insertPayload);
    }

    private function upsertTime(int $definitionId, array $payload): void
    {
        $query = DB::table(self::TABLE_TIMES)
            ->where('process_definition_id', $definitionId)
            ->whereNull('noc');

        if ($payload['nb'] === null) {
            $query->whereNull('nb');
        } else {
            $query->where('nb', $payload['nb']);
        }

        $existing = $query->first();
        $values = [
            'minutes_inner' => $payload['tube_inner'],
            'minutes_outer' => $payload['tube_outer'],
            'notes' => $payload['notes'],
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table(self::TABLE_TIMES)->where('id', $existing->id)->update($values);
            return;
        }

        DB::table(self::TABLE_TIMES)->insert(array_merge($values, [
            'process_definition_id' => $definitionId,
            'nb' => $payload['nb'],
            'noc' => null,
            'created_at' => now(),
        ]));
    }

    private function cleanupOrphanDefinition(int $definitionId): void
    {
        $hasChildren = DB::table(self::TABLE_TIMES)
            ->where('process_definition_id', $definitionId)
            ->exists();

        if (! $hasChildren) {
            DB::table(self::TABLE_DEFINITIONS)->where('id', $definitionId)->delete();
        }
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
                'price_tube_inner' => 52500.00,
                'tube_outer' => 30,
                'price_tube_outer' => 52500.00,
                'unit' => 'menit',
                'notes' => null,
            ],
            [
                'component_type' => 'EJM Production',
                'process_name' => 'Assembly',
                'nb' => 250,
                'tube_inner' => 2,
                'price_tube_inner' => 52500.00,
                'tube_outer' => 2,
                'price_tube_outer' => 52500.00,
                'unit' => 'menit',
                'notes' => 'Satuan proses assembly',
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

    private function hasDualRateColumns(): bool
    {
        return Schema::hasColumn(self::TABLE_DEFINITIONS, 'rate_inner_per_hour')
            && Schema::hasColumn(self::TABLE_DEFINITIONS, 'rate_outer_per_hour');
    }

    private function hasLegacyRateColumn(): bool
    {
        return Schema::hasColumn(self::TABLE_DEFINITIONS, 'rate_per_hour');
    }

    private function priceInnerSelectExpr(): string
    {
        if ($this->hasDualRateColumns()) {
            return 'COALESCE(d.rate_inner_per_hour, 52500)';
        }
        if ($this->hasLegacyRateColumn()) {
            return 'COALESCE(d.rate_per_hour, 52500)';
        }
        return '52500';
    }

    private function priceOuterSelectExpr(): string
    {
        if ($this->hasDualRateColumns()) {
            return 'COALESCE(d.rate_outer_per_hour, d.rate_inner_per_hour, 52500)';
        }
        if ($this->hasLegacyRateColumn()) {
            return 'COALESCE(d.rate_per_hour, 52500)';
        }
        return '52500';
    }

    private function normalizeRates(?string $inner, ?string $outer): array
    {
        if ($inner === null && $outer === null) {
            return ['52500.00', '52500.00'];
        }
        if ($inner === null) {
            $inner = $outer;
        }
        if ($outer === null) {
            $outer = $inner;
        }
        return [$inner, $outer];
    }
}
