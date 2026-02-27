<?php

namespace App\Http\Controllers;

use App\Models\DataValidasiEjmExpansionJoint;
use App\Models\DataValidasiEjmMaterial;
use App\Models\PceHeader;
use App\Models\PceItem;
use App\Models\ProductTypeConfig;
use App\Models\Shape;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PceItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = PceItem::query()
            ->with([
                'header:id,pce_number',
                'shape:id,shape_name',
                'typeConfig:id,type_name',
                'materialBellow:id,part_number,material',
                'materialFlange:id,part_number,material',
                'materialPipeEnd:id,part_number,material',
            ])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim((string) $request->query('q'));
                $q->where(function ($inner) use ($term) {
                    $inner->where('plat_number', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%')
                        ->orWhereHas('header', fn ($h) => $h->where('pce_number', 'like', '%' . $term . '%'));
                });
            })
            ->when($request->filled('pce_header_id'), fn ($q) => $q->where('pce_header_id', (int) $request->query('pce_header_id')))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('pce-order-list.index', [
            'items' => $items,
            'pceHeaders' => PceHeader::query()->orderByDesc('id')->get(['id', 'pce_number']),
        ]);
    }

    public function create(): View
    {
        return view('pce-order-list.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        PceItem::create($this->validatedPayload($request, null));

        return redirect()->route('pce-orderlist.index')->with('success', 'PCE Item berhasil dibuat.');
    }

    public function show(PceItem $item): View
    {
        $item->load([
            'header:id,pce_number',
            'shape:id,shape_name',
            'typeConfig:id,type_name',
            'materialBellow:id,part_number,material',
            'materialFlange:id,part_number,material',
            'materialPipeEnd:id,part_number,material',
        ]);

        return view('pce-order-list.show', ['item' => $item]);
    }

    public function edit(PceItem $item): View
    {
        return view('pce-order-list.edit', array_merge(
            $this->formOptions(),
            ['item' => $item]
        ));
    }

    public function update(Request $request, PceItem $item): RedirectResponse
    {
        $item->update($this->validatedPayload($request, $item->id));

        return redirect()->route('pce-orderlist.index')->with('success', 'PCE Item berhasil diupdate.');
    }

    public function destroy(PceItem $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('pce-orderlist.index')->with('success', 'PCE Item berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?int $ignoreId): array
    {
        $data = $request->validate([
            'pce_header_id' => ['required', 'exists:pce_headers,id'],
            'plat_number' => [
                'required',
                'string',
                'max:120',
                Rule::unique('pce_items', 'plat_number')
                    ->where(fn ($q) => $q->where('pce_header_id', (int) $request->input('pce_header_id')))
                    ->ignore($ignoreId),
            ],
            'description' => ['nullable', 'string'],
            'qty' => ['required', 'integer', 'min:1'],
            'shape_id' => ['nullable', 'exists:shapes,id'],
            'type_config_id' => ['nullable', 'exists:product_type_configs,id'],
            'nb' => ['required', 'integer', 'min:1'],
            'noc' => ['nullable', 'integer', 'min:0'],
            'material_bellow_id' => ['nullable', 'exists:ejm_special_materials,id'],
            'material_flange_id' => ['nullable', 'exists:ejm_special_materials,id'],
            'material_pipe_end_id' => ['nullable', 'exists:ejm_special_materials,id'],
        ]);

        $validation = DataValidasiEjmExpansionJoint::query()
            ->where('is_active', true)
            ->where('nb', (int) $data['nb'])
            ->orderByDesc('id')
            ->first();

        if (! $validation) {
            abort(422, 'Data validasi expansion joint untuk NB tersebut tidak ditemukan.');
        }

        return [
            'pce_header_id' => (int) $data['pce_header_id'],
            'plat_number' => trim((string) $data['plat_number']),
            'description' => $data['description'] ?? null,
            'qty' => (int) $data['qty'],
            'shape_id' => $data['shape_id'] ?? null,
            'type_config_id' => $data['type_config_id'] ?? null,
            'nb' => (int) $data['nb'],
            'noc' => $this->resolveNoc($data['noc'] ?? null, $validation->noc),
            'id_mm' => $validation->id_mm,
            'od_mm' => $validation->od_mm,
            'thk_mm' => $validation->thk,
            'ply' => $validation->ly !== null ? (int) $validation->ly : null,
            'expansion_joint_validation_id' => $validation->id,
            'material_bellow_id' => $data['material_bellow_id'] ?? null,
            'material_flange_id' => $data['material_flange_id'] ?? null,
            'material_pipe_end_id' => $data['material_pipe_end_id'] ?? null,
            'status' => 'DRAFT',
        ];
    }

    private function resolveNoc(mixed $inputNoc, mixed $defaultNoc): int
    {
        $maxNoc = is_numeric($defaultNoc) ? (int) $defaultNoc : 0;
        $selectedNoc = is_numeric($inputNoc) ? (int) $inputNoc : $maxNoc;

        if ($maxNoc > 0 && $selectedNoc > $maxNoc) {
            throw ValidationException::withMessages([
                'noc' => "NOC tidak boleh lebih dari NOC Default ({$maxNoc}) untuk NB yang dipilih.",
            ]);
        }

        return $selectedNoc;
    }

    private function formOptions(): array
    {
        $materials = DataValidasiEjmMaterial::query()
            ->where('is_active', true)
            ->orderBy('component')
            ->orderBy('material')
            ->get(['id', 'component', 'material', 'part_number', 'thk_mm', 'size_in', 'type']);

        return [
            'pceHeaders' => PceHeader::query()->orderByDesc('id')->get(['id', 'pce_number']),
            'shapes' => Shape::query()->where('is_active', true)->orderBy('shape_name')->get(['id', 'shape_name']),
            'types' => ProductTypeConfig::query()
                ->where('is_active', true)
                ->orderBy('shape_id')
                ->orderBy('sort_order')
                ->orderBy('type_name')
                ->get(['id', 'shape_id', 'type_name']),
            'nbOptions' => DataValidasiEjmExpansionJoint::query()
                ->where('is_active', true)
                ->whereNotNull('nb')
                ->orderByDesc('id')
                ->get(['id', 'nb', 'noc', 'id_mm', 'od_mm', 'thk', 'ly'])
                ->unique('nb')
                ->sortBy('nb')
                ->values(),
            'materialBellow' => $materials->filter(fn ($m) => strtoupper((string) $m->component) === 'BELLOW')->values(),
            'materialFlange' => $materials->filter(fn ($m) => strtoupper((string) $m->component) === 'FLANGE')->values(),
            'materialPipeEnd' => $materials->filter(fn ($m) => in_array(strtoupper((string) $m->component), ['PIPE - NIPPLE', 'PIPE NIPPLE', 'PIPE-NIPPLE', 'PIPE_NIPPLE'], true))->values(),
        ];
    }
}
