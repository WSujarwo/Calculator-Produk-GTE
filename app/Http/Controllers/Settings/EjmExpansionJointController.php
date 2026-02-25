<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use ZipArchive;

class EjmExpansionJointController extends Controller
{
    private const TABLE = 'validasi_dataejm_expansion_joint';

    public function index(Request $request): View
    {
        $rows = DB::table(self::TABLE)
            ->orderByRaw('COALESCE(nb, 0) asc')
            ->orderBy('size_code')
            ->paginate(50)
            ->withQueryString();

        $editing = null;
        if ($request->filled('edit')) {
            $editing = DB::table(self::TABLE)->where('id', (int) $request->query('edit'))->first();
        }

        $validationMenus = [
            ['key' => 'actual', 'label' => 'Actual Design Calculation', 'url' => route('setting.ejm-validation.index', ['tab' => 'actual'])],
            ['key' => 'can-length', 'label' => 'Calculation of CAN Length', 'url' => route('setting.ejm-validation.index', ['tab' => 'can-length'])],
            ['key' => 'expansion-joint', 'label' => 'Expansion Joint', 'url' => route('setting.ejm-expansion-joint.index')],
        ];

        return view('settings.ejm-expansion-joint', [
            'rows' => $rows,
            'editing' => $editing ? (array) $editing : null,
            'openCreateModal' => $request->boolean('create'),
            'validationMenus' => $validationMenus,
            'activeTab' => 'expansion-joint',
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('setting.ejm-expansion-joint.index', ['create' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        $payload['created_at'] = now();
        $payload['updated_at'] = now();

        DB::table(self::TABLE)->insert($payload);

        return redirect()->route('setting.ejm-expansion-joint.index')
            ->with('success', 'Data expansion joint berhasil ditambahkan.');
    }

    public function update(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id');
        abort_if($id <= 0, 422, 'ID data tidak valid.');

        $payload = $this->validatedPayload($request, $id);
        $payload['updated_at'] = now();

        DB::table(self::TABLE)->where('id', $id)->update($payload);

        return redirect()->route('setting.ejm-expansion-joint.index')
            ->with('success', 'Data expansion joint berhasil diupdate.');
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $ext = strtolower((string) $validated['file']->getClientOriginalExtension());
        if ($ext === 'xlsx' && ! class_exists(ZipArchive::class)) {
            return redirect()->route('setting.ejm-expansion-joint.index')
                ->with('error', 'Import XLSX membutuhkan ekstensi PHP zip (ZipArchive). Gunakan CSV atau aktifkan php_zip.');
        }

        $rows = $this->readRows($validated['file']);
        if (empty($rows)) {
            return redirect()->route('setting.ejm-expansion-joint.index')
                ->with('error', 'Tidak ada data valid pada file import.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, &$created, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $shapeCode = strtoupper(trim((string) ($row['shape_code'] ?? 'RND')));
                $sizeCode = $this->toNullString($row['size_code'] ?? null);

                if ($sizeCode === null) {
                    $skipped++;
                    continue;
                }

                $payload = [
                    'standard_version_id' => $this->toInt($row['standard_version_id'] ?? null),
                    'shape_code' => $shapeCode,
                    'size_code' => $sizeCode,
                    'nb' => $this->toInt($row['nb'] ?? null),
                    'width_mm' => $this->toDecimal($row['width_mm'] ?? null),
                    'length_mm' => $this->toDecimal($row['length_mm'] ?? null),
                    'tl_per_side_mm' => $this->toDecimal($row['tl_per_side_mm'] ?? null),
                    'tl_qty' => $this->toInt($row['tl_qty'] ?? null),
                    'spacer_width_mm' => $this->toDecimal($row['spacer_width_mm'] ?? null),
                    'spacer_qty' => $this->toInt($row['spacer_qty'] ?? null),
                    'tool_radius_mm' => $this->toDecimal($row['tool_radius_mm'] ?? null),
                    'tool_radius_qty' => $this->toInt($row['tool_radius_qty'] ?? null),
                    'pitch_ejma_mm' => $this->toDecimal($row['pitch_ejma_mm'] ?? null),
                    'pitch_gte_mm' => $this->toDecimal($row['pitch_gte_mm'] ?? null),
                    'total_tl_mm' => $this->toDecimal($row['total_tl_mm'] ?? null),
                    'total_spacer_mm' => $this->toDecimal($row['total_spacer_mm'] ?? null),
                    'total_tool_radius_mm' => $this->toDecimal($row['total_tool_radius_mm'] ?? null),
                    'tl_spacer_tool_total_mm' => $this->toDecimal($row['tl_spacer_tool_total_mm'] ?? null),
                    'gap_mm' => $this->toDecimal($row['gap_mm'] ?? null),
                    'can_length_mm' => $this->toDecimal($row['can_length_mm'] ?? null),
                    'effective_from' => $this->toNullString($row['effective_from'] ?? null),
                    'effective_to' => $this->toNullString($row['effective_to'] ?? null),
                    'is_active' => $this->toBoolean($row['is_active'] ?? 1),
                    'notes' => $this->toNullString($row['notes'] ?? null),
                    'updated_at' => now(),
                ];

                $existing = DB::table(self::TABLE)
                    ->where('shape_code', $shapeCode)
                    ->where('size_code', $sizeCode)
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

        return redirect()->route('setting.ejm-expansion-joint.index')
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'standard_version_id' => ['nullable', 'integer', 'min:1'],
            'shape_code' => ['required', 'string', 'max:30'],
            'size_code' => ['nullable', 'string', 'max:80'],
            'nb' => ['nullable', 'integer', 'min:0'],
            'width_mm' => ['nullable', 'numeric'],
            'length_mm' => ['nullable', 'numeric'],
            'tl_per_side_mm' => ['nullable', 'numeric'],
            'tl_qty' => ['nullable', 'integer', 'min:0'],
            'spacer_width_mm' => ['nullable', 'numeric'],
            'spacer_qty' => ['nullable', 'integer', 'min:0'],
            'tool_radius_mm' => ['nullable', 'numeric'],
            'tool_radius_qty' => ['nullable', 'integer', 'min:0'],
            'pitch_ejma_mm' => ['nullable', 'numeric'],
            'pitch_gte_mm' => ['nullable', 'numeric'],
            'total_tl_mm' => ['nullable', 'numeric'],
            'total_spacer_mm' => ['nullable', 'numeric'],
            'total_tool_radius_mm' => ['nullable', 'numeric'],
            'tl_spacer_tool_total_mm' => ['nullable', 'numeric'],
            'gap_mm' => ['nullable', 'numeric'],
            'can_length_mm' => ['nullable', 'numeric'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        return [
            'standard_version_id' => $this->toInt($data['standard_version_id'] ?? null),
            'shape_code' => strtoupper(trim((string) $data['shape_code'])),
            'size_code' => $this->toNullString($data['size_code'] ?? null),
            'nb' => $this->toInt($data['nb'] ?? null),
            'width_mm' => $this->toDecimal($data['width_mm'] ?? null),
            'length_mm' => $this->toDecimal($data['length_mm'] ?? null),
            'tl_per_side_mm' => $this->toDecimal($data['tl_per_side_mm'] ?? null),
            'tl_qty' => $this->toInt($data['tl_qty'] ?? null),
            'spacer_width_mm' => $this->toDecimal($data['spacer_width_mm'] ?? null),
            'spacer_qty' => $this->toInt($data['spacer_qty'] ?? null),
            'tool_radius_mm' => $this->toDecimal($data['tool_radius_mm'] ?? null),
            'tool_radius_qty' => $this->toInt($data['tool_radius_qty'] ?? null),
            'pitch_ejma_mm' => $this->toDecimal($data['pitch_ejma_mm'] ?? null),
            'pitch_gte_mm' => $this->toDecimal($data['pitch_gte_mm'] ?? null),
            'total_tl_mm' => $this->toDecimal($data['total_tl_mm'] ?? null),
            'total_spacer_mm' => $this->toDecimal($data['total_spacer_mm'] ?? null),
            'total_tool_radius_mm' => $this->toDecimal($data['total_tool_radius_mm'] ?? null),
            'tl_spacer_tool_total_mm' => $this->toDecimal($data['tl_spacer_tool_total_mm'] ?? null),
            'gap_mm' => $this->toDecimal($data['gap_mm'] ?? null),
            'can_length_mm' => $this->toDecimal($data['can_length_mm'] ?? null),
            'effective_from' => $data['effective_from'] ?? null,
            'effective_to' => $data['effective_to'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'notes' => $this->toNullString($data['notes'] ?? null),
        ];
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function toDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
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

        return number_format((float) $normalized, 2, '.', '');
    }

    private function toNullString(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' ? null : $v;
    }

    private function toBoolean(mixed $value): bool
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
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
        if ($indices['size_code'] === null) {
            return [];
        }

        $rows = [];
        foreach (array_slice($rawRows, 1) as $row) {
            $rows[] = [
                'standard_version_id' => $this->readCellByIndex($row, $indices['standard_version_id']),
                'shape_code' => $this->readCellByIndex($row, $indices['shape_code']),
                'size_code' => $this->readCellByIndex($row, $indices['size_code']),
                'nb' => $this->readCellByIndex($row, $indices['nb']),
                'width_mm' => $this->readCellByIndex($row, $indices['width_mm']),
                'length_mm' => $this->readCellByIndex($row, $indices['length_mm']),
                'tl_per_side_mm' => $this->readCellByIndex($row, $indices['tl_per_side_mm']),
                'tl_qty' => $this->readCellByIndex($row, $indices['tl_qty']),
                'spacer_width_mm' => $this->readCellByIndex($row, $indices['spacer_width_mm']),
                'spacer_qty' => $this->readCellByIndex($row, $indices['spacer_qty']),
                'tool_radius_mm' => $this->readCellByIndex($row, $indices['tool_radius_mm']),
                'tool_radius_qty' => $this->readCellByIndex($row, $indices['tool_radius_qty']),
                'pitch_ejma_mm' => $this->readCellByIndex($row, $indices['pitch_ejma_mm']),
                'pitch_gte_mm' => $this->readCellByIndex($row, $indices['pitch_gte_mm']),
                'total_tl_mm' => $this->readCellByIndex($row, $indices['total_tl_mm']),
                'total_spacer_mm' => $this->readCellByIndex($row, $indices['total_spacer_mm']),
                'total_tool_radius_mm' => $this->readCellByIndex($row, $indices['total_tool_radius_mm']),
                'tl_spacer_tool_total_mm' => $this->readCellByIndex($row, $indices['tl_spacer_tool_total_mm']),
                'gap_mm' => $this->readCellByIndex($row, $indices['gap_mm']),
                'can_length_mm' => $this->readCellByIndex($row, $indices['can_length_mm']),
                'effective_from' => $this->readCellByIndex($row, $indices['effective_from']),
                'effective_to' => $this->readCellByIndex($row, $indices['effective_to']),
                'is_active' => $this->readCellByIndex($row, $indices['is_active']),
                'notes' => $this->readCellByIndex($row, $indices['notes']),
            ];
        }

        return $rows;
    }

    private function resolveColumnMap(array $headers): array
    {
        $aliases = [
            'standard_version_id' => ['standardversionid'],
            'shape_code' => ['shapecode'],
            'size_code' => ['sizecode'],
            'nb' => ['nb'],
            'width_mm' => ['widthmm'],
            'length_mm' => ['lengthmm'],
            'tl_per_side_mm' => ['tlpersidemm'],
            'tl_qty' => ['tlqty'],
            'spacer_width_mm' => ['spacerwidthmm'],
            'spacer_qty' => ['spacerqty'],
            'tool_radius_mm' => ['toolradiusmm'],
            'tool_radius_qty' => ['toolradiusqty'],
            'pitch_ejma_mm' => ['pitchejmamm'],
            'pitch_gte_mm' => ['pitchgtemm'],
            'total_tl_mm' => ['totaltlmm'],
            'total_spacer_mm' => ['totalspacermm'],
            'total_tool_radius_mm' => ['totaltoolradiusmm'],
            'tl_spacer_tool_total_mm' => ['tlspacertooltotalmm'],
            'gap_mm' => ['gapmm'],
            'can_length_mm' => ['canlengthmm'],
            'effective_from' => ['effectivefrom'],
            'effective_to' => ['effectiveto'],
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
}
