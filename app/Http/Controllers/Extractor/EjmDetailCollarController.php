<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EjmDetailCollarController extends Controller
{
    public function index(Request $request): View
    {
        $pceNumber = trim((string) $request->query('pce_number', ''));
        $headerId = (int) $request->query('header_id', 0);

        $itemRelations = [
            'shape:id,shape_name',
            'typeConfig:id,type_name',
            'validation:id,inch,nb,id_mm,od_mm,thk,ly,noc,p,oal,oal_b,can_length,spare',
            'materialBellow:id,material,part_number,price_sqm,thk_mm',
            'detailCollar:id,pce_item_id,qty_kanan_kiri,width,length,square_sqm,time_cutting_minute,time_roll_minute,time_welding_minute,total_time_minute,part_number_plate,description_plate,part_number_collar,description_collar,raw_material,raw_material_code,price_sqm,cost_raw_material,price_validasi_machine,cost_machine_material,rate_per_hour,quantity,total_cost_manpower,total_price',
        ];

        $selectedHeader = null;
        if ($headerId > 0) {
            $selectedHeader = PceHeader::with([
                'items' => fn ($query) => $query->with($itemRelations)->orderBy('id'),
            ])->find($headerId);
        } elseif ($pceNumber !== '') {
            $selectedHeader = PceHeader::where('pce_number', $pceNumber)
                ->with([
                    'items' => fn ($query) => $query->with($itemRelations)->orderBy('id'),
                ])
                ->first();
        }

        $items = $selectedHeader?->items ?? collect();
        $processMap = $this->buildCollarProcessMap($items);
        $rateResult = $this->loadCostRates();

        $recentHeaders = PceHeader::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'pce_number', 'project_name', 'drawing_no']);

        return view('extractor.detail-collar', [
            'pceNumber' => $pceNumber,
            'selectedHeader' => $selectedHeader,
            'items' => $items,
            'recentHeaders' => $recentHeaders,
            'processMap' => $processMap,
            'rates' => $rateResult['rates'],
            'missingRates' => $rateResult['missing'],
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'header_id' => ['required', 'integer', 'exists:pce_headers,id'],
        ]);

        $header = PceHeader::with([
            'items' => fn ($query) => $query->with([
                'validation:id,nb,id_mm,od_mm,oal_b,oal,can_length,spare',
                'materialBellow:id,material,part_number,price_sqm,thk_mm',
            ])->orderBy('id'),
        ])->findOrFail((int) $validated['header_id']);

        if ($header->items->isEmpty()) {
            return redirect()
                ->route('extractor.ejm.detailcollar', ['header_id' => $header->id])
                ->with('error', 'Tidak ada PCE Item pada header ini.');
        }

        $processMap = $this->buildCollarProcessMap($header->items);
        $rateResult = $this->loadCostRates();
        $rates = $rateResult['rates'];
        $issues = [];
        if (! empty($rateResult['missing'])) {
            $issues[] = 'Cost Product kosong: ' . implode(', ', $rateResult['missing']);
        }

        $now = now();
        $rows = [];

        foreach ($header->items as $item) {
            if (! $item->validation) {
                $issues[] = "Item {$item->id}: validasi expansion joint belum terhubung.";
                continue;
            }

            $collarMaterial = $this->resolveCollarMaterial($item);
            if (! $collarMaterial) {
                $issues[] = "Item {$item->id}: material collar tidak ditemukan di ejm_special_materials.";
                continue;
            }

            $proc = $processMap[(int) ($item->nb ?? 0)] ?? collect();
            $cut = $proc->get('cutting_shearing');
            $roll = $proc->get('rolling');
            $weld = $proc->get('welding');

            if (! $cut || ! $roll || ! $weld) {
                $issues[] = "Item {$item->id} NB {$item->nb}: proses collar belum lengkap (cutting/rolling/welding).";
                continue;
            }

            $timeCut = (float) ($cut?->tube_inner ?? 0);
            $timeRoll = (float) ($roll?->tube_inner ?? 0);
            $timeWeld = (float) ($weld?->tube_inner ?? 0);
            $totalTime = $timeCut + $timeRoll + $timeWeld;

            $odMm = (float) ($item->od_mm ?? $item->validation?->od_mm ?? 0);
            $width = $odMm > 0 ? (M_PI * $odMm) : 0;
            $length = (float) ($item->validation?->can_length ?? $item->validation?->oal_b ?? $item->validation?->oal ?? 0);
            $square = ($width > 0 && $length > 0) ? (($width * $length) / 1000000) : 0;

            $qtyKananKiri = 2.0;
            $priceSqm = (float) ($collarMaterial->price_sqm ?? 0);
            $spare = (float) ($item->validation?->spare ?? 5);
            $costRawMaterial = ($square * $qtyKananKiri * $priceSqm) * (1 + ($spare / 100));
            $partNumberPlate = 'BAR-FLAT-' . preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $collarMaterial->material)) . '-00.' . str_pad((string) $item->id, 3, '0', STR_PAD_LEFT);
            $thkText = $item->thk_mm !== null ? rtrim(rtrim(number_format((float) $item->thk_mm, 3, '.', ''), '0'), '.') : '-';
            $descriptionPlate = 'BAR-FLAT-' . (string) $collarMaterial->material . ', Size : Thk ' . $thkText;
            $partNumberCollar = (string) $collarMaterial->part_number;
            $descriptionCollar = (string) ($collarMaterial->description ?? $collarMaterial->naming ?? ('Collar ' . $collarMaterial->material));

            if ($rates['machine_minute'] === null || $rates['manpower_hour'] === null) {
                $issues[] = "Item {$item->id}: rate machine/manpower kosong di cost_products.";
                continue;
            }

            $priceValidasiMachine = (float) $rates['machine_minute'];
            $costMachineMaterial = $totalTime * $priceValidasiMachine;
            $ratePerHour = (float) $rates['manpower_hour'];
            $qtyManpower = 2.0;
            $totalCostManpower = ($totalTime / 60) * $ratePerHour * $qtyManpower;
            $totalPrice = $costRawMaterial + $costMachineMaterial + $totalCostManpower;

            $rows[] = [
                'pce_item_id' => $item->id,
                'qty_kanan_kiri' => $qtyKananKiri,
                'width' => $width,
                'length' => $length,
                'square_sqm' => $square,
                'time_cutting_minute' => $timeCut,
                'time_roll_minute' => $timeRoll,
                'time_welding_minute' => $timeWeld,
                'total_time_minute' => $totalTime,
                'part_number_plate' => $partNumberPlate,
                'description_plate' => $descriptionPlate,
                'part_number_collar' => $partNumberCollar,
                'description_collar' => $descriptionCollar,
                'raw_material' => (string) $collarMaterial->material,
                'raw_material_code' => (string) $collarMaterial->part_number,
                'price_sqm' => $priceSqm,
                'cost_raw_material' => $costRawMaterial,
                'price_validasi_machine' => $priceValidasiMachine,
                'cost_machine_material' => $costMachineMaterial,
                'rate_per_hour' => $ratePerHour,
                'quantity' => $qtyManpower,
                'total_cost_manpower' => $totalCostManpower,
                'total_price' => $totalPrice,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($issues)) {
            $preview = array_slice(array_values(array_unique($issues)), 0, 12);
            $more = count($issues) > 12 ? ' (dan error lainnya...)' : '';
            return redirect()
                ->route('extractor.ejm.detailcollar', ['header_id' => $header->id])
                ->with('error', 'Generate dibatalkan. ' . implode(' | ', $preview) . $more);
        }

        DB::table('ejm_detail_collars')->upsert(
            $rows,
            ['pce_item_id'],
            [
                'qty_kanan_kiri',
                'width',
                'length',
                'square_sqm',
                'time_cutting_minute',
                'time_roll_minute',
                'time_welding_minute',
                'total_time_minute',
                'part_number_plate',
                'description_plate',
                'part_number_collar',
                'description_collar',
                'raw_material',
                'raw_material_code',
                'price_sqm',
                'cost_raw_material',
                'price_validasi_machine',
                'cost_machine_material',
                'rate_per_hour',
                'quantity',
                'total_cost_manpower',
                'total_price',
                'updated_at',
            ]
        );

        return redirect()
            ->route('extractor.ejm.detailcollar', ['header_id' => $header->id])
            ->with('success', 'Generate Detail Collar selesai. Upsert: ' . count($rows) . ' item.');
    }

    private function buildCollarProcessMap($items)
    {
        $nbList = $items->pluck('nb')
            ->filter(fn ($nb) => is_numeric($nb))
            ->map(fn ($nb) => (int) $nb)
            ->unique()
            ->values();

        $innerRateExpr = 'NULL';
        $outerRateExpr = 'NULL';
        if (Schema::hasColumn('ejm_process_definitions', 'rate_inner_per_hour') && Schema::hasColumn('ejm_process_definitions', 'rate_outer_per_hour')) {
            $innerRateExpr = 'd.rate_inner_per_hour';
            $outerRateExpr = 'd.rate_outer_per_hour';
        } elseif (Schema::hasColumn('ejm_process_definitions', 'rate_per_hour')) {
            $innerRateExpr = 'd.rate_per_hour';
            $outerRateExpr = 'd.rate_per_hour';
        }

        return DB::table('ejm_process_times as t')
            ->join('ejm_process_definitions as d', 'd.id', '=', 't.process_definition_id')
            ->whereRaw('UPPER(d.component_type) = ?', ['COLLAR'])
            ->when($nbList->isNotEmpty(), fn ($query) => $query->whereIn('t.nb', $nbList))
            ->orderBy('t.nb')
            ->orderBy('d.sequence')
            ->orderBy('t.id')
            ->get([
                't.nb',
                'd.process_name',
                DB::raw('t.minutes_inner as tube_inner'),
                DB::raw('t.minutes_outer as tube_outer'),
                DB::raw($innerRateExpr . ' as price_tube_inner'),
                DB::raw($outerRateExpr . ' as price_tube_outer'),
            ])
            ->groupBy('nb')
            ->map(function ($rows) {
                return $rows->mapWithKeys(function ($row) {
                    $key = strtolower((string) $row->process_name);
                    $key = str_replace([' ', '-'], '_', $key);
                    return [$key => $row];
                });
            });
    }

    private function resolveCollarMaterial(object $item): ?object
    {
        $baseMaterial = (string) ($item->materialBellow?->material ?? '');
        if ($baseMaterial === '') {
            return null;
        }

        $thk = (float) ($item->thk_mm ?? 0);
        $query = DB::table('ejm_special_materials')
            ->whereRaw('UPPER(component) = ?', ['COLLAR'])
            ->where('material', $baseMaterial);

        if ($thk > 0) {
            return $query
                ->orderByRaw('ABS(COALESCE(thk_mm, 0) - ?)', [$thk])
                ->orderBy('id')
                ->first(['id', 'material', 'part_number', 'price_sqm', 'thk_mm', 'description', 'naming']);
        }

        return $query->orderBy('id')->first(['id', 'material', 'part_number', 'price_sqm', 'thk_mm', 'description', 'naming']);
    }

    private function loadCostRates(): array
    {
        $codes = [
            'EJM_MACHINE_MINUTE',
            'EJM_MANPOWER_HOUR',
        ];

        $rows = DB::table('cost_products')
            ->whereIn('dlaborno', $codes)
            ->get(['dlaborno', 'cost']);

        $map = [];
        foreach ($rows as $row) {
            $map[(string) $row->dlaborno] = $row->cost !== null ? (float) $row->cost : null;
        }

        $missing = [];
        foreach ($codes as $code) {
            if (! array_key_exists($code, $map) || $map[$code] === null) {
                $missing[] = $code;
            }
        }

        return [
            'rates' => [
                'machine_minute' => $map['EJM_MACHINE_MINUTE'] ?? null,
                'manpower_hour' => $map['EJM_MANPOWER_HOUR'] ?? null,
            ],
            'missing' => $missing,
        ];
    }
}
