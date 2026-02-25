<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('settings.index');
    }

    public function gppValidation(Request $request): View
    {
        $tables = $this->getGppTables();

        $selectedTable = (string) $request->query('table', '');
        if (! in_array($selectedTable, $tables, true)) {
            $selectedTable = $tables[0] ?? '';
        }

        $columns = [];
        $rows = [];
        $totalRows = 0;
        $columnMeta = [];
        $primaryKey = null;
        $autoIncrementColumn = null;
        $editingRow = null;

        if ($selectedTable !== '') {
            $columnMeta = collect(DB::select("SHOW COLUMNS FROM `{$selectedTable}`"))
                ->map(function ($row) {
                    $arr = (array) $row;
                    return [
                        'name' => (string) ($arr['Field'] ?? ''),
                        'type' => (string) ($arr['Type'] ?? 'varchar(255)'),
                        'nullable' => (($arr['Null'] ?? 'NO') === 'YES'),
                        'key' => (string) ($arr['Key'] ?? ''),
                        'default' => $arr['Default'] ?? null,
                        'extra' => (string) ($arr['Extra'] ?? ''),
                    ];
                })
                ->values()
                ->all();

            $columns = array_column($columnMeta, 'name');

            $primaryKey = collect($columnMeta)->firstWhere('key', 'PRI')['name'] ?? null;
            $autoIncrementColumn = collect($columnMeta)
                ->first(fn ($col) => str_contains(strtolower((string) $col['extra']), 'auto_increment'))['name'] ?? null;

            $totalRows = (int) DB::table($selectedTable)->count();

            $rowsQuery = DB::table($selectedTable);
            if ($primaryKey && in_array($primaryKey, $columns, true)) {
                $rowsQuery->orderBy($primaryKey, 'desc');
            }

            $rows = $rowsQuery
                ->limit(200)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->all();

            $editPk = (string) $request->query('edit', '');
            if ($editPk !== '' && $primaryKey) {
                $editingRow = DB::table($selectedTable)
                    ->where($primaryKey, $editPk)
                    ->first();
                $editingRow = $editingRow ? (array) $editingRow : null;
            }
        }

        return view('settings.gpp-validation', [
            'tables' => $tables,
            'selectedTable' => $selectedTable,
            'columns' => $columns,
            'columnMeta' => $columnMeta,
            'rows' => $rows,
            'totalRows' => $totalRows,
            'primaryKey' => $primaryKey,
            'autoIncrementColumn' => $autoIncrementColumn,
            'editingRow' => $editingRow,
        ]);
    }

    public function gppValidationStore(Request $request)
    {
        $table = (string) $request->input('table');
        abort_unless($this->isAllowedGppTable($table), 404);

        $columnMeta = collect(DB::select("SHOW COLUMNS FROM `{$table}`"))
            ->map(fn ($row) => (array) $row)
            ->values();

        $insert = [];
        foreach ($columnMeta as $meta) {
            $name = (string) ($meta['Field'] ?? '');
            $extra = strtolower((string) ($meta['Extra'] ?? ''));
            if ($name === '' || str_contains($extra, 'auto_increment')) {
                continue;
            }

            $type = (string) ($meta['Type'] ?? 'varchar(255)');
            $nullable = (($meta['Null'] ?? 'NO') === 'YES');
            $value = $request->input("data.{$name}");
            $insert[$name] = $this->castByColumnType($value, $type, $nullable);
        }

        DB::table($table)->insert($insert);

        return redirect()
            ->route('setting.gpp-validation', ['table' => $table])
            ->with('success', "Data baru berhasil ditambahkan ke tabel {$table}.");
    }

    public function gppValidationUpdate(Request $request)
    {
        $table = (string) $request->input('table');
        abort_unless($this->isAllowedGppTable($table), 404);

        $columnMeta = collect(DB::select("SHOW COLUMNS FROM `{$table}`"))
            ->map(fn ($row) => (array) $row)
            ->values();

        $primaryKey = (string) (collect($columnMeta)->firstWhere('Key', 'PRI')['Field'] ?? '');
        abort_unless($primaryKey !== '', 422, 'Primary key tidak ditemukan pada tabel ini.');

        $pkValue = (string) $request->input('pk_value', '');
        abort_if($pkValue === '', 422, 'Primary key value tidak boleh kosong.');

        $update = [];
        foreach ($columnMeta as $meta) {
            $name = (string) ($meta['Field'] ?? '');
            $extra = strtolower((string) ($meta['Extra'] ?? ''));
            if ($name === '' || $name === $primaryKey || str_contains($extra, 'auto_increment')) {
                continue;
            }

            $type = (string) ($meta['Type'] ?? 'varchar(255)');
            $nullable = (($meta['Null'] ?? 'NO') === 'YES');
            $value = $request->input("data.{$name}");
            $update[$name] = $this->castByColumnType($value, $type, $nullable);
        }

        DB::table($table)->where($primaryKey, $pkValue)->update($update);

        return redirect()
            ->route('setting.gpp-validation', ['table' => $table])
            ->with('success', "Data pada tabel {$table} berhasil diupdate.");
    }

    private function getGppTables(): array
    {
        return collect(DB::select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0] ?? null)
            ->filter(fn ($name) => is_string($name) && str_ends_with(strtolower($name), '_gpp'))
            ->sort()
            ->values()
            ->all();
    }

    private function isAllowedGppTable(string $table): bool
    {
        return in_array($table, $this->getGppTables(), true);
    }

    private function castByColumnType(mixed $value, string $type, bool $nullable): mixed
    {
        if ($value === '' || $value === null) {
            return $nullable ? null : $value;
        }

        $type = strtolower($type);
        if (str_contains($type, 'tinyint(1)')) {
            return in_array((string) $value, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
        }

        if (preg_match('/int|decimal|double|float/', $type)) {
            if (is_numeric($value)) {
                return (str_contains($type, 'int')) ? (int) $value : (float) $value;
            }
        }

        return is_string($value) ? trim($value) : $value;
    }
}
