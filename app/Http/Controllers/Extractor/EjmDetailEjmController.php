<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EjmDetailEjmController extends Controller
{
    public function index(Request $request): View
    {
        $pceNumber = trim((string) $request->query('pce_number', ''));
        $headerId = (int) $request->query('header_id', 0);

        $itemRelations = [
            'validation:id,inch,nb,id_mm,od_mm,thk,ly',
            'detailEjm:id,pce_item_id,material_bellows,material_pipe_end,material_flange,time_assembly_minute,time_painting_minute,time_finishing_minute,manpower_rate_per_hour,total_time_hour,manpower_cost,total_bellows,total_collar,total_metal_bellows,total_pipe_end,total_flange,total,margin_percent,margin_amount,grand_total',
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
        $recentHeaders = PceHeader::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'pce_number', 'project_name', 'drawing_no']);

        return view('extractor.detail-ejm', [
            'pceNumber' => $pceNumber,
            'selectedHeader' => $selectedHeader,
            'items' => $items,
            'recentHeaders' => $recentHeaders,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'header_id' => ['required', 'integer', 'exists:pce_headers,id'],
        ]);

        $header = PceHeader::with([
            'items' => fn ($query) => $query->with([
                'validation:id,nb',
            ])->orderBy('id'),
        ])->findOrFail((int) $validated['header_id']);

        if ($header->items->isEmpty()) {
            return redirect()
                ->route('extractor.ejm.detailejm', ['header_id' => $header->id])
                ->with('error', 'Tidak ada PCE Item pada header ini.');
        }

        $processMap = $this->buildEjmProductionProcessMap($header->items);
        $manpowerRate = $this->loadManpowerRate();
        $issues = [];
        if ($manpowerRate === null) {
            $issues[] = 'Cost Product kosong: EJM_MANPOWER_HOUR';
        }

        $itemIds = $header->items->pluck('id')->map(fn ($id) => (int) $id)->all();
        $bellowsMap = DB::table('ejm_detail_bellows')
            ->whereIn('pce_item_id', $itemIds)
            ->pluck('total_price', 'pce_item_id');
        $collarMap = DB::table('ejm_detail_collars')
            ->whereIn('pce_item_id', $itemIds)
            ->pluck('total_price', 'pce_item_id');
        $metalMap = DB::table('ejm_detail_metal_bellows')
            ->whereIn('pce_item_id', $itemIds)
            ->pluck('grand_total', 'pce_item_id');
        $pipeEndMap = DB::table('ejm_detail_pipe_ends')
            ->whereIn('pce_item_id', $itemIds)
            ->pluck('total_price', 'pce_item_id');
        $flangeMap = DB::table('ejm_detail_flanges')
            ->whereIn('pce_item_id', $itemIds)
            ->pluck('total_price', 'pce_item_id');
        $existingMargins = DB::table('ejm_detail_ejms')
            ->whereIn('pce_item_id', $itemIds)
            ->pluck('margin_percent', 'pce_item_id');

        $rows = [];
        $now = now();

        foreach ($header->items as $item) {
            $itemId = (int) $item->id;
            $nb = (int) ($item->nb ?? 0);

            if (! $item->validation) {
                $issues[] = "Item {$itemId}: validasi expansion joint belum terhubung.";
                continue;
            }

            $totalBellows = $bellowsMap[$itemId] ?? null;
            $totalCollar = $collarMap[$itemId] ?? null;
            $totalMetalBellows = $metalMap[$itemId] ?? null;
            $totalPipeEnd = $pipeEndMap[$itemId] ?? null;
            $totalFlange = $flangeMap[$itemId] ?? null;

            if ($totalBellows === null) {
                $issues[] = "Item {$itemId}: detail bellows belum digenerate.";
                continue;
            }
            if ($totalCollar === null) {
                $issues[] = "Item {$itemId}: detail collar belum digenerate.";
                continue;
            }
            if ($totalMetalBellows === null) {
                $issues[] = "Item {$itemId}: detail metal bellows belum digenerate.";
                continue;
            }
            if ($totalPipeEnd === null) {
                $issues[] = "Item {$itemId}: detail pipe end belum digenerate.";
                continue;
            }
            if ($totalFlange === null) {
                $issues[] = "Item {$itemId}: detail flange belum digenerate.";
                continue;
            }

            $process = $processMap[$nb] ?? ($processMap['*'] ?? collect());
            $assembly = $process->get('assembly');
            $painting = $process->get('painting');
            $finishing = $process->get('finishing');
            if (! $assembly || ! $painting || ! $finishing) {
                $issues[] = "Item {$itemId} NB {$nb}: proses EJM Production belum lengkap (assembly/painting/finishing).";
                continue;
            }
            if ($manpowerRate === null) {
                $issues[] = "Item {$itemId}: manpower rate tidak ditemukan.";
                continue;
            }

            $timeAssembly = (float) ($assembly?->minutes_inner ?? 0);
            $timePainting = (float) ($painting?->minutes_inner ?? 0);
            $timeFinishing = (float) ($finishing?->minutes_inner ?? 0);
            $totalTimeHour = ($timeAssembly + $timePainting + $timeFinishing) / 60;
            $manpowerCost = $totalTimeHour * (float) $manpowerRate;

            $totalBellowsVal = (float) $totalBellows;
            $totalCollarVal = (float) $totalCollar;
            $totalMetalBellowsVal = (float) $totalMetalBellows;
            $totalPipeEndVal = (float) $totalPipeEnd;
            $totalFlangeVal = (float) $totalFlange;

            $materialBellows = $totalBellowsVal + $totalCollarVal + $totalMetalBellowsVal;
            $materialPipeEnd = $totalPipeEndVal;
            $materialFlange = $totalFlangeVal;

            $total = $totalBellowsVal + $totalCollarVal + $totalMetalBellowsVal + $totalPipeEndVal + $totalFlangeVal + $manpowerCost;

            $marginPercent = (float) ($existingMargins[$itemId] ?? 0);
            $marginAmount = $total * ($marginPercent / 100);
            $grandTotal = $total + $marginAmount;

            $rows[] = [
                'pce_item_id' => $itemId,
                'material_bellows' => $materialBellows,
                'material_pipe_end' => $materialPipeEnd,
                'material_flange' => $materialFlange,
                'time_assembly_minute' => $timeAssembly,
                'time_painting_minute' => $timePainting,
                'time_finishing_minute' => $timeFinishing,
                'manpower_rate_per_hour' => (float) $manpowerRate,
                'total_time_hour' => $totalTimeHour,
                'manpower_cost' => $manpowerCost,
                'total_bellows' => $totalBellowsVal,
                'total_collar' => $totalCollarVal,
                'total_metal_bellows' => $totalMetalBellowsVal,
                'total_pipe_end' => $totalPipeEndVal,
                'total_flange' => $totalFlangeVal,
                'total' => $total,
                'margin_percent' => $marginPercent,
                'margin_amount' => $marginAmount,
                'grand_total' => $grandTotal,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($issues)) {
            $preview = array_slice(array_values(array_unique($issues)), 0, 14);
            $more = count($issues) > 14 ? ' (dan error lainnya...)' : '';
            return redirect()
                ->route('extractor.ejm.detailejm', ['header_id' => $header->id])
                ->with('error', 'Generate dibatalkan. ' . implode(' | ', $preview) . $more);
        }

        DB::table('ejm_detail_ejms')->upsert(
            $rows,
            ['pce_item_id'],
            [
                'material_bellows',
                'material_pipe_end',
                'material_flange',
                'time_assembly_minute',
                'time_painting_minute',
                'time_finishing_minute',
                'manpower_rate_per_hour',
                'total_time_hour',
                'manpower_cost',
                'total_bellows',
                'total_collar',
                'total_metal_bellows',
                'total_pipe_end',
                'total_flange',
                'total',
                'margin_percent',
                'margin_amount',
                'grand_total',
                'updated_at',
            ]
        );

        return redirect()
            ->route('extractor.ejm.detailejm', ['header_id' => $header->id])
            ->with('success', 'Generate Detail EJM selesai. Upsert: ' . count($rows) . ' item.');
    }

    public function updateMargin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'row_id' => ['required', 'integer', 'exists:ejm_detail_ejms,id'],
            'header_id' => ['required', 'integer', 'exists:pce_headers,id'],
            'margin_percent' => ['required', 'numeric', 'min:0', 'max:1000'],
        ]);

        $row = DB::table('ejm_detail_ejms')
            ->where('id', (int) $validated['row_id'])
            ->first(['id', 'total']);

        if (! $row) {
            return redirect()
                ->route('extractor.ejm.detailejm', ['header_id' => (int) $validated['header_id']])
                ->with('error', 'Baris Detail EJM tidak ditemukan.');
        }

        $marginPercent = (float) $validated['margin_percent'];
        $total = (float) ($row->total ?? 0);
        $marginAmount = $total * ($marginPercent / 100);
        $grandTotal = $total + $marginAmount;

        DB::table('ejm_detail_ejms')
            ->where('id', (int) $row->id)
            ->update([
                'margin_percent' => $marginPercent,
                'margin_amount' => $marginAmount,
                'grand_total' => $grandTotal,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('extractor.ejm.detailejm', ['header_id' => (int) $validated['header_id']])
            ->with('success', 'Margin berhasil diupdate.');
    }

    private function buildEjmProductionProcessMap($items)
    {
        $nbList = $items->pluck('nb')
            ->filter(fn ($nb) => is_numeric($nb))
            ->map(fn ($nb) => (int) $nb)
            ->unique()
            ->values();

        $grouped = DB::table('ejm_process_times as t')
            ->join('ejm_process_definitions as d', 'd.id', '=', 't.process_definition_id')
            ->where(function ($query) {
                $query->whereRaw('UPPER(d.component_type) = ?', ['EJM PRODUCTION'])
                    ->orWhereRaw('UPPER(d.component_type) = ?', ['GENERIC']);
            })
            ->whereIn(DB::raw('UPPER(d.process_name)'), ['ASSEMBLY', 'PAINTING', 'FINISHING'])
            ->when($nbList->isNotEmpty(), fn ($query) => $query->whereIn('t.nb', $nbList))
            ->orderBy('t.nb')
            ->orderBy('d.sequence')
            ->orderBy('t.id')
            ->get([
                't.nb',
                'd.process_name',
                't.minutes_inner',
            ])
            ->groupBy('nb')
            ->map(function ($rows) {
                return $rows->mapWithKeys(function ($row) {
                    $key = strtolower((string) $row->process_name);
                    $key = str_replace([' ', '-'], '_', $key);
                    return [$key => $row];
                });
            });

        $defaultRows = DB::table('ejm_process_times as t')
            ->join('ejm_process_definitions as d', 'd.id', '=', 't.process_definition_id')
            ->where(function ($query) {
                $query->whereRaw('UPPER(d.component_type) = ?', ['EJM PRODUCTION'])
                    ->orWhereRaw('UPPER(d.component_type) = ?', ['GENERIC']);
            })
            ->whereNull('t.nb')
            ->whereIn(DB::raw('UPPER(d.process_name)'), ['ASSEMBLY', 'PAINTING', 'FINISHING'])
            ->orderBy('d.sequence')
            ->orderBy('t.id')
            ->get([
                'd.process_name',
                't.minutes_inner',
            ]);

        if ($defaultRows->isNotEmpty()) {
            $grouped['*'] = $defaultRows->mapWithKeys(function ($row) {
                $key = strtolower((string) $row->process_name);
                $key = str_replace([' ', '-'], '_', $key);
                return [$key => $row];
            });
        }

        return $grouped;
    }

    private function loadManpowerRate(): ?float
    {
        $row = DB::table('cost_products')
            ->where('dlaborno', 'EJM_MANPOWER_HOUR')
            ->first(['cost']);

        if (! $row || $row->cost === null) {
            return null;
        }

        return (float) $row->cost;
    }
}
