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
            ->orderByRaw('COALESCE(inch, 0) asc')
            ->orderByRaw('COALESCE(nb, 0) asc')
            ->paginate(100)
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
            'fields' => $this->fields(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('setting.ejm-expansion-joint.index', ['create' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedPayload($request);

        DB::table(self::TABLE)->insert(array_merge($payload, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

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
                ->with('error', 'Import XLSX membutuhkan ekstensi PHP zip (ZipArchive).');
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
                $inch = $this->toInt($row['inch'] ?? null);
                $nb = $this->toInt($row['nb'] ?? null);

                if ($inch === null && $nb === null) {
                    $skipped++;
                    continue;
                }

                $shapeCode = strtoupper(trim((string) ($row['shape_code'] ?? 'RND')));
                $sizeCode = $this->toNullString($row['size_code'] ?? null) ?? ('RND_NB' . ($nb ?? '0'));

                $payload = $this->mapPayload($row);
                $payload['shape_code'] = $shapeCode;
                $payload['size_code'] = $sizeCode;
                $payload['updated_at'] = now();

                $existing = DB::table(self::TABLE)
                    ->where('shape_code', $shapeCode)
                    ->where('size_code', $sizeCode)
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

        return redirect()->route('setting.ejm-expansion-joint.index')
            ->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    public function templateCsv()
    {
        $headers = $this->fields();
        $headers[] = 'is_active';

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
            'Content-Disposition' => 'attachment; filename=ejm_expansion_joint_template.csv',
        ]);
    }

    public function templateExcel()
    {
        $rows = $this->sampleRows();
        $html = view('settings.ejm-expansion-joint-template-xls', compact('rows'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=ejm_expansion_joint_template.xls',
        ]);
    }

    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'standard_version_id' => ['nullable', 'integer', 'min:1'],
            'shape_code' => ['required', 'string', 'max:30'],
            'size_code' => ['nullable', 'string', 'max:80'],
            'inch' => ['nullable', 'integer', 'min:0'],
            'nb' => ['nullable', 'integer', 'min:0'],
            'width' => ['nullable', 'numeric'],
            'length' => ['nullable', 'numeric'],
            'id_mm' => ['nullable', 'numeric'],
            'od_mm' => ['nullable', 'numeric'],
            'thk' => ['nullable', 'numeric'],
            'ly' => ['nullable', 'numeric'],
            'noc' => ['nullable', 'integer', 'min:0'],
            'lc' => ['nullable', 'numeric'],
            'tc' => ['nullable', 'numeric'],
            'p' => ['nullable', 'numeric'],
            'tr' => ['nullable', 'numeric'],
            'r' => ['nullable', 'numeric'],
            'oal_b' => ['nullable', 'numeric'],
            'bl' => ['nullable', 'numeric'],
            'tl' => ['nullable', 'numeric'],
            'slc' => ['nullable', 'numeric'],
            'lpe' => ['nullable', 'numeric'],
            'pres' => ['nullable', 'numeric'],
            'temp_c' => ['nullable', 'string', 'max:20'],
            'axial_m' => ['nullable', 'string', 'max:20'],
            'lsr_n_per' => ['nullable', 'numeric'],
            'mp_ci_mpa' => ['nullable', 'numeric'],
            'mp_ii_mpa' => ['nullable', 'numeric'],
            'mlc' => ['nullable', 'numeric'],
            'gpf' => ['nullable', 'numeric'],
            'oal' => ['nullable', 'numeric'],
            'al' => ['nullable', 'numeric'],
            'width1' => ['nullable', 'numeric'],
            'width2' => ['nullable', 'numeric'],
            'spare' => ['nullable', 'numeric'],
            'can_length' => ['nullable', 'numeric'],
            'circumference_collar' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];

        $data = $request->validate($rules);

        return $this->mapPayload($data);
    }

    private function mapPayload(array $data): array
    {
        return [
            'standard_version_id' => $this->toInt($data['standard_version_id'] ?? null),
            'shape_code' => strtoupper(trim((string) ($data['shape_code'] ?? 'RND'))),
            'size_code' => $this->toNullString($data['size_code'] ?? null),
            'inch' => $this->toInt($data['inch'] ?? null),
            'nb' => $this->toInt($data['nb'] ?? null),
            'width' => $this->toDecimal($data['width'] ?? null),
            'length' => $this->toDecimal($data['length'] ?? null),
            'id_mm' => $this->toDecimal($data['id_mm'] ?? null),
            'od_mm' => $this->toDecimal($data['od_mm'] ?? null),
            'thk' => $this->toDecimal($data['thk'] ?? null),
            'ly' => $this->toDecimal($data['ly'] ?? null),
            'noc' => $this->toInt($data['noc'] ?? null),
            'lc' => $this->toDecimal($data['lc'] ?? null),
            'tc' => $this->toDecimal($data['tc'] ?? null),
            'p' => $this->toDecimal($data['p'] ?? null),
            'tr' => $this->toDecimal($data['tr'] ?? null),
            'r' => $this->toDecimal($data['r'] ?? null),
            'oal_b' => $this->toDecimal($data['oal_b'] ?? null),
            'bl' => $this->toDecimal($data['bl'] ?? null),
            'tl' => $this->toDecimal($data['tl'] ?? null),
            'slc' => $this->toDecimal($data['slc'] ?? null),
            'lpe' => $this->toDecimal($data['lpe'] ?? null),
            'pres' => $this->toDecimal($data['pres'] ?? null),
            'temp_c' => $this->toNullString($data['temp_c'] ?? null),
            'axial_m' => $this->toNullString($data['axial_m'] ?? null),
            'lsr_n_per' => $this->toDecimal($data['lsr_n_per'] ?? null, 3),
            'mp_ci_mpa' => $this->toDecimal($data['mp_ci_mpa'] ?? null, 3),
            'mp_ii_mpa' => $this->toDecimal($data['mp_ii_mpa'] ?? null, 3),
            'mlc' => $this->toDecimal($data['mlc'] ?? null, 3),
            'gpf' => $this->toDecimal($data['gpf'] ?? null),
            'oal' => $this->toDecimal($data['oal'] ?? null),
            'al' => $this->toDecimal($data['al'] ?? null),
            'width1' => $this->toDecimal($data['width1'] ?? null),
            'width2' => $this->toDecimal($data['width2'] ?? null),
            'spare' => $this->toDecimal($data['spare'] ?? null),
            'can_length' => $this->toDecimal($data['can_length'] ?? null),
            'circumference_collar' => $this->toDecimal($data['circumference_collar'] ?? null),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'notes' => $this->toNullString($data['notes'] ?? null),
        ];
    }

    private function fields(): array
    {
        return [
            'standard_version_id', 'shape_code', 'size_code', 'inch', 'nb', 'width', 'length',
            'id_mm', 'od_mm', 'thk', 'ly', 'noc', 'lc', 'tc', 'p', 'tr', 'r',
            'oal_b', 'bl', 'tl', 'slc', 'lpe', 'pres', 'temp_c', 'axial_m',
            'lsr_n_per', 'mp_ci_mpa', 'mp_ii_mpa', 'mlc', 'gpf', 'oal', 'al',
            'width1', 'width2', 'spare', 'can_length', 'circumference_collar', 'notes',
        ];
    }

    private function sampleRows(): array
    {
        return [
            [
                'standard_version_id' => 1, 'shape_code' => 'RND', 'size_code' => 'RND_NB100',
                'inch' => 4, 'nb' => 100, 'width' => '', 'length' => '', 'id_mm' => 114, 'od_mm' => 141, 'thk' => 0.5,
                'ly' => 2, 'noc' => 14, 'lc' => 25, 'tc' => 1.5, 'p' => 12.2, 'tr' => 2.54, 'r' => 3.54,
                'oal_b' => 220, 'bl' => 170, 'tl' => 40, 'slc' => 6.42, 'lpe' => 69, 'pres' => 1, 'temp_c' => 450,
                'axial_m' => '±20', 'lsr_n_per' => 152, 'mp_ci_mpa' => 1.13, 'mp_ii_mpa' => 1.96, 'mlc' => 2.577,
                'gpf' => 8, 'oal' => 324, 'al' => 5.6, 'width1' => 356.3, 'width2' => 361, 'spare' => 5,
                'can_length' => 556.8, 'circumference_collar' => 366.3, 'notes' => '', 'is_active' => 1,
            ],
            [
                'standard_version_id' => 1, 'shape_code' => 'RND', 'size_code' => 'RND_NB125',
                'inch' => 5, 'nb' => 125, 'width' => '', 'length' => '', 'id_mm' => 141, 'od_mm' => 168, 'thk' => 0.5,
                'ly' => 2, 'noc' => 14, 'lc' => 25, 'tc' => 1.5, 'p' => 12.9, 'tr' => 2.72, 'r' => 3.72,
                'oal_b' => 230, 'bl' => 180, 'tl' => 40, 'slc' => 6.06, 'lpe' => 72, 'pres' => 1, 'temp_c' => 450,
                'axial_m' => '±20', 'lsr_n_per' => 240, 'mp_ci_mpa' => 1.29, 'mp_ii_mpa' => 1.93, 'mlc' => 2.578,
                'gpf' => 8, 'oal' => 340, 'al' => 5.8, 'width1' => 441.11, 'width2' => 446, 'spare' => 5,
                'can_length' => 561.8, 'circumference_collar' => 451.1, 'notes' => '', 'is_active' => 1,
            ],
        ];
    }

    private function resolveColumnMap(array $headers): array
    {
        $aliases = [
            'standard_version_id' => ['standardversionid'],
            'shape_code' => ['shapecode'],
            'size_code' => ['sizecode'],
            'inch' => ['inch'],
            'nb' => ['nb'],
            'width' => ['width'],
            'length' => ['length'],
            'id_mm' => ['id', 'idmm'],
            'od_mm' => ['od', 'odmm'],
            'thk' => ['thk'],
            'ly' => ['ly'],
            'noc' => ['noc'],
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
            'pres' => ['pres'],
            'temp_c' => ['tempc'],
            'axial_m' => ['axialm'],
            'lsr_n_per' => ['lsrnper'],
            'mp_ci_mpa' => ['mpcimpa'],
            'mp_ii_mpa' => ['mpiimpa'],
            'mlc' => ['mlc'],
            'gpf' => ['gpf'],
            'oal' => ['oal'],
            'al' => ['al'],
            'width1' => ['width1'],
            'width2' => ['width2'],
            'spare' => ['spare'],
            'can_length' => ['canlength'],
            'circumference_collar' => ['circumferencecollar'],
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
        if ($indices['inch'] === null && $indices['nb'] === null) {
            return [];
        }

        $rows = [];
        foreach (array_slice($rawRows, $headerRowIndex + 1) as $row) {
            $item = [];
            foreach ($indices as $field => $index) {
                $item[$field] = $this->readCellByIndex($row, $index);
            }
            $rows[] = $item;
        }

        return $rows;
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

    private function toNullString(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' ? null : $v;
    }
}
