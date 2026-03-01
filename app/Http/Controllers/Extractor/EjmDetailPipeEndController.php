<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EjmDetailPipeEndController extends Controller
{
    public function index(Request $request): View
    {
        $pceNumber = trim((string) $request->query('pce_number', ''));
        $headerId = (int) $request->query('header_id', 0);

        $itemRelations = [
            'shape:id,shape_name',
            'validation:id,inch,nb,id_mm,od_mm,thk,oal,oal_b,can_length',
            'materialPipeEnd:id,material,part_number,description,naming,price_sqm',
            'detailPipeEnd:id,pce_item_id,length,time_cutting_minute,time_bevel_minute,time_grinding_minute,total_time_minute,raw_material,raw_material_code,price_sqm,cost_raw_material,price_validasi_machine,cost_machine,rate_per_hour,quantity,total_cost,total_price,part_number_pipe_end,description_pipe_end',
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
        $processMap = $this->buildPipeEndProcessMap($items);
        $rateResult = $this->loadCostRates();

        $recentHeaders = PceHeader::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'pce_number', 'project_name', 'drawing_no']);

        return view('extractor.detail-pipe-end', [
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
                'validation:id,nb,id_mm,od_mm,oal,oal_b,can_length',
                'materialPipeEnd:id,material,part_number,description,naming,price_sqm',
            ])->orderBy('id'),
        ])->findOrFail((int) $validated['header_id']);

        if ($header->items->isEmpty()) {
            return redirect()
                ->route('extractor.ejm.detailpipeend', ['header_id' => $header->id])
                ->with('error', 'Tidak ada PCE Item pada header ini.');
        }

        $processMap = $this->buildPipeEndProcessMap($header->items);
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
            if (! $item->materialPipeEnd) {
                $issues[] = "Item {$item->id}: material pipe end belum diisi.";
                continue;
            }

            $proc = $processMap[(int) ($item->nb ?? 0)] ?? collect();
            $cut = $proc->get('cutting');
            $bevel = $proc->get('bevel');
            $grind = $proc->get('grinding');
            if (! $cut || ! $bevel || ! $grind) {
                $issues[] = "Item {$item->id} NB {$item->nb}: proses pipe end belum lengkap (cutting/bevel/grinding).";
                continue;
            }

            $length = (float) ($item->validation?->can_length ?? $item->validation?->oal_b ?? $item->validation?->oal ?? 0);
            if ($length <= 0) {
                $issues[] = "Item {$item->id}: length pipe end tidak valid.";
                continue;
            }

            $timeCut = (float) ($cut?->tube_inner ?? 0);
            $timeBevel = (float) ($bevel?->tube_inner ?? 0);
            $timeGrinding = (float) ($grind?->tube_inner ?? 0);
            $totalTime = $timeCut + $timeBevel + $timeGrinding;

            $od = (float) ($item->od_mm ?? $item->validation?->od_mm ?? 0);
            $width = $od > 0 ? (M_PI * $od) : 0;
            if ($width <= 0) {
                $issues[] = "Item {$item->id}: OD kosong untuk hitung raw material.";
                continue;
            }
            $square = ($width * $length) / 1000000;
            $priceSqm = (float) ($item->materialPipeEnd->price_sqm ?? 0);
            $costRawMaterial = $square * $priceSqm;

            if ($rates['machine_minute'] === null || $rates['manpower_hour'] === null) {
                $issues[] = "Item {$item->id}: rate machine/manpower kosong di cost_products.";
                continue;
            }

            $priceValidasiMachine = (float) $rates['machine_minute'];
            $costMachine = $totalTime * $priceValidasiMachine;
            $ratePerHour = (float) $rates['manpower_hour'];
            $qtyManpower = 2.0;
            $totalCost = ($totalTime / 60) * $ratePerHour * $qtyManpower;

            $qtyItem = max(1, (int) ($item->qty ?? 1));
            $totalPrice = ($costRawMaterial + $costMachine + $totalCost) * $qtyItem;

            $rows[] = [
                'pce_item_id' => $item->id,
                'length' => $length,
                'time_cutting_minute' => $timeCut,
                'time_bevel_minute' => $timeBevel,
                'time_grinding_minute' => $timeGrinding,
                'total_time_minute' => $totalTime,
                'raw_material' => (string) $item->materialPipeEnd->material,
                'raw_material_code' => (string) $item->materialPipeEnd->part_number,
                'price_sqm' => $priceSqm,
                'cost_raw_material' => $costRawMaterial,
                'price_validasi_machine' => $priceValidasiMachine,
                'cost_machine' => $costMachine,
                'rate_per_hour' => $ratePerHour,
                'quantity' => $qtyManpower,
                'total_cost' => $totalCost,
                'total_price' => $totalPrice,
                'part_number_pipe_end' => (string) $item->materialPipeEnd->part_number,
                'description_pipe_end' => (string) ($item->materialPipeEnd->description ?? $item->materialPipeEnd->naming),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($issues)) {
            $preview = array_slice(array_values(array_unique($issues)), 0, 12);
            $more = count($issues) > 12 ? ' (dan error lainnya...)' : '';
            return redirect()
                ->route('extractor.ejm.detailpipeend', ['header_id' => $header->id])
                ->with('error', 'Generate dibatalkan. ' . implode(' | ', $preview) . $more);
        }

        DB::table('ejm_detail_pipe_ends')->upsert(
            $rows,
            ['pce_item_id'],
            [
                'length',
                'time_cutting_minute',
                'time_bevel_minute',
                'time_grinding_minute',
                'total_time_minute',
                'raw_material',
                'raw_material_code',
                'price_sqm',
                'cost_raw_material',
                'price_validasi_machine',
                'cost_machine',
                'rate_per_hour',
                'quantity',
                'total_cost',
                'total_price',
                'part_number_pipe_end',
                'description_pipe_end',
                'updated_at',
            ]
        );

        return redirect()
            ->route('extractor.ejm.detailpipeend', ['header_id' => $header->id])
            ->with('success', 'Generate Detail Pipe End selesai. Upsert: ' . count($rows) . ' item.');
    }

    private function buildPipeEndProcessMap($items)
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
            ->whereRaw('UPPER(d.component_type) = ?', ['PIPE END'])
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

