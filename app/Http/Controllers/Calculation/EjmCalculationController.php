<?php

namespace App\Http\Controllers\Calculation;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DataValidasiEjmExpansionJoint;
use App\Models\DataValidasiEjmMaterial;
use App\Models\Marketing;
use App\Models\PceHeader;
use App\Models\ProductTypeConfig;
use App\Models\Shape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EjmCalculationController extends Controller
{
    public function index(Request $request): View
    {
        $headerSearch = trim((string) $request->query('header_search', ''));
        $headerDate = trim((string) $request->query('header_date', ''));

        $headers = PceHeader::query()
            ->with([
                'endUser:id,company_code,company_name',
                'sales:id,marketing_no,name',
            ])
            ->withCount('items')
            ->when($headerSearch !== '', function ($query) use ($headerSearch) {
                $query->where(function ($inner) use ($headerSearch) {
                    $inner->where('pce_number', 'like', '%' . $headerSearch . '%')
                        ->orWhere('project_of_name', 'like', '%' . $headerSearch . '%');
                });
            })
            ->when($headerDate !== '', fn ($query) => $query->whereDate('date', $headerDate))
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $selectedHeaderId = (int) $request->query('header_id', 0);
        if ($selectedHeaderId <= 0 && $headers->isNotEmpty()) {
            $selectedHeaderId = (int) $headers->first()->id;
        }

        $selectedHeader = null;
        if ($selectedHeaderId > 0) {
            $selectedHeader = PceHeader::query()
                ->with([
                    'endUser:id,company_code,company_name',
                    'sales:id,marketing_no,name',
                    'items' => fn ($query) => $query->with([
                        'shape:id,shape_code,shape_name',
                        'typeConfig:id,type_code,type_name,shape_id',
                        'materialBellow:id,part_number,naming',
                        'materialFlange:id,part_number,naming',
                        'materialPipeEnd:id,part_number,naming',
                    ])->orderBy('line_no'),
                ])
                ->find($selectedHeaderId);
        }

        $summaryRows = collect();
        if ($selectedHeader) {
            $summaryRows = $selectedHeader->items
                ->groupBy(function ($item) {
                    return implode('|', [
                        $item->shape_id ?? '',
                        $item->type_config_id ?? '',
                        $item->size_nb ?? '',
                        $item->noc ?? '',
                        $item->material_bellow_id ?? '',
                        $item->material_flange_id ?? '',
                        $item->material_pipe_end_id ?? '',
                        $item->status ?? '',
                    ]);
                })
                ->map(function ($group) {
                    $first = $group->first();
                    $first->quantity = (int) $group->sum(fn ($row) => (int) ($row->quantity ?? 1));
                    return $first;
                })
                ->values();
        }

        $productEjmId = DB::table('products')->where('product_code', 'EJM')->value('id');

        $shapes = Shape::query()
            ->where('is_active', true)
            ->orderBy('shape_code')
            ->get(['id', 'shape_code', 'shape_name']);

        $types = ProductTypeConfig::query()
            ->when($productEjmId, fn ($query) => $query->where('product_id', (int) $productEjmId))
            ->where('is_active', true)
            ->orderBy('shape_id')
            ->orderBy('sort_order')
            ->orderBy('type_code')
            ->get(['id', 'shape_id', 'type_code', 'type_name']);

        $sizeCatalog = DataValidasiEjmExpansionJoint::query()
            ->where('is_active', true)
            ->whereNotNull('nb')
            ->orderByDesc('standard_version_id')
            ->orderByDesc('id')
            ->get(['id', 'nb', 'noc', 'id_mm', 'od_mm', 'thk', 'ly'])
            ->unique('nb')
            ->values()
            ->sortBy('nb')
            ->values();

        $materialRows = DataValidasiEjmMaterial::query()
            ->where('is_active', true)
            ->whereNotNull('part_number')
            ->leftJoin('materials', 'materials.part_number', '=', 'ejm_special_materials.part_number')
            ->orderBy('component')
            ->orderBy('material')
            ->get([
                'ejm_special_materials.id',
                DB::raw("CASE
                    WHEN UPPER(ejm_special_materials.component) = 'BELLOW' THEN 'BELLOW'
                    WHEN UPPER(ejm_special_materials.component) IN ('PIPE - NIPPLE','PIPE NIPPLE','PIPE-NIPPLE','PIPE_NIPPLE') THEN 'PIPE_NIPPLE'
                    WHEN UPPER(ejm_special_materials.component) = 'FLANGE' THEN 'FLANGE'
                    ELSE UPPER(ejm_special_materials.component)
                END AS material_role"),
                DB::raw('ejm_special_materials.material AS material_name'),
                DB::raw('materials.id AS material_id'),
                'ejm_special_materials.part_number',
                'ejm_special_materials.naming',
                'ejm_special_materials.thk_mm',
                DB::raw('ejm_special_materials.ply AS jumlah_ply'),
                'ejm_special_materials.size_in',
                'ejm_special_materials.sch',
                'ejm_special_materials.type',
                'ejm_special_materials.price_sqm',
                'ejm_special_materials.price_kg',
                'ejm_special_materials.price_gram',
            ])
            ->filter(fn ($row) => $row->material_id !== null)
            ->values();

        $bellowMaterials = $materialRows->where('material_role', 'BELLOW')->values();
        $pipeNippleMaterials = $materialRows->where('material_role', 'PIPE_NIPPLE')->values();
        $flangeMaterials = $materialRows->where('material_role', 'FLANGE')->values();

        $endUsers = Company::query()
            ->orderBy('company_name')
            ->get(['id', 'company_code', 'company_name']);

        $salesUsers = Marketing::query()
            ->orderBy('name')
            ->get(['id', 'marketing_no', 'name']);

        return view('calculation.ejm', [
            'headers' => $headers,
            'selectedHeader' => $selectedHeader,
            'selectedHeaderId' => $selectedHeaderId,
            'shapes' => $shapes,
            'types' => $types,
            'sizeCatalog' => $sizeCatalog,
            'bellowMaterials' => $bellowMaterials,
            'pipeNippleMaterials' => $pipeNippleMaterials,
            'flangeMaterials' => $flangeMaterials,
            'endUsers' => $endUsers,
            'salesUsers' => $salesUsers,
            'summaryRows' => $summaryRows,
            'headerSearch' => $headerSearch,
            'headerDate' => $headerDate,
        ]);
    }
}
