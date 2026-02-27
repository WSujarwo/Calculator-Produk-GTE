<?php

namespace App\Http\Controllers\Calculation;

use App\Http\Controllers\Controller;
use App\Models\DataValidasiEjmExpansionJoint;
use App\Models\PceHeader;
use App\Models\PceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rows = PceHeader::query()
            ->with(['endUser:id,company_code,company_name', 'sales:id,marketing_no,name'])
            ->withCount('items')
            ->latest('id')
            ->paginate((int) $request->integer('per_page', 20))
            ->withQueryString();

        return response()->json($rows);
    }

    public function storeHeader(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pce_number' => ['required', 'string', 'max:60', 'unique:pce_headers,pce_number'],
            'project_of_name' => ['nullable', 'string', 'max:150'],
            'end_user_id' => ['nullable', 'exists:companies,id'],
            'area' => ['nullable', 'string', 'max:120'],
            'drawing_no' => ['nullable', 'string', 'max:80'],
            'document_no' => ['nullable', 'string', 'max:80'],
            'revision' => ['nullable', 'string', 'max:20'],
            'date' => ['nullable', 'date'],
            'sales_id' => ['nullable', 'exists:marketings,id'],
            'status' => ['nullable', 'in:DRAFT,SUBMITTED,APPROVED,REJECTED,CANCELLED'],
        ]);

        $header = PceHeader::create([
            'pce_number' => $data['pce_number'],
            'project_of_name' => $data['project_of_name'] ?? 'Expansion Joint Metal',
            'end_user_id' => $data['end_user_id'] ?? null,
            'area' => $data['area'] ?? null,
            'drawing_no' => $data['drawing_no'] ?? null,
            'document_no' => $data['document_no'] ?? null,
            'revision' => $data['revision'] ?? null,
            'date' => $data['date'] ?? null,
            'sales_id' => $data['sales_id'] ?? null,
            'status' => $data['status'] ?? 'DRAFT',
        ]);

        return response()->json([
            'message' => 'PCE header berhasil dibuat.',
            'data' => $header,
        ], 201);
    }

    public function updateHeader(Request $request, PceHeader $pceHeader): JsonResponse
    {
        $data = $request->validate([
            'pce_number' => ['required', 'string', 'max:60', 'unique:pce_headers,pce_number,' . $pceHeader->id],
            'project_of_name' => ['nullable', 'string', 'max:150'],
            'end_user_id' => ['nullable', 'exists:companies,id'],
            'area' => ['nullable', 'string', 'max:120'],
            'drawing_no' => ['nullable', 'string', 'max:80'],
            'document_no' => ['nullable', 'string', 'max:80'],
            'revision' => ['nullable', 'string', 'max:20'],
            'date' => ['nullable', 'date'],
            'sales_id' => ['nullable', 'exists:marketings,id'],
            'status' => ['nullable', 'in:DRAFT,SUBMITTED,APPROVED,REJECTED,CANCELLED'],
        ]);

        $pceHeader->update([
            'pce_number' => $data['pce_number'],
            'project_of_name' => $data['project_of_name'] ?? 'Expansion Joint Metal',
            'end_user_id' => $data['end_user_id'] ?? null,
            'area' => $data['area'] ?? null,
            'drawing_no' => $data['drawing_no'] ?? null,
            'document_no' => $data['document_no'] ?? null,
            'revision' => $data['revision'] ?? null,
            'date' => $data['date'] ?? null,
            'sales_id' => $data['sales_id'] ?? null,
            'status' => $data['status'] ?? $pceHeader->status,
        ]);

        return response()->json([
            'message' => 'PCE header berhasil diupdate.',
            'data' => $pceHeader->fresh(),
        ]);
    }

    public function storeItem(Request $request, PceHeader $pceHeader): JsonResponse
    {
        $data = $request->validate([
            'line_no' => ['nullable', 'integer', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'shape_id' => ['nullable', 'exists:shapes,id'],
            'type_config_id' => ['nullable', 'exists:product_type_configs,id'],
            'size_nb' => ['required', 'integer', 'min:1'],
            'noc' => ['nullable', 'integer', 'min:0'],
            'material_bellow_id' => ['nullable', 'exists:materials,id'],
            'material_flange_id' => ['nullable', 'exists:materials,id'],
            'material_pipe_end_id' => ['nullable', 'exists:materials,id'],
            'status' => ['nullable', 'in:DRAFT,SUBMITTED,APPROVED,REJECTED,CANCELLED'],
        ]);

        $validation = $this->resolveValidationByNb((int) $data['size_nb']);
        $maxNoc = (int) ($validation?->noc ?? 0);
        $nocInput = (int) ($data['noc'] ?? $maxNoc);
        if ($maxNoc > 0 && $nocInput > $maxNoc) {
            return response()->json([
                'message' => 'Nilai NOC melebihi batas maksimum untuk NB ini.',
                'errors' => ['noc' => ["Maksimal NOC untuk NB {$data['size_nb']} adalah {$maxNoc}."]],
            ], 422);
        }

        $lineNo = (int) ($data['line_no'] ?? 0);
        if ($lineNo <= 0) {
            $lineNo = ((int) $pceHeader->items()->max('line_no')) + 1;
        }
        $quantity = (int) ($data['quantity'] ?? 1);

        $item = DB::transaction(function () use ($pceHeader, $data, $validation, $lineNo, $nocInput) {
            $existing = PceItem::query()
                ->where('pce_header_id', $pceHeader->id)
                ->where('shape_id', $data['shape_id'] ?? null)
                ->where('type_config_id', $data['type_config_id'] ?? null)
                ->where('size_nb', $data['size_nb'])
                ->where('material_bellow_id', $data['material_bellow_id'] ?? null)
                ->where('material_flange_id', $data['material_flange_id'] ?? null)
                ->where('material_pipe_end_id', $data['material_pipe_end_id'] ?? null)
                ->where('status', $data['status'] ?? 'DRAFT')
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => (int) $existing->quantity + (int) ($data['quantity'] ?? 1),
                    'noc' => $nocInput,
                    'validation_id' => $validation?->id,
                    'id_mm' => $validation?->id_mm,
                    'od_mm' => $validation?->od_mm,
                    'thk_mm' => $validation?->thk,
                    'ply' => $validation?->ly,
                ]);

                return $existing->fresh();
            }

            return PceItem::create([
                'pce_header_id' => $pceHeader->id,
                'line_no' => $lineNo,
                'quantity' => (int) ($data['quantity'] ?? 1),
                'shape_id' => $data['shape_id'] ?? null,
                'type_config_id' => $data['type_config_id'] ?? null,
                'size_nb' => $data['size_nb'],
                'noc' => $nocInput,
                'validation_id' => $validation?->id,
                'id_mm' => $validation?->id_mm,
                'od_mm' => $validation?->od_mm,
                'thk_mm' => $validation?->thk,
                'ply' => $validation?->ly,
                'material_bellow_id' => $data['material_bellow_id'] ?? null,
                'material_flange_id' => $data['material_flange_id'] ?? null,
                'material_pipe_end_id' => $data['material_pipe_end_id'] ?? null,
                'status' => $data['status'] ?? 'DRAFT',
            ]);
        });

        return response()->json([
            'message' => 'PCE item berhasil dibuat.',
            'data' => $item,
        ], 201);
    }

    public function updateItem(Request $request, PceHeader $pceHeader, PceItem $pceItem): JsonResponse
    {
        abort_unless($pceItem->pce_header_id === $pceHeader->id, 404);

        $data = $request->validate([
            'line_no' => ['nullable', 'integer', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'shape_id' => ['nullable', 'exists:shapes,id'],
            'type_config_id' => ['nullable', 'exists:product_type_configs,id'],
            'size_nb' => ['required', 'integer', 'min:1'],
            'noc' => ['nullable', 'integer', 'min:0'],
            'material_bellow_id' => ['nullable', 'exists:materials,id'],
            'material_flange_id' => ['nullable', 'exists:materials,id'],
            'material_pipe_end_id' => ['nullable', 'exists:materials,id'],
            'status' => ['nullable', 'in:DRAFT,SUBMITTED,APPROVED,REJECTED,CANCELLED'],
        ]);

        $validation = $this->resolveValidationByNb((int) $data['size_nb']);
        $maxNoc = (int) ($validation?->noc ?? 0);
        $nocInput = (int) ($data['noc'] ?? $maxNoc);
        if ($maxNoc > 0 && $nocInput > $maxNoc) {
            return response()->json([
                'message' => 'Nilai NOC melebihi batas maksimum untuk NB ini.',
                'errors' => ['noc' => ["Maksimal NOC untuk NB {$data['size_nb']} adalah {$maxNoc}."]],
            ], 422);
        }

        $pceItem->update([
            'line_no' => $data['line_no'] ?? $pceItem->line_no,
            'quantity' => $data['quantity'] ?? $pceItem->quantity,
            'shape_id' => $data['shape_id'] ?? null,
            'type_config_id' => $data['type_config_id'] ?? null,
            'size_nb' => $data['size_nb'],
            'noc' => $nocInput,
            'validation_id' => $validation?->id,
            'id_mm' => $validation?->id_mm,
            'od_mm' => $validation?->od_mm,
            'thk_mm' => $validation?->thk,
            'ply' => $validation?->ly,
            'material_bellow_id' => $data['material_bellow_id'] ?? null,
            'material_flange_id' => $data['material_flange_id'] ?? null,
            'material_pipe_end_id' => $data['material_pipe_end_id'] ?? null,
            'status' => $data['status'] ?? $pceItem->status,
        ]);

        return response()->json([
            'message' => 'PCE item berhasil diupdate.',
            'data' => $pceItem->fresh(),
        ]);
    }

    public function lookupValidation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'size_nb' => ['required', 'integer', 'min:1'],
        ]);

        $validation = $this->resolveValidationByNb((int) $data['size_nb']);
        if (! $validation) {
            return response()->json([
                'message' => "Data validasi expansion joint untuk NB {$data['size_nb']} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'nb' => (int) $validation->nb,
            'max_noc' => (int) ($validation->noc ?? 0),
            'default_noc' => (int) ($validation->noc ?? 0),
            'id_mm' => $validation->id_mm,
            'od_mm' => $validation->od_mm,
            'thk_mm' => $validation->thk,
            'ply' => $validation->ly,
            'validation_id' => $validation->id,
        ]);
    }

    private function resolveValidationByNb(int $nb): ?DataValidasiEjmExpansionJoint
    {
        return DataValidasiEjmExpansionJoint::query()
            ->where('nb', $nb)
            ->where('is_active', true)
            ->orderByDesc('standard_version_id')
            ->orderByDesc('id')
            ->first();
    }
}
