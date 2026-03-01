<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EjmDetailTubeController extends Controller
{
    /**
     * Show detail tube data extracted from PCE order list.
     */
    public function index(Request $request): View
    {
        $pceNumber = trim((string) $request->query('pce_number', ''));
        $headerId = (int) $request->query('header_id', 0);

        $itemRelations = [
            'shape:id,shape_name',
            'typeConfig:id,type_name',
            'validation:id,inch,nb,width,length,id_mm,od_mm,thk,ly,noc,lc,p,lpe,gpf,oal,oal_b,bl,can_length,width1,width2',
            'detailTube:id,pce_item_id,nama_barang,part_number_plate,description_plate,mesin_roll_minute,seam_welding_minute,welding_machine_minute,welding_rod_minute,manpower,penetrant,rate_mesin_roll,rate_seam_welding,rate_welding_machine,rate_welding_rod,harga_material,total,part_number,description',
            'materialBellow:id,material,part_number,description,naming,price_sqm,price_kg,price_gram',
            'materialFlange:id,material,part_number,description,naming,size_in,type,sch,code1,code2,price_sqm,price_kg,price_gram',
            'materialPipeEnd:id,material,part_number,description,naming,size_in,type,thk_mm,code1,price_sqm,price_kg,price_gram',
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
        $processMap = $this->buildProcessMap($items);

        $recentHeaders = PceHeader::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'pce_number', 'project_name', 'drawing_no']);

        return view('extractor.detail-tube', [
            'pceNumber' => $pceNumber,
            'selectedHeader' => $selectedHeader,
            'items' => $items,
            'recentHeaders' => $recentHeaders,
            'processMap' => $processMap,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'header_id' => ['required', 'integer', 'exists:pce_headers,id'],
        ]);

        $header = PceHeader::with([
            'items' => fn ($query) => $query->with([
                'typeConfig:id,type_name',
                'validation:id,nb,noc,lc,p,lpe,gpf,oal,oal_b,bl,width1,width2,id_mm,od_mm',
                'detailTube:id,pce_item_id',
                'materialBellow:id,material,part_number,price_sqm,price_kg,price_gram,code1',
                'materialFlange:id,material,part_number,price_sqm,price_kg,price_gram',
                'materialPipeEnd:id,material,part_number,description,price_sqm,price_kg,price_gram,code1',
            ])->orderBy('id'),
        ])->findOrFail((int) $validated['header_id']);

        if ($header->items->isEmpty()) {
            return redirect()
                ->route('extractor.ejm.detailtube', ['header_id' => $header->id])
                ->with('error', 'Tidak ada PCE Item pada header ini.');
        }

        $processMap = $this->buildProcessMap($header->items);
        $costRateResult = $this->loadCostRates();
        $costRates = $costRateResult['rates'];
        $issues = [];
        if (! empty($costRateResult['missing'])) {
            $issues[] = 'Cost Product kosong: ' . implode(', ', $costRateResult['missing']);
        }
        $now = now();
        $rows = [];

        foreach ($header->items as $item) {
            if (! $item->validation) {
                $issues[] = "Item {$item->id}: validasi expansion joint belum terhubung.";
                continue;
            }
            if (! $item->materialBellow && ! $item->materialPipeEnd) {
                $issues[] = "Item {$item->id}: material bellow/pipe-end belum diisi.";
                continue;
            }

            $processRows = $processMap[(int) ($item->nb ?? 0)] ?? collect();
            $roll = $processRows->get('rolling');
            $seam = $processRows->get('seam_welding');
            $weldMachine = $processRows->get('welding');

            if (! $roll || ! $seam || ! $weldMachine) {
                $issues[] = "Item {$item->id} NB {$item->nb}: proses rolling/seam welding/welding belum lengkap di validasi proses.";
                continue;
            }

            $geometry = $this->buildTubeGeometry($item);
            $platePartNumber = $this->buildPlatePartNumber($item);
            $plateDescription = $this->buildPlateDescription($item);

            $mesinRollMinute = $roll?->tube_inner;
            $seamWeldingMinute = $seam?->tube_inner;
            $weldingMachineMinute = $weldMachine?->tube_inner;
            $weldingRodMinute = null;

            $rateMesinRoll = $roll?->price_tube_inner ?? $costRates['rolling'];
            $rateSeamWelding = $seam?->price_tube_inner ?? $costRates['seam_welding'];
            $rateWeldingMachine = $weldMachine?->price_tube_inner ?? $costRates['welding_machine'];
            $rateWeldingRod = $costRates['welding_rod'];

            if ($mesinRollMinute === null || $seamWeldingMinute === null || $weldingMachineMinute === null) {
                $issues[] = "Item {$item->id} NB {$item->nb}: minutes proses kosong (rolling/seam/welding).";
                continue;
            }

            if ($rateMesinRoll === null || $rateSeamWelding === null || $rateWeldingMachine === null || $costRates['manpower'] === null) {
                $issues[] = "Item {$item->id} NB {$item->nb}: rate proses/manpower kosong (cek ejm_process_definitions atau cost_products).";
                continue;
            }

            $manpower = 2.0;
            $hargaMaterial = $this->calculatePlateMaterialCost($item, $geometry['oal_b']);

            $biayaProses =
                ((float) ($mesinRollMinute ?? 0) * (float) ($rateMesinRoll ?? 0)) +
                ((float) ($seamWeldingMinute ?? 0) * (float) ($rateSeamWelding ?? 0)) +
                ((float) ($weldingMachineMinute ?? 0) * (float) ($rateWeldingMachine ?? 0)) +
                ((float) ($weldingRodMinute ?? 0) * (float) ($rateWeldingRod ?? 0)) +
                ($manpower * (float) $costRates['manpower']);

            $total = (float) $hargaMaterial + $biayaProses;

            $rows[] = [
                'pce_item_id' => $item->id,
                'nama_barang' => $item->description ?: $item->typeConfig?->type_name,
                'part_number_plate' => $platePartNumber,
                'description_plate' => $plateDescription,
                'mesin_roll_minute' => $mesinRollMinute,
                'seam_welding_minute' => $seamWeldingMinute,
                'welding_machine_minute' => $weldingMachineMinute,
                'welding_rod_minute' => $weldingRodMinute,
                'manpower' => $manpower,
                'penetrant' => null,
                'rate_mesin_roll' => $rateMesinRoll,
                'rate_seam_welding' => $rateSeamWelding,
                'rate_welding_machine' => $rateWeldingMachine,
                'rate_welding_rod' => $rateWeldingRod,
                'harga_material' => $hargaMaterial,
                'total' => $total,
                'part_number' => $item->materialBellow?->part_number,
                'description' => $item->description,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($issues)) {
            $preview = array_slice(array_values(array_unique($issues)), 0, 12);
            $more = count($issues) > 12 ? ' (dan error lainnya...)' : '';
            return redirect()
                ->route('extractor.ejm.detailtube', ['header_id' => $header->id])
                ->with('error', 'Generate dibatalkan. ' . implode(' | ', $preview) . $more);
        }

        DB::table('ejm_detail_tubes')->upsert(
            $rows,
            ['pce_item_id'],
            [
                'nama_barang',
                'part_number_plate',
                'description_plate',
                'mesin_roll_minute',
                'seam_welding_minute',
                'welding_machine_minute',
                'welding_rod_minute',
                'manpower',
                'penetrant',
                'rate_mesin_roll',
                'rate_seam_welding',
                'rate_welding_machine',
                'rate_welding_rod',
                'harga_material',
                'total',
                'part_number',
                'description',
                'updated_at',
            ]
        );

        return redirect()
            ->route('extractor.ejm.detailtube', ['header_id' => $header->id])
            ->with('success', 'Generate Detail Tube selesai. Upsert: ' . count($rows) . ' item.');
    }

    private function buildProcessMap($items)
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
            ->when($nbList->isNotEmpty(), fn ($query) => $query->whereIn('t.nb', $nbList))
            ->orderBy('t.nb')
            ->orderBy('d.sequence')
            ->orderBy('t.id')
            ->get([
                't.nb',
                'd.component_type',
                'd.process_name',
                DB::raw('t.minutes_inner as tube_inner'),
                DB::raw('t.minutes_outer as tube_outer'),
                DB::raw($innerRateExpr . ' as price_tube_inner'),
                DB::raw($outerRateExpr . ' as price_tube_outer'),
            ])
            ->groupBy('nb')
            ->map(function ($rows) {
                $componentPriority = [
                    'BELLOWS' => 10,
                    'COLLAR' => 20,
                    'GENERIC' => 30,
                    'PIPE END' => 40,
                ];

                $ordered = $rows->sortBy(function ($row) use ($componentPriority) {
                    $component = strtoupper((string) ($row->component_type ?? ''));
                    return $componentPriority[$component] ?? 999;
                });

                $selected = collect();
                foreach ($ordered as $row) {
                    $key = strtolower((string) $row->process_name);
                    $key = str_replace([' ', '-'], '_', $key);
                    if (! $selected->has($key)) {
                        $selected->put($key, $row);
                    }
                }

                return $selected;
            });
    }

    private function buildTubeGeometry(object $item): array
    {
        $validation = $item->validation;
        $noc = is_numeric($item->noc ?? null) ? (float) $item->noc : (float) ($validation?->noc ?? 0);
        $pitch = (float) ($validation?->p ?? 0);
        $lc = (float) ($validation?->lc ?? 0);
        $lpe = (float) ($validation?->lpe ?? 0);
        $gpf = (float) ($validation?->gpf ?? 0);

        $bl = $noc * $pitch; // BL = NOC * Pitch
        $oalB = (2 * $lc) + $bl; // OALB = (2*LC) + BL
        $oal = $oalB + (2 * $lpe) + (2 * $lc) + (2 * $gpf); // OAL = OALB + (2*LPE) + (2*LC) + (2*GPF)

        return [
            'noc' => $noc,
            'pitch' => $pitch,
            'lc' => $lc,
            'lpe' => $lpe,
            'gpf' => $gpf,
            'bl' => $bl,
            'oal_b' => $oalB,
            'oal' => $oal,
        ];
    }

    private function buildPlatePartNumber(object $item): string
    {
        $materialName = strtoupper((string) ($item->materialBellow?->material ?? $item->materialPipeEnd?->material ?? 'CS'));
        $code = 'SS4X';
        if (str_contains($materialName, '316')) {
            $code = 'SS6X';
        } elseif (str_contains($materialName, '304')) {
            $code = 'SS5X';
        }

        return sprintf('BAR-FLAT-%s-00.%03d', $code, (int) $item->id);
    }

    private function buildPlateDescription(object $item): string
    {
        $material = (string) ($item->materialBellow?->material ?? $item->materialPipeEnd?->material ?? '-');
        $thk = $item->thk_mm !== null ? rtrim(rtrim(number_format((float) $item->thk_mm, 3, '.', ''), '0'), '.') : '-';
        return sprintf('BAR-FLAT-%s, Size : Thk %s', $material, $thk);
    }

    private function calculatePlateMaterialCost(object $item, float $oalB): float
    {
        $material = $item->materialBellow ?? $item->materialPipeEnd ?? null;
        if (! $material || $oalB <= 0) {
            return 0.0;
        }

        $widthMm = (float) ($item->validation?->width1 ?? 0);
        if ($widthMm <= 0) {
            $diameter = (float) ($item->od_mm ?? $item->validation?->od_mm ?? 0);
            $widthMm = $diameter > 0 ? (M_PI * $diameter) : 0.0;
        }
        if ($widthMm <= 0) {
            return 0.0;
        }

        $ply = max(1, (int) ($item->ply ?? 1));
        $thkMm = max(0.1, (float) ($item->thk_mm ?? $material->thk_mm ?? 0.5));

        $areaM2 = ($oalB / 1000) * ($widthMm / 1000) * $ply;
        $volumeM3 = $areaM2 * ($thkMm / 1000);
        $weightKg = $volumeM3 * 7850; // steel density
        $weightGr = $weightKg * 1000;

        if ($material->price_gram !== null) {
            return $weightGr * (float) $material->price_gram;
        }
        if ($material->price_kg !== null) {
            return $weightKg * (float) $material->price_kg;
        }
        if ($material->price_sqm !== null) {
            return $areaM2 * (float) $material->price_sqm;
        }

        return 0.0;
    }

    private function loadCostRates(): array
    {
        $codes = [
            'EJM_ROLLING',
            'EJM_SEAM_WELDING',
            'EJM_WELDING_MACHINE',
            'EJM_WELDING_ROD',
            'EJM_MANPOWER',
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
                'rolling' => $map['EJM_ROLLING'] ?? null,
                'seam_welding' => $map['EJM_SEAM_WELDING'] ?? null,
                'welding_machine' => $map['EJM_WELDING_MACHINE'] ?? null,
                'welding_rod' => $map['EJM_WELDING_ROD'] ?? null,
                'manpower' => $map['EJM_MANPOWER'] ?? null,
            ],
            'missing' => $missing,
        ];
    }
}
