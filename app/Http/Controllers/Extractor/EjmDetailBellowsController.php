<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EjmDetailBellowsController extends Controller
{
    public function index(Request $request): View
    {
        $pceNumber = trim((string) $request->query('pce_number', ''));
        $headerId = (int) $request->query('header_id', 0);

        $itemRelations = [
            'shape:id,shape_name',
            'typeConfig:id,type_name',
            'validation:id,inch,nb,id_mm,od_mm,thk,ly,noc,p,oal,oal_b,can_length,spare',
            'materialBellow:id,material,part_number,price_sqm,price_kg,price_gram',
            'materialPipeEnd:id,material,part_number,description,naming',
            'detailBellows:id,pce_item_id,width_inner,width_outer,length_inner,length_outer,square_inner_sqm,square_outer_sqm,time_cutting_inner,time_cutting_outer,time_roll_inner,time_roll_outer,time_welding_inner,time_welding_outer,time_hydroforming_inner,time_hydroforming_outer,total_time_minute,part_number_plate,description_plate,part_number_tube,description_tube,part_number_bellows,description_bellows,raw_material,raw_material_code,raw_material_price_sqm,cost_raw_material,machine_rate_per_minute,machine_cost,total_cost_raw,partner_hour_rate,manpower_qty,total_cost_manpower,total_price',
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
        $processMap = $this->buildBellowsProcessMap($items);
        $rateResult = $this->loadCostRates();
        $rates = $rateResult['rates'];

        $recentHeaders = PceHeader::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'pce_number', 'project_name', 'drawing_no']);

        return view('extractor.detail-bellows', [
            'pceNumber' => $pceNumber,
            'selectedHeader' => $selectedHeader,
            'items' => $items,
            'recentHeaders' => $recentHeaders,
            'processMap' => $processMap,
            'rates' => $rates,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'header_id' => ['required', 'integer', 'exists:pce_headers,id'],
        ]);

        $header = PceHeader::with([
            'items' => fn ($query) => $query->with([
                'validation:id,nb,id_mm,od_mm,p,can_length,oal_b,oal,spare',
                'materialBellow:id,material,part_number,price_sqm',
                'materialPipeEnd:id,material,part_number,description,naming',
            ])->orderBy('id'),
        ])->findOrFail((int) $validated['header_id']);

        if ($header->items->isEmpty()) {
            return redirect()
                ->route('extractor.ejm.detailbellows', ['header_id' => $header->id])
                ->with('error', 'Tidak ada PCE Item pada header ini.');
        }

        $processMap = $this->buildBellowsProcessMap($header->items);
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
            if (! $item->materialBellow) {
                $issues[] = "Item {$item->id}: material bellow belum diisi.";
                continue;
            }

            $proc = $processMap[(int) ($item->nb ?? 0)] ?? collect();
            $cut = $proc->get('cutting_shearing');
            $roll = $proc->get('rolling');
            $seam = $proc->get('seam_welding');
            $hydro = $proc->get('hydro_forming');

            if (! $cut || ! $roll || ! $seam || ! $hydro) {
                $issues[] = "Item {$item->id} NB {$item->nb}: proses bellows belum lengkap (cutting/rolling/seam/hydro).";
                continue;
            }

            $idMm = (float) ($item->id_mm ?? $item->validation?->id_mm ?? 0);
            $odMm = (float) ($item->od_mm ?? $item->validation?->od_mm ?? 0);
            $widthInner = $idMm > 0 ? (M_PI * $idMm) : 0;
            $widthOuter = $odMm > 0 ? (M_PI * $odMm) : 0;

            $length = (float) ($item->validation?->can_length ?? $item->validation?->oal_b ?? $item->validation?->oal ?? 0);
            $lengthInner = $length;
            $lengthOuter = $length;

            $squareInner = ($widthInner > 0 && $lengthInner > 0) ? (($widthInner * $lengthInner) / 1000000) : 0;
            $squareOuter = ($widthOuter > 0 && $lengthOuter > 0) ? (($widthOuter * $lengthOuter) / 1000000) : 0;

            $timeCutI = (float) ($cut?->tube_inner ?? 0);
            $timeCutO = (float) ($cut?->tube_outer ?? 0);
            $timeRollI = (float) ($roll?->tube_inner ?? 0);
            $timeRollO = (float) ($roll?->tube_outer ?? 0);
            $timeWeldI = (float) ($seam?->tube_inner ?? 0);
            $timeWeldO = (float) ($seam?->tube_outer ?? 0);
            $timeHydroI = (float) ($hydro?->tube_inner ?? 0);
            $timeHydroO = (float) ($hydro?->tube_outer ?? 0);

            $timeInnerBase = $timeCutI + $timeRollI + $timeWeldI;
            $timeOuterBase = $timeCutO + $timeRollO + $timeWeldO;
            $totalTime = $timeInnerBase + $timeOuterBase + max($timeHydroI, $timeHydroO);

            $rawMaterial = (string) ($item->materialBellow?->material ?? '');
            $rawMaterialCode = (string) ($item->materialBellow?->part_number ?? '');
            $thkText = $item->thk_mm !== null ? rtrim(rtrim(number_format((float) $item->thk_mm, 3, '.', ''), '0'), '.') : '-';
            $partNumberPlate = 'BAR-FLAT-' . preg_replace('/[^A-Z0-9]/', '', strtoupper((string) ($item->materialBellow?->material ?? 'MAT'))) . '-00.' . str_pad((string) $item->id, 3, '0', STR_PAD_LEFT);
            $descriptionPlate = 'BAR-FLAT-' . (string) ($item->materialBellow?->material ?? '-') . ', Size : Thk ' . $thkText;
            $partNumberTube = (string) ($item->materialPipeEnd?->part_number ?? '');
            $descriptionTube = (string) ($item->materialPipeEnd?->description ?? $item->materialPipeEnd?->naming ?? '');
            $partNumberBellows = (string) ($item->materialBellow?->part_number ?? '');
            $descriptionBellows = (string) ($item->description ?? $item->typeConfig?->type_name ?? '');
            $rawMatPriceSqm = (float) ($item->materialBellow?->price_sqm ?? 0);
            $ply = max(1, (int) ($item->ply ?? 1));
            $sparePct = (float) ($item->validation?->spare ?? 5);
            $costRawMaterial = (($squareInner + $squareOuter) * $ply) * $rawMatPriceSqm * (1 + ($sparePct / 100));

            if ($rates['machine_minute'] === null || $rates['manpower_hour'] === null) {
                $issues[] = "Item {$item->id} NB {$item->nb}: rate machine/manpower kosong di cost_products.";
                continue;
            }

            $machineRate = (float) $rates['machine_minute'];
            $machineCost = $totalTime * $machineRate;
            $totalCostRaw = $costRawMaterial + $machineCost;

            $partnerRate = (float) $rates['manpower_hour'];
            $manpowerQty = 2.0;
            $totalCostManpower = ($totalTime / 60) * $partnerRate * $manpowerQty;
            $totalPrice = $totalCostRaw + $totalCostManpower;

            $rows[] = [
                'pce_item_id' => $item->id,
                'width_inner' => $widthInner,
                'width_outer' => $widthOuter,
                'length_inner' => $lengthInner,
                'length_outer' => $lengthOuter,
                'square_inner_sqm' => $squareInner,
                'square_outer_sqm' => $squareOuter,
                'time_cutting_inner' => $timeCutI,
                'time_cutting_outer' => $timeCutO,
                'time_roll_inner' => $timeRollI,
                'time_roll_outer' => $timeRollO,
                'time_welding_inner' => $timeWeldI,
                'time_welding_outer' => $timeWeldO,
                'time_hydroforming_inner' => $timeHydroI,
                'time_hydroforming_outer' => $timeHydroO,
                'total_time_minute' => $totalTime,
                'part_number_plate' => $partNumberPlate,
                'description_plate' => $descriptionPlate,
                'part_number_tube' => $partNumberTube,
                'description_tube' => $descriptionTube,
                'part_number_bellows' => $partNumberBellows,
                'description_bellows' => $descriptionBellows,
                'raw_material' => $rawMaterial,
                'raw_material_code' => $rawMaterialCode,
                'raw_material_price_sqm' => $rawMatPriceSqm,
                'cost_raw_material' => $costRawMaterial,
                'machine_rate_per_minute' => $machineRate,
                'machine_cost' => $machineCost,
                'total_cost_raw' => $totalCostRaw,
                'partner_hour_rate' => $partnerRate,
                'manpower_qty' => $manpowerQty,
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
                ->route('extractor.ejm.detailbellows', ['header_id' => $header->id])
                ->with('error', 'Generate dibatalkan. ' . implode(' | ', $preview) . $more);
        }

        DB::table('ejm_detail_bellows')->upsert(
            $rows,
            ['pce_item_id'],
            [
                'width_inner',
                'width_outer',
                'length_inner',
                'length_outer',
                'square_inner_sqm',
                'square_outer_sqm',
                'time_cutting_inner',
                'time_cutting_outer',
                'time_roll_inner',
                'time_roll_outer',
                'time_welding_inner',
                'time_welding_outer',
                'time_hydroforming_inner',
                'time_hydroforming_outer',
                'total_time_minute',
                'part_number_plate',
                'description_plate',
                'part_number_tube',
                'description_tube',
                'part_number_bellows',
                'description_bellows',
                'raw_material',
                'raw_material_code',
                'raw_material_price_sqm',
                'cost_raw_material',
                'machine_rate_per_minute',
                'machine_cost',
                'total_cost_raw',
                'partner_hour_rate',
                'manpower_qty',
                'total_cost_manpower',
                'total_price',
                'updated_at',
            ]
        );

        return redirect()
            ->route('extractor.ejm.detailbellows', ['header_id' => $header->id])
            ->with('success', 'Generate Detail Bellows selesai. Upsert: ' . count($rows) . ' item.');
    }

    private function buildBellowsProcessMap($items)
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
            ->whereRaw('UPPER(d.component_type) = ?', ['BELLOWS'])
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

    private function loadCostRates(): array
    {
        $codes = [
            'EJM_CUTTING',
            'EJM_ROLLING',
            'EJM_SEAM_WELDING',
            'EJM_HYDRO_FORMING',
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
                'cutting' => $map['EJM_CUTTING'] ?? null,
                'rolling' => $map['EJM_ROLLING'] ?? null,
                'seam_welding' => $map['EJM_SEAM_WELDING'] ?? null,
                'hydro_forming' => $map['EJM_HYDRO_FORMING'] ?? null,
                'machine_minute' => $map['EJM_MACHINE_MINUTE'] ?? null,
                'manpower_hour' => $map['EJM_MANPOWER_HOUR'] ?? null,
            ],
            'missing' => $missing,
        ];
    }
}
