<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EjmDetailFlangeController extends Controller
{
    public function index(Request $request): View
    {
        $pceNumber = trim((string) $request->query('pce_number', ''));
        $headerId = (int) $request->query('header_id', 0);

        $itemRelations = [
            'validation:id,inch,nb',
            'materialFlange:id,material,part_number,description,naming,type,sch,price_sqm,price_kg,price_gram',
            'detailFlange:id,pce_item_id,left_material,left_class,left_type,left_part_number,left_description,left_qty,right_material,right_class,right_type,right_part_number,right_description,right_qty,left_flange_price,left_grinding_painting,left_total,right_flange_price,right_grinding_painting,right_total,rate_per_hour,manpower_qty,total_cost_manpower,total_price',
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
        $rateResult = $this->loadRates();

        $recentHeaders = PceHeader::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'pce_number', 'project_name', 'drawing_no']);

        return view('extractor.detail-flange', [
            'pceNumber' => $pceNumber,
            'selectedHeader' => $selectedHeader,
            'items' => $items,
            'recentHeaders' => $recentHeaders,
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
                'validation:id,inch,nb',
                'materialFlange:id,material,part_number,description,naming,type,sch,price_sqm,price_kg,price_gram',
            ])->orderBy('id'),
        ])->findOrFail((int) $validated['header_id']);

        if ($header->items->isEmpty()) {
            return redirect()
                ->route('extractor.ejm.detailflange', ['header_id' => $header->id])
                ->with('error', 'Tidak ada PCE Item pada header ini.');
        }

        $rateResult = $this->loadRates();
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
            if (! $item->materialFlange) {
                $issues[] = "Item {$item->id}: material flange belum diisi.";
                continue;
            }
            if ($rates['grinding_painting'] === null || $rates['manpower_hour'] === null) {
                $issues[] = "Item {$item->id}: rate grinding/painting atau manpower kosong di cost_products.";
                continue;
            }

            $mf = $item->materialFlange;
            $baseFlangePrice = $this->resolveBasePrice($mf);
            $leftQty = 1.0;
            $rightQty = 1.0;

            $leftFlangePrice = $baseFlangePrice * $leftQty;
            $leftGrinding = (float) $rates['grinding_painting'] * $leftQty;
            $leftTotal = $leftFlangePrice + $leftGrinding;

            $rightFlangePrice = $baseFlangePrice * $rightQty;
            $rightGrinding = (float) $rates['grinding_painting'] * $rightQty;
            $rightTotal = $rightFlangePrice + $rightGrinding;

            $ratePerHour = (float) $rates['manpower_hour'];
            $manpowerQty = 2.0;
            $totalCostManpower = $ratePerHour * $manpowerQty;

            $totalPrice = $leftTotal + $rightTotal + $totalCostManpower;

            $rows[] = [
                'pce_item_id' => $item->id,
                'left_material' => (string) $mf->material,
                'left_class' => (string) ($mf->sch ?? '-'),
                'left_type' => (string) ($mf->type ?? '-'),
                'left_part_number' => (string) $mf->part_number,
                'left_description' => (string) ($mf->description ?? $mf->naming),
                'left_qty' => $leftQty,
                'right_material' => (string) $mf->material,
                'right_class' => (string) ($mf->sch ?? '-'),
                'right_type' => (string) ($mf->type ?? '-'),
                'right_part_number' => (string) $mf->part_number,
                'right_description' => (string) ($mf->description ?? $mf->naming),
                'right_qty' => $rightQty,
                'left_flange_price' => $leftFlangePrice,
                'left_grinding_painting' => $leftGrinding,
                'left_total' => $leftTotal,
                'right_flange_price' => $rightFlangePrice,
                'right_grinding_painting' => $rightGrinding,
                'right_total' => $rightTotal,
                'rate_per_hour' => $ratePerHour,
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
                ->route('extractor.ejm.detailflange', ['header_id' => $header->id])
                ->with('error', 'Generate dibatalkan. ' . implode(' | ', $preview) . $more);
        }

        DB::table('ejm_detail_flanges')->upsert(
            $rows,
            ['pce_item_id'],
            [
                'left_material',
                'left_class',
                'left_type',
                'left_part_number',
                'left_description',
                'left_qty',
                'right_material',
                'right_class',
                'right_type',
                'right_part_number',
                'right_description',
                'right_qty',
                'left_flange_price',
                'left_grinding_painting',
                'left_total',
                'right_flange_price',
                'right_grinding_painting',
                'right_total',
                'rate_per_hour',
                'manpower_qty',
                'total_cost_manpower',
                'total_price',
                'updated_at',
            ]
        );

        return redirect()
            ->route('extractor.ejm.detailflange', ['header_id' => $header->id])
            ->with('success', 'Generate Detail Flange selesai. Upsert: ' . count($rows) . ' item.');
    }

    private function resolveBasePrice(object $material): float
    {
        if ($material->price_sqm !== null) {
            return (float) $material->price_sqm;
        }
        if ($material->price_kg !== null) {
            return (float) $material->price_kg;
        }
        if ($material->price_gram !== null) {
            return (float) $material->price_gram;
        }

        return 0.0;
    }

    private function loadRates(): array
    {
        $codes = [
            'EJM_FLANGE_GRINDING_PAINTING',
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
                'grinding_painting' => $map['EJM_FLANGE_GRINDING_PAINTING'] ?? null,
                'manpower_hour' => $map['EJM_MANPOWER_HOUR'] ?? null,
            ],
            'missing' => $missing,
        ];
    }
}

