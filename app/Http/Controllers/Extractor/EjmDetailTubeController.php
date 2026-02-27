<?php

namespace App\Http\Controllers\Extractor;

use App\Http\Controllers\Controller;
use App\Models\PceHeader;
use Illuminate\Http\Request;
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

        $selectedHeader = null;
        if ($headerId > 0) {
            $selectedHeader = PceHeader::with([
                'items' => fn ($query) => $query->with([
                    'shape:id,shape_name',
                    'typeConfig:id,type_name',
                    'materialBellow:id,material,part_number,description,naming,price_sqm,price_kg,price_gram',
                    'materialFlange:id,material,part_number,description,naming,price_sqm,price_kg,price_gram',
                    'materialPipeEnd:id,material,part_number,description,naming,size_in,type,thk_mm,price_sqm,price_kg,price_gram',
                ])->orderBy('id'),
            ])->find($headerId);
        } elseif ($pceNumber !== '') {
            $selectedHeader = PceHeader::where('pce_number', $pceNumber)
                ->with([
                    'items' => fn ($query) => $query->with([
                        'shape:id,shape_name',
                        'typeConfig:id,type_name',
                        'materialBellow:id,material,part_number,description,naming,price_sqm,price_kg,price_gram',
                        'materialFlange:id,material,part_number,description,naming,price_sqm,price_kg,price_gram',
                        'materialPipeEnd:id,material,part_number,description,naming,size_in,type,thk_mm,price_sqm,price_kg,price_gram',
                    ])->orderBy('id'),
                ])
                ->first();
        }

        $items = $selectedHeader?->items ?? collect();

        $recentHeaders = PceHeader::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'pce_number', 'project_name']);

        return view('extractor.detail-tube', [
            'pceNumber' => $pceNumber,
            'selectedHeader' => $selectedHeader,
            'items' => $items,
            'recentHeaders' => $recentHeaders,
        ]);
    }
}
