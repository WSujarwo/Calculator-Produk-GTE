<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EjmDetailMetalBellowsController extends Controller
{
    public function index(Request $request): View
    {
        $pceNumber = trim((string) $request->query('pce_number', ''));
        $headerId = (int) $request->query('header_id', 0);

        $itemRelations = [
            'shape:id,shape_name',
            'typeConfig:id,type_name',
            'validation:id,inch,nb,width,length,id_mm,od_mm,thk,ly,noc,lc,p,lpe,gpf,oal,oal_b,can_length,width1,spare',
            'materialBellow:id,material,part_number,description,naming,price_sqm,price_kg,price_gram',
            'detailMetalBellows:id,pce_item_id,width,length,oal,noc,material,part_number_bellows,description_bellows,part_number_collar,description_collar,welding_rod_qty,mesin_qty,manpower_qty,grinda_poles_qty,disc_poles_qty,harga_bellows,harga_collar,rate_welding_rod,rate_mesin,rate_manpower,rate_grinda_poles,rate_disc_poles,total,grand_total,part_number_metal_bellows,description_metal_bellows',
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

        return view('extractor.detail-metal-bellows', [
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
                'shape:id,shape_name',
                'typeConfig:id,type_name',
                'validation:id,inch,nb,width,length,id_mm,od_mm,thk,ly,noc,lc,p,lpe,gpf,oal,oal_b,can_length,width1,spare',
                'materialBellow:id,material,part_number,description,naming,price_sqm,price_kg,price_gram,thk_mm',
            ])->orderBy('id'),
        ])->findOrFail((int) $validated['header_id']);

        if ($header->items->isEmpty()) {
            return redirect()
                ->route('extractor.ejm.detailmetalbellows', ['header_id' => $header->id])
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
            if (! $item->materialBellow) {
                $issues[] = "Item {$item->id}: material bellow belum diisi.";
                continue;
            }

            $collarMaterial = $this->resolveCollarMaterial($item);
            if (! $collarMaterial) {
                $issues[] = "Item {$item->id}: material collar tidak ditemukan.";
                continue;
            }

            if (in_array(null, $rates, true)) {
                $issues[] = "Item {$item->id}: rate cost_product belum lengkap.";
                continue;
            }

            $geom = $this->buildGeometry($item);
            if ($geom['width'] <= 0 || $geom['length'] <= 0) {
                $issues[] = "Item {$item->id}: width/length tidak valid untuk hitung biaya.";
                continue;
            }

            $square = ($geom['width'] * $geom['length']) / 1000000;
            $ply = max(1, (int) ($item->ply ?? 1));
            $spare = (float) ($item->validation?->spare ?? 5);

            $hargaBellows = $square * $ply * (float) ($item->materialBellow->price_sqm ?? 0) * (1 + ($spare / 100));
            $hargaCollar = $square * 2 * (float) ($collarMaterial->price_sqm ?? 0) * (1 + ($spare / 100));

            $weldingRodQty = 1.0;
            $mesinQty = 1.0;
            $manpowerQty = 2.0;
            $grindaQty = 1.0;
            $discQty = 1.0;

            $total =
                $hargaBellows +
                $hargaCollar +
                ($weldingRodQty * $rates['welding_rod']) +
                ($mesinQty * $rates['mesin']) +
                ($manpowerQty * $rates['manpower']) +
                ($grindaQty * $rates['grinda_poles']) +
                ($discQty * $rates['disc_poles']);

            $qty = max(1, (int) ($item->qty ?? 1));
            $grandTotal = $total * $qty;

            $rows[] = [
                'pce_item_id' => $item->id,
                'width' => $geom['width'],
                'length' => $geom['length'],
                'oal' => $geom['oal'],
                'noc' => $geom['noc'],
                'material' => $item->materialBellow->material,
                'part_number_bellows' => $item->materialBellow->part_number,
                'description_bellows' => $item->materialBellow->description ?? $item->materialBellow->naming,
                'part_number_collar' => $collarMaterial->part_number,
                'description_collar' => $collarMaterial->description ?? $collarMaterial->naming,
                'welding_rod_qty' => $weldingRodQty,
                'mesin_qty' => $mesinQty,
                'manpower_qty' => $manpowerQty,
                'grinda_poles_qty' => $grindaQty,
                'disc_poles_qty' => $discQty,
                'harga_bellows' => $hargaBellows,
                'harga_collar' => $hargaCollar,
                'rate_welding_rod' => $rates['welding_rod'],
                'rate_mesin' => $rates['mesin'],
                'rate_manpower' => $rates['manpower'],
                'rate_grinda_poles' => $rates['grinda_poles'],
                'rate_disc_poles' => $rates['disc_poles'],
                'total' => $total,
                'grand_total' => $grandTotal,
                'part_number_metal_bellows' => 'MBELL-' . str_pad((string) $item->id, 4, '0', STR_PAD_LEFT),
                'description_metal_bellows' => (string) ($item->description ?? $item->typeConfig?->type_name ?? 'Metal Bellows'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($issues)) {
            $preview = array_slice(array_values(array_unique($issues)), 0, 12);
            $more = count($issues) > 12 ? ' (dan error lainnya...)' : '';
            return redirect()
                ->route('extractor.ejm.detailmetalbellows', ['header_id' => $header->id])
                ->with('error', 'Generate dibatalkan. ' . implode(' | ', $preview) . $more);
        }

        DB::table('ejm_detail_metal_bellows')->upsert(
            $rows,
            ['pce_item_id'],
            [
                'width',
                'length',
                'oal',
                'noc',
                'material',
                'part_number_bellows',
                'description_bellows',
                'part_number_collar',
                'description_collar',
                'welding_rod_qty',
                'mesin_qty',
                'manpower_qty',
                'grinda_poles_qty',
                'disc_poles_qty',
                'harga_bellows',
                'harga_collar',
                'rate_welding_rod',
                'rate_mesin',
                'rate_manpower',
                'rate_grinda_poles',
                'rate_disc_poles',
                'total',
                'grand_total',
                'part_number_metal_bellows',
                'description_metal_bellows',
                'updated_at',
            ]
        );

        return redirect()
            ->route('extractor.ejm.detailmetalbellows', ['header_id' => $header->id])
            ->with('success', 'Generate Detail Metal Bellows selesai. Upsert: ' . count($rows) . ' item.');
    }

    private function buildGeometry(object $item): array
    {
        $v = $item->validation;
        $noc = is_numeric($item->noc ?? null) ? (float) $item->noc : (float) ($v?->noc ?? 0);
        $pitch = (float) ($v?->p ?? 0);
        $lc = (float) ($v?->lc ?? 0);
        $lpe = (float) ($v?->lpe ?? 0);
        $gpf = (float) ($v?->gpf ?? 0);
        $bl = $noc * $pitch;
        $oalB = (2 * $lc) + $bl;
        $oal = $oalB + (2 * $lpe) + (2 * $lc) + (2 * $gpf);

        $width = (float) ($v?->width ?? 0);
        if ($width <= 0) {
            $width = (float) ($v?->width1 ?? 0);
        }
        if ($width <= 0) {
            $od = (float) ($item->od_mm ?? $v?->od_mm ?? 0);
            $width = $od > 0 ? (M_PI * $od) : 0;
        }

        $length = (float) ($v?->length ?? 0);
        if ($length <= 0) {
            $length = (float) ($v?->can_length ?? $oalB ?? $oal);
        }

        return [
            'noc' => $noc,
            'oal' => $oal > 0 ? $oal : ($v?->oal ?? 0),
            'width' => $width,
            'length' => $length,
        ];
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
            return $query->orderByRaw('ABS(COALESCE(thk_mm, 0) - ?)', [$thk])
                ->orderBy('id')
                ->first(['id', 'material', 'part_number', 'price_sqm', 'description', 'naming']);
        }

        return $query->orderBy('id')->first(['id', 'material', 'part_number', 'price_sqm', 'description', 'naming']);
    }

    private function loadRates(): array
    {
        $codes = [
            'EJM_METAL_WELDING_ROD',
            'EJM_METAL_MESIN',
            'EJM_METAL_MANPOWER',
            'EJM_METAL_GRINDA_POLES',
            'EJM_METAL_DISC_POLES',
            'EJM_MANPOWER_HOUR',
        ];

        $rows = DB::table('cost_products')
            ->whereIn('dlaborno', $codes)
            ->get(['dlaborno', 'cost']);

        $map = [];
        foreach ($rows as $row) {
            $map[(string) $row->dlaborno] = $row->cost !== null ? (float) $row->cost : null;
        }

        // Backward-compatible rule:
        // if EJM_METAL_MANPOWER is empty, fallback to EJM_MANPOWER_HOUR / 60 (per minute).
        $manpowerMetal = $map['EJM_METAL_MANPOWER'] ?? null;
        if ($manpowerMetal === null) {
            $hourRate = $map['EJM_MANPOWER_HOUR'] ?? null;
            if ($hourRate !== null) {
                $manpowerMetal = (float) $hourRate / 60;
            }
        }

        $missing = [];
        foreach (['EJM_METAL_WELDING_ROD', 'EJM_METAL_MESIN', 'EJM_METAL_GRINDA_POLES', 'EJM_METAL_DISC_POLES'] as $code) {
            if (! array_key_exists($code, $map) || $map[$code] === null) {
                $missing[] = $code;
            }
        }
        if ($manpowerMetal === null) {
            $missing[] = 'EJM_METAL_MANPOWER (fallback EJM_MANPOWER_HOUR tidak tersedia)';
        }

        return [
            'rates' => [
                'welding_rod' => $map['EJM_METAL_WELDING_ROD'] ?? null,
                'mesin' => $map['EJM_METAL_MESIN'] ?? null,
                'manpower' => $manpowerMetal,
                'grinda_poles' => $map['EJM_METAL_GRINDA_POLES'] ?? null,
                'disc_poles' => $map['EJM_METAL_DISC_POLES'] ?? null,
            ],
            'missing' => $missing,
        ];
    }
}
