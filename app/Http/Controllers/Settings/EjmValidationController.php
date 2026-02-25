<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use ZipArchive;

class EjmValidationController extends Controller
{
    private const TABLE = 'validasi_dataejm_can_length_calculations';

    public function __construct()
    {
        $this->middleware('permission:settings.ejm-validation.view')->only(['index']);
        $this->middleware('permission:settings.ejm-validation.create')->only(['create', 'store']);
        $this->middleware('permission:settings.ejm-validation.edit')->only(['update']);
        $this->middleware('permission:settings.ejm-validation.import')->only(['import']);
        $this->middleware('permission:settings.ejm-validation.export')->only(['templateCsv', 'templateExcel']);
    }

    public function index(Request $request): View
    {
        $rows = DB::table(self::TABLE)->orderBy('nb')->paginate(50)->withQueryString();
        $editing = null;
        $activeTab = (string) $request->query('tab', 'actual');
        if (! in_array($activeTab, ['actual', 'can-length'], true)) {
            $activeTab = 'actual';
        }

        if ($request->filled('edit')) {
            $editing = DB::table(self::TABLE)->where('id', (int) $request->query('edit'))->first();
        }

        $validationMenus = [
            ['key' => 'actual', 'label' => 'Actual Design Calculation', 'url' => route('setting.ejm-validation.index', ['tab' => 'actual'])],
            ['key' => 'can-length', 'label' => 'Calculation of CAN Length', 'url' => route('setting.ejm-validation.index', ['tab' => 'can-length'])],
            ['key' => 'proses', 'label' => 'Validasi Proses', 'url' => route('setting.ejm-validation-proses.index')],
            ['key' => 'expansion-joint', 'label' => 'Expansion Joint', 'url' => route('setting.ejm-expansion-joint.index')],
        ];

        return view('settings.ejm-validation', [
            'rows' => $rows,
            'editing' => $editing ? (array) $editing : null,
            'openCreateModal' => $request->boolean('create'),
            'validationMenus' => $validationMenus,
            'activeTab' => $activeTab,
        ]);
    }

    public function create()
    {
        $tab = request()->query('tab', 'actual');
        if (! in_array($tab, ['actual', 'can-length'], true)) {
            $tab = 'actual';
        }

        return redirect()->route('setting.ejm-validation.index', ['create' => 1, 'tab' => $tab]);
    }

    public function store(Request $request)
    {
        $payload = $this->validatedPayload($request);
        $nb = (int) $payload['nb'];

        $existing = DB::table(self::TABLE)->where('nb', $nb)->first();
        if ($existing) {
            DB::table(self::TABLE)->where('id', $existing->id)->update(array_merge(
                $payload,
                ['updated_at' => now()]
            ));

            return redirect()->route('setting.ejm-validation.index', ['tab' => $this->resolveTab($request)])
                ->with('success', "Data dengan NB {$nb} sudah ada, data berhasil diupdate.");
        }

        DB::table(self::TABLE)->insert(array_merge(
            $payload,
            ['created_at' => now(), 'updated_at' => now()]
        ));

        return redirect()->route('setting.ejm-validation.index', ['tab' => $this->resolveTab($request)])
            ->with('success', "Data NB {$nb} berhasil ditambahkan.");
    }

    public function update(Request $request)
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        $payload = $this->validatedPayload($request, $id);

        DB::table(self::TABLE)
            ->where('id', $id)
            ->update(array_merge($payload, ['updated_at' => now()]));

        return redirect()->route('setting.ejm-validation.index', ['tab' => $this->resolveTab($request)])
            ->with('success', 'Data validasi EJM berhasil diupdate.');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $ext = strtolower((string) $validated['file']->getClientOriginalExtension());
        if ($ext === 'xlsx' && ! class_exists(ZipArchive::class)) {
            return redirect()->route('setting.ejm-validation.index')
                ->with('error', 'Import XLSX membutuhkan ekstensi PHP zip (ZipArchive). Gunakan CSV atau aktifkan php_zip.');
        }

