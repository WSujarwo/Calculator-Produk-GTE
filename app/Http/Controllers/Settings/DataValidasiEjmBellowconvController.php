<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use ZipArchive;

class DataValidasiEjmBellowconvController extends Controller
{
    private const TABLE = 'validasi_dataejm_bellowconvs';
    private const ACTIVE_TAB = 'bellowconv';
    private const EXPORT_HEADERS = ['size', 'noc', 'naming', 'oalb_mm', 'bl_mm'];

    public function __construct()
    {
        $this->middleware('permission:settings.ejm-validation.view')->only(['index']);
        $this->middleware('permission:settings.ejm-validation.create')->only(['create', 'store']);
        $this->middleware('permission:settings.ejm-validation.edit')->only(['update']);
        $this->middleware('permission:settings.ejm-validation.delete')->only(['destroy']);
        $this->middleware('permission:settings.ejm-validation.import')->only(['import']);
        $this->middleware('permission:settings.ejm-validation.export')->only(['templateCsv', 'templateExcel', 'exportCsv', 'exportExcel']);
    }

    public function index(Request $request): View
    {
        $query = DB::table(self::TABLE);
        if ($request->filled('size') && is_numeric($request->query('size'))) {
            $query->where('size', (int) $request->query('size'));
        }
        if ($request->filled('noc') && is_numeric($request->query('noc'))) {
            $query->where('noc', (int) $request->query('noc'));
        }
        if ($request->filled('naming')) {
            $query->where('naming', 'like', '%' . trim((string) $request->query('naming')) . '%');
        }

        $rows = $query
            ->orderByRaw('COALESCE(size, 0) asc')
            ->orderByRaw('COALESCE(noc, 0) asc')
            ->paginate(100)
            ->withQueryString();

        $editing = null;
        if ($request->filled('edit')) {
            $editing = DB::table(self::TABLE)->where('id', (int) $request->query('edit'))->first();
        }

        return view('settings.ejm-validation-bellowconv', [
            'rows' => $rows,
            'editing' => $editing ? (array) $editing : null,
            'openCreateModal' => $request->boolean('create'),
            'validationMenus' => [
                ['key' => 'actual', 'label' => 'Actual Design Calculation', 'url' => route('setting.ejm-validation.index', ['tab' => 'actual'])],
                ['key' => 'can-length', 'label' => 'Calculation of CAN Length', 'url' => route('setting.ejm-validation.index', ['tab' => 'can-length'])],
                ['key' => 'proses', 'label' => 'Validasi Proses', 'url' => route('setting.ejm-validation-proses.index')],
                ['key' => self::ACTIVE_TAB, 'label' => 'Bellowconv', 'url' => route('setting.ejm-validation-bellowconv.index')],
                ['key' => 'expansion-joint', 'label' => 'Expansion Joint', 'url' => route('setting.ejm-expansion-joint.index')],
                ['key' => 'material', 'label' => 'Validasi Material EJM', 'url' => route('setting.ejm-validation-material.index')],
            ],
            'activeTab' => self::ACTIVE_TAB,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('setting.ejm-validation-bellowconv.index', ['create' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        DB::table(self::TABLE)->insert(array_merge($payload, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('setting.ejm-validation-bellowconv.index')
            ->with('success', 'Data validasi bellowconv berhasil ditambahkan.');
    }

    public function update(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        $payload = $this->validatedPayload($request, $id);
        DB::table(self::TABLE)->where('id', $id)->update(array_merge($payload, [
            'updated_at' => now(),
        ]));

        return redirect()->route('setting.ejm-validation-bellowconv.index')
            ->with('success', 'Data validasi bellowconv berhasil diupdate.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        DB::table(self::TABLE)->where('id', $id)->delete();

        return redirect()->route('setting.ejm-validation-bellowconv.index')
            ->with('success', 'Data validasi bellowconv berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $ext = strtolower((string) $validated['file']->getClientOriginalExtension());
        if ($ext === 'xlsx' && ! class_exists(ZipArchive::class)) {
            return redirect()->route('setting.ejm-validation-bellowconv.index')
                ->with('error', 'Import XLSX membutuhkan ekstensi PHP zip (ZipArchive).');
        }

        $rows = $this->readRows($validated['file']);
        if (empty($rows)) {
            return redirect()->route('setting.ejm-validation-bellowconv.index')
                ->with('error', 'Tidak ada data valid pada file import.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $size = $this->toInteger($row['size'] ?? null);
                $noc = $this->toInteger($row['noc'] ?? null);
                $naming = $this->cleanString($row['naming'] ?? null);
                $oalbMm = $this->toDecimal($row['oalb_mm'] ?? null);
                $blMm = $this->toDecimal($row['bl_mm'] ?? null);

                if ($size === null || $noc === null || $naming === null || $oalbMm === null || $blMm === null) {
                    $skipped++;
                    continue;
                }

                $payload = [
                    'size' => $size,
                    'noc' => $noc,
                    'naming' => $naming,
                    'oalb_mm' => $oalbMm,
                    'bl_mm' => $blMm,
                    'updated_at' => now(),
                ];

                $existing = DB::table(self::TABLE)
                    ->where('size', $size)
                    ->where('noc', $noc)
                    ->first();

                if ($existing) {
                    DB::table(self::TABLE)->where('id', $existing->id)->update($payload);
                    $updated++;
                    continue;
                }

                DB::table(self::TABLE)->insert(array_merge($payload, [
                    'created_at' => now(),
                ]));
                $created++;
            }
        });

        return redirect()->route('setting.ejm-validation-bellowconv.index')
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    public function templateCsv()
    {
        return $this->buildCsvResponse($this->sampleRows(), 'ejm_validasi_bellowconv_template.csv');
    }

    public function exportCsv(Request $request)
    {
        return $this->buildCsvResponse($this->exportRows($request), 'ejm_validasi_bellowconv_export.csv');
    }

    public function templateExcel()
    {
        $html = $this->buildExcelHtml($this->sampleRows(), 'Template Validasi Bellowconv EJM');
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validasi_bellowconv_template.xls',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $html = $this->buildExcelHtml($this->exportRows($request), 'Export Validasi Bellowconv EJM');
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validasi_bellowconv_export.xls',
        ]);
    }

    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $nocUnique = Rule::unique(self::TABLE, 'noc')
            ->where(fn ($query) => $query->where('size', (int) $request->input('size')));
        if ($ignoreId !== null) {
            $nocUnique = $nocUnique->ignore($ignoreId);
        }

        $data = $request->validate([
            'size' => ['required', 'integer', 'min:1'],
            'noc' => ['required', 'integer', 'min:1', $nocUnique],
            'naming' => ['required', 'string', 'max:50'],
            'oalb_mm' => ['required', 'numeric'],
            'bl_mm' => ['required', 'numeric'],
        ]);

        return [
            'size' => (int) $data['size'],
            'noc' => (int) $data['noc'],
            'naming' => trim($data['naming']),
            'oalb_mm' => $this->toDecimal($data['oalb_mm']),
            'bl_mm' => $this->toDecimal($data['bl_mm']),
        ];
    }

    private function exportRows(Request $request): array
    {
        $query = DB::table(self::TABLE);
        if ($request->filled('size') && is_numeric($request->query('size'))) {
            $query->where('size', (int) $request->query('size'));
        }
        if ($request->filled('noc') && is_numeric($request->query('noc'))) {
            $query->where('noc', (int) $request->query('noc'));
        }
        if ($request->filled('naming')) {
            $query->where('naming', 'like', '%' . trim((string) $request->query('naming')) . '%');
        }

        return $query
            ->orderByRaw('COALESCE(size, 0) asc')
            ->orderByRaw('COALESCE(noc, 0) asc')
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
        $thead = '<tr style="background:#d9ead3;font-weight:700;">';
        foreach (self::EXPORT_HEADERS as $header) {
            $thead .= '<th>' . e(strtoupper($header)) . '</th>';
        }
        $thead .= '</tr>';

        $tbody = '';
        foreach ($rows as $row) {
            $tbody .= '<tr>';
            foreach (self::EXPORT_HEADERS as $header) {
                $tbody .= '<td>' . e((string) ($row[$header] ?? '')) . '</td>';
            }
            $tbody .= '</tr>';
        }

        return '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;}th,td{border:1px solid #777;padding:4px 6px;text-align:center;}</style></head><body><h3>' . e($title) . '</h3><table><thead>' . $thead . '</thead><tbody>' . $tbody . '</tbody></table></body></html>';
    }

    private function sampleRows(): array
    {
        return [
            ['size' => 100, 'noc' => 14, 'naming' => 'Single Ply', 'oalb_mm' => 220.00, 'bl_mm' => 170.00],
            ['size' => 150, 'noc' => 18, 'naming' => 'Multi Ply', 'oalb_mm' => 280.00, 'bl_mm' => 230.00],
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
        if ($indices['size'] === null || $indices['noc'] === null) {
            return [];
        }

        $rows = [];
        foreach (array_slice($rawRows, 1) as $row) {
            $rows[] = [
                'size' => $this->readCellByIndex($row, $indices['size']),
                'noc' => $this->readCellByIndex($row, $indices['noc']),
                'naming' => $this->readCellByIndex($row, $indices['naming']),
                'oalb_mm' => $this->readCellByIndex($row, $indices['oalb_mm']),
                'bl_mm' => $this->readCellByIndex($row, $indices['bl_mm']),
            ];
        }

        return $rows;
    }

    private function resolveColumnMap(array $headers): array
    {
        $aliases = [
            'size' => ['size', 'nb'],
            'noc' => ['noc', 'numberofconvolution'],
            'naming' => ['naming', 'name'],
            'oalb_mm' => ['oalbmm', 'oalb'],
            'bl_mm' => ['blmm', 'bl'],
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