        $rows = $this->readRows($validated['file']);
        if (empty($rows)) {
            return redirect()->route('setting.ejm-validation.index')
                ->with('error', 'Tidak ada data valid pada file import.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $nb = $this->toInteger($row['nb'] ?? null);
                if ($nb === null) {
                    $skipped++;
                    continue;
                }

                $payload = [
                    'product_id' => $this->toInteger($row['product_id'] ?? null) ?? 1,
                    'shape_id' => $this->toInteger($row['shape_id'] ?? null) ?? 1,
                    'product_shapes_id' => $this->toInteger($row['product_shapes_id'] ?? null) ?? 1,
                    'size_inch' => $this->toInteger($row['size_inch'] ?? null) ?? 0,
                    'nb' => $nb,
                    'tl_width' => $this->toDecimal($row['tl_width'] ?? null),
                    'tl_qty' => $this->toInteger($row['tl_qty'] ?? null),
                    'tl_total' => $this->toDecimal($row['tl_total'] ?? null),
                    'spacer_width' => $this->toDecimal($row['spacer_width'] ?? null),
                    'spacer_qty' => $this->toInteger($row['spacer_qty'] ?? null),
                    'spacer_total' => $this->toDecimal($row['spacer_total'] ?? null),
                    'pitch_ejma' => $this->toDecimal($row['pitch_ejma'] ?? null),
                    'pitch_gte' => $this->toDecimal($row['pitch_gte'] ?? null),
                    'tool_radius_qty' => $this->toInteger($row['tool_radius_qty'] ?? null),
                    'tool_radius_total' => $this->toDecimal($row['tool_radius_total'] ?? null),
                    'tl_spacer_tool_total' => $this->toDecimal($row['tl_spacer_tool_total'] ?? null),
                    'gap' => $this->toDecimal($row['gap'] ?? null),
                    'can_length' => $this->toDecimal($row['can_length'] ?? null),
                    'id_bellows' => $this->toDecimal($row['id_bellows'] ?? null),
                    'thk' => $this->toDecimal($row['thk'] ?? null),
                    'ly' => $this->toDecimal($row['ly'] ?? null),
                    'ejma_circm_1' => $this->toDecimal($row['ejma_circm_1'] ?? null),
                    'ejma_circm_2' => $this->toDecimal($row['ejma_circm_2'] ?? null),
                    'ejma_gap' => $this->toDecimal($row['ejma_gap'] ?? null),
                    'manual_circm_1' => $this->toDecimal($row['manual_circm_1'] ?? null),
                    'manual_circm_2' => $this->toDecimal($row['manual_circm_2'] ?? null),
                    'manual_gap' => $this->toDecimal($row['manual_gap'] ?? null),
                    'correction_circm_1' => $this->toDecimal($row['correction_circm_1'] ?? null),
                    'correction_circm_2' => $this->toDecimal($row['correction_circm_2'] ?? null),
                    'correction_gap' => $this->toDecimal($row['correction_gap'] ?? null),
                    'correction_circm_2_actual' => $this->toDecimal($row['correction_circm_2_actual'] ?? null),
                    'calculation_tl' => $this->toDecimal($row['calculation_tl'] ?? null),
                    'can_length_actual' => $this->toDecimal($row['can_length_actual'] ?? null),
                    'is_active' => $this->toBoolean($row['is_active'] ?? 1),
                    'notes' => $this->cleanString($row['notes'] ?? null),
                    'updated_at' => now(),
                ];

                $existing = DB::table(self::TABLE)->where('nb', $nb)->first();
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

        return redirect()->route('setting.ejm-validation.index', ['tab' => $this->resolveTab($request)])
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    public function templateCsv()
    {
        $headers = [
            'product_id', 'shape_id', 'product_shapes_id', 'size_inch', 'nb',
            'tl_width', 'tl_qty', 'tl_total',
            'spacer_width', 'spacer_qty', 'spacer_total',
            'pitch_ejma', 'pitch_gte', 'tool_radius_qty', 'tool_radius_total',
            'tl_spacer_tool_total', 'gap', 'can_length',
            'id_bellows', 'thk', 'ly', 'ejma_circm_1', 'ejma_circm_2', 'ejma_gap',
            'manual_circm_1', 'manual_circm_2', 'manual_gap',
            'correction_circm_1', 'correction_circm_2', 'correction_gap',
            'correction_circm_2_actual', 'calculation_tl', 'can_length_actual',
            'is_active', 'notes',
        ];

        $lines = [];
        $lines[] = implode(',', $headers);

        foreach ($this->sampleRows() as $row) {
            $line = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                $escaped = str_replace('"', '""', (string) $value);
                $line[] = '"' . $escaped . '"';
            }
            $lines[] = implode(',', $line);
        }

        $content = "\xEF\xBB\xBF" . implode("\n", $lines) . "\n";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validation_template.csv',
        ]);
    }

    public function templateExcel()
    {
        $rows = $this->sampleRows();

        $html = view('settings.ejm-validation-template-xls', compact('rows'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_validation_template.xls',
        ]);
    }

    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $nbRule = 'unique:' . self::TABLE . ',nb';
        if ($ignoreId !== null) {
            $nbRule .= ',' . $ignoreId;
        }

        $data = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'shape_id' => ['required', 'integer', 'min:1'],
            'product_shapes_id' => ['required', 'integer', 'min:1'],
            'size_inch' => ['required', 'integer', 'min:1'],
            'nb' => ['required', 'integer', 'min:1', $nbRule],
            'tl_width' => ['nullable', 'numeric'],
            'tl_qty' => ['nullable', 'integer', 'min:0'],
            'tl_total' => ['nullable', 'numeric'],
            'spacer_width' => ['nullable', 'numeric'],
            'spacer_qty' => ['nullable', 'integer', 'min:0'],
            'spacer_total' => ['nullable', 'numeric'],
            'pitch_ejma' => ['nullable', 'numeric'],
            'pitch_gte' => ['nullable', 'numeric'],
            'tool_radius_qty' => ['nullable', 'integer', 'min:0'],
            'tool_radius_total' => ['nullable', 'numeric'],
            'tl_spacer_tool_total' => ['nullable', 'numeric'],
            'gap' => ['nullable', 'numeric'],
            'can_length' => ['nullable', 'numeric'],
            'id_bellows' => ['nullable', 'numeric'],
            'thk' => ['nullable', 'numeric'],
            'ly' => ['nullable', 'numeric'],
            'ejma_circm_1' => ['nullable', 'numeric'],
            'ejma_circm_2' => ['nullable', 'numeric'],
            'ejma_gap' => ['nullable', 'numeric'],
            'manual_circm_1' => ['nullable', 'numeric'],
            'manual_circm_2' => ['nullable', 'numeric'],
            'manual_gap' => ['nullable', 'numeric'],
            'correction_circm_1' => ['nullable', 'numeric'],
            'correction_circm_2' => ['nullable', 'numeric'],
            'correction_gap' => ['nullable', 'numeric'],
            'correction_circm_2_actual' => ['nullable', 'numeric'],
            'calculation_tl' => ['nullable', 'numeric'],
            'can_length_actual' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        return [
            'product_id' => (int) $data['product_id'],
            'shape_id' => (int) $data['shape_id'],
            'product_shapes_id' => (int) $data['product_shapes_id'],
            'size_inch' => (int) $data['size_inch'],
            'nb' => (int) $data['nb'],
            'tl_width' => $this->toDecimal($data['tl_width'] ?? null),
            'tl_qty' => $this->toInteger($data['tl_qty'] ?? null),
            'tl_total' => $this->toDecimal($data['tl_total'] ?? null),
            'spacer_width' => $this->toDecimal($data['spacer_width'] ?? null),
            'spacer_qty' => $this->toInteger($data['spacer_qty'] ?? null),
            'spacer_total' => $this->toDecimal($data['spacer_total'] ?? null),
            'pitch_ejma' => $this->toDecimal($data['pitch_ejma'] ?? null),
            'pitch_gte' => $this->toDecimal($data['pitch_gte'] ?? null),
            'tool_radius_qty' => $this->toInteger($data['tool_radius_qty'] ?? null),
            'tool_radius_total' => $this->toDecimal($data['tool_radius_total'] ?? null),
            'tl_spacer_tool_total' => $this->toDecimal($data['tl_spacer_tool_total'] ?? null),
            'gap' => $this->toDecimal($data['gap'] ?? null),
            'can_length' => $this->toDecimal($data['can_length'] ?? null),
            'id_bellows' => $this->toDecimal($data['id_bellows'] ?? null),
            'thk' => $this->toDecimal($data['thk'] ?? null),
            'ly' => $this->toDecimal($data['ly'] ?? null),
            'ejma_circm_1' => $this->toDecimal($data['ejma_circm_1'] ?? null),
            'ejma_circm_2' => $this->toDecimal($data['ejma_circm_2'] ?? null),
            'ejma_gap' => $this->toDecimal($data['ejma_gap'] ?? null),
            'manual_circm_1' => $this->toDecimal($data['manual_circm_1'] ?? null),
            'manual_circm_2' => $this->toDecimal($data['manual_circm_2'] ?? null),
            'manual_gap' => $this->toDecimal($data['manual_gap'] ?? null),
            'correction_circm_1' => $this->toDecimal($data['correction_circm_1'] ?? null),
            'correction_circm_2' => $this->toDecimal($data['correction_circm_2'] ?? null),
            'correction_gap' => $this->toDecimal($data['correction_gap'] ?? null),
            'correction_circm_2_actual' => $this->toDecimal($data['correction_circm_2_actual'] ?? null),
            'calculation_tl' => $this->toDecimal($data['calculation_tl'] ?? null),
            'can_length_actual' => $this->toDecimal($data['can_length_actual'] ?? null),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'notes' => $this->cleanString($data['notes'] ?? null),
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

        if ($indices['nb'] === null) {
            return [];
        }

        $rows = [];
        foreach (array_slice($rawRows, 1) as $row) {
            $rows[] = [
                'product_id' => $this->readCellByIndex($row, $indices['product_id']),
                'shape_id' => $this->readCellByIndex($row, $indices['shape_id']),
                'product_shapes_id' => $this->readCellByIndex($row, $indices['product_shapes_id']),
                'size_inch' => $this->readCellByIndex($row, $indices['size_inch']),
                'nb' => $this->readCellByIndex($row, $indices['nb']),
                'tl_width' => $this->readCellByIndex($row, $indices['tl_width']),
                'tl_qty' => $this->readCellByIndex($row, $indices['tl_qty']),
                'tl_total' => $this->readCellByIndex($row, $indices['tl_total']),
                'spacer_width' => $this->readCellByIndex($row, $indices['spacer_width']),
                'spacer_qty' => $this->readCellByIndex($row, $indices['spacer_qty']),
                'spacer_total' => $this->readCellByIndex($row, $indices['spacer_total']),
                'pitch_ejma' => $this->readCellByIndex($row, $indices['pitch_ejma']),
                'pitch_gte' => $this->readCellByIndex($row, $indices['pitch_gte']),
                'tool_radius_qty' => $this->readCellByIndex($row, $indices['tool_radius_qty']),
                'tool_radius_total' => $this->readCellByIndex($row, $indices['tool_radius_total']),
                'tl_spacer_tool_total' => $this->readCellByIndex($row, $indices['tl_spacer_tool_total']),
                'gap' => $this->readCellByIndex($row, $indices['gap']),
                'can_length' => $this->readCellByIndex($row, $indices['can_length']),
                'id_bellows' => $this->readCellByIndex($row, $indices['id_bellows']),
                'thk' => $this->readCellByIndex($row, $indices['thk']),
                'ly' => $this->readCellByIndex($row, $indices['ly']),
                'ejma_circm_1' => $this->readCellByIndex($row, $indices['ejma_circm_1']),
                'ejma_circm_2' => $this->readCellByIndex($row, $indices['ejma_circm_2']),
                'ejma_gap' => $this->readCellByIndex($row, $indices['ejma_gap']),
                'manual_circm_1' => $this->readCellByIndex($row, $indices['manual_circm_1']),
                'manual_circm_2' => $this->readCellByIndex($row, $indices['manual_circm_2']),
                'manual_gap' => $this->readCellByIndex($row, $indices['manual_gap']),
                'correction_circm_1' => $this->readCellByIndex($row, $indices['correction_circm_1']),
                'correction_circm_2' => $this->readCellByIndex($row, $indices['correction_circm_2']),
                'correction_gap' => $this->readCellByIndex($row, $indices['correction_gap']),
                'correction_circm_2_actual' => $this->readCellByIndex($row, $indices['correction_circm_2_actual']),
                'calculation_tl' => $this->readCellByIndex($row, $indices['calculation_tl']),
                'can_length_actual' => $this->readCellByIndex($row, $indices['can_length_actual']),
                'is_active' => $this->readCellByIndex($row, $indices['is_active']),
                'notes' => $this->readCellByIndex($row, $indices['notes']),
            ];
        }

        return $rows;
    }

    private function resolveColumnMap(array $headers): array
    {
        $aliases = [
            'product_id' => ['productid'],
            'shape_id' => ['shapeid'],
            'product_shapes_id' => ['productshapesid', 'product_shape_id'],
            'size_inch' => ['sizeinch', 'size', 'inch', 'inche'],
            'nb' => ['nb'],
            'tl_width' => ['tlwidth'],
            'tl_qty' => ['tlqty'],
            'tl_total' => ['tltotal'],
            'spacer_width' => ['spacerwidth'],
            'spacer_qty' => ['spacerqty'],
            'spacer_total' => ['spacertotal'],
            'pitch_ejma' => ['pitchejma'],
            'pitch_gte' => ['pitchgte'],
            'tool_radius_qty' => ['toolradiusqty'],
            'tool_radius_total' => ['toolradiustotal'],
            'tl_spacer_tool_total' => ['tlspacertooltotal'],
            'gap' => ['gap'],
            'can_length' => ['canlength'],
            'id_bellows' => ['idbellows'],
            'thk' => ['thk'],
            'ly' => ['ly'],
            'ejma_circm_1' => ['ejmacircm1'],
            'ejma_circm_2' => ['ejmacircm2'],
            'ejma_gap' => ['ejmagap'],
            'manual_circm_1' => ['manualcircm1'],
            'manual_circm_2' => ['manualcircm2'],
            'manual_gap' => ['manualgap'],
            'correction_circm_1' => ['correctioncircm1'],
            'correction_circm_2' => ['correctioncircm2'],
            'correction_gap' => ['correctiongap'],
            'correction_circm_2_actual' => ['correctioncircm2actual'],
            'calculation_tl' => ['calculationtl'],
            'can_length_actual' => ['canlengthactual'],
            'is_active' => ['isactive'],
            'notes' => ['notes'],
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

    private function sampleRows(): array
    {
        return [
            [
                'product_id' => 1, 'shape_id' => 1, 'product_shapes_id' => 1, 'size_inch' => 4, 'nb' => 100,
                'tl_width' => 40, 'tl_qty' => 2, 'tl_total' => 80,
                'spacer_width' => 22.2, 'spacer_qty' => 14, 'spacer_total' => 310.8,
                'pitch_ejma' => 12.20, 'pitch_gte' => 12.00, 'tool_radius_qty' => 13, 'tool_radius_total' => 156.00,
                'tl_spacer_tool_total' => 546.80, 'gap' => 10, 'can_length' => 556.80,
                'id_bellows' => 114.00, 'thk' => 0.50, 'ly' => 2.00,
                'ejma_circm_1' => 358.00, 'ejma_circm_2' => 352.00, 'ejma_gap' => 6.00,
                'manual_circm_1' => 358.14, 'manual_circm_2' => 361.28, 'manual_gap' => 3.14,
                'correction_circm_1' => 356.28, 'correction_circm_2' => 361.28, 'correction_gap' => 5.00,
                'correction_circm_2_actual' => 366.28, 'calculation_tl' => 571.15, 'can_length_actual' => 556.80,
                'is_active' => 1, 'notes' => '',
            ],
            [
                'product_id' => 1, 'shape_id' => 1, 'product_shapes_id' => 1, 'size_inch' => 6, 'nb' => 150,
                'tl_width' => 40, 'tl_qty' => 2, 'tl_total' => 80,
                'spacer_width' => 25.2, 'spacer_qty' => 14, 'spacer_total' => 352.8,
                'pitch_ejma' => 14.70, 'pitch_gte' => 14.15, 'tool_radius_qty' => 13, 'tool_radius_total' => 183.95,
                'tl_spacer_tool_total' => 616.75, 'gap' => 10, 'can_length' => 626.75,
                'id_bellows' => 168.00, 'thk' => 0.50, 'ly' => 2.00,
                'ejma_circm_1' => 528.00, 'ejma_circm_2' => 522.00, 'ejma_gap' => 6.00,
                'manual_circm_1' => 527.79, 'manual_circm_2' => 530.93, 'manual_gap' => 3.14,
                'correction_circm_1' => 525.93, 'correction_circm_2' => 530.93, 'correction_gap' => 5.00,
                'correction_circm_2_actual' => 535.93, 'calculation_tl' => 647.29, 'can_length_actual' => 626.75,
                'is_active' => 1, 'notes' => '',
            ],
        ];
    }

    private function resolveTab(Request $request): string
    {
        $tab = (string) $request->input('tab', $request->query('tab', 'actual'));
        return in_array($tab, ['actual', 'can-length'], true) ? $tab : 'actual';
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

    private function toBoolean(mixed $value): bool
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
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
