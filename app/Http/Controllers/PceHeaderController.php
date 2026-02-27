<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Marketing;
use App\Models\PceHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PceHeaderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pce-headers.view')->only(['index', 'show']);
        $this->middleware('permission:pce-headers.create')->only(['store']);
        $this->middleware('permission:pce-headers.edit')->only(['edit', 'update']);
        $this->middleware('permission:pce-headers.delete')->only(['destroy']);
    }

    public function index()
    {
        $query = PceHeader::query()->select('pce_headers.*');

        if (Schema::hasTable('pce_items') && Schema::hasColumn('pce_items', 'pce_header_id')) {
            $query
                ->selectSub(function ($sub) {
                    $sub->from('pce_items')
                        ->selectRaw('COALESCE(SUM(qty), 0)')
                        ->whereColumn('pce_items.pce_header_id', 'pce_headers.id');
                }, 'quantity_item')
                ->selectRaw('0 as grand_total_idr');
        } elseif (Schema::hasTable('pce_details') && Schema::hasColumn('pce_details', 'pce_header_id')) {
            $query
                ->selectSub(function ($sub) {
                    $sub->from('pce_details')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('pce_details.pce_header_id', 'pce_headers.id');
                }, 'quantity_item')
                ->selectSub(function ($sub) {
                    if (Schema::hasColumn('pce_details', 'line_total')) {
                        $sub->from('pce_details')
                            ->selectRaw('COALESCE(SUM(line_total), 0)')
                            ->whereColumn('pce_details.pce_header_id', 'pce_headers.id');
                        return;
                    }

                    if (Schema::hasColumn('pce_details', 'grand_total')) {
                        $sub->from('pce_details')
                            ->selectRaw('COALESCE(SUM(grand_total), 0)')
                            ->whereColumn('pce_details.pce_header_id', 'pce_headers.id');
                        return;
                    }

                    $sub->selectRaw('0');
                }, 'grand_total_idr');
        } else {
            $query->selectRaw('0 as quantity_item, 0 as grand_total_idr');
        }

        if (Schema::hasColumn('pce_headers', 'created_at')) {
            $query->orderByDesc('created_at');
        } else {
            $query->orderByDesc('id');
        }

        $headers = $query->paginate(15);
        $companies = Company::orderBy('company_name')->get(['id', 'company_code', 'company_name']);
        $marketings = Marketing::orderBy('name')->get(['id', 'marketing_no', 'name']);

        return view('pce_headers.index', compact('headers', 'companies', 'marketings'));
    }

    public function show(PceHeader $pceHeader)
    {
        $pceHeader->loadMissing(['company', 'marketing']);

        return view('pce_headers.show', compact('pceHeader'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pce_number' => ['required', 'string', 'max:60', 'unique:pce_headers,pce_number'],
            'project_name' => ['nullable', 'string', 'max:150'],
            'end_user_id' => ['nullable', 'exists:companies,id'],
            'area' => ['nullable', 'string', 'max:120'],
            'drawing_no' => ['nullable', 'string', 'max:80'],
            'document_no' => ['nullable', 'string', 'max:80'],
            'revision' => ['nullable', 'string', 'max:20'],
            'pce_date' => ['nullable', 'date'],
            'sales_user_id' => ['nullable', 'exists:marketings,id'],
        ]);

        $data['project_name'] = $data['project_name'] ?? 'Expansion Joint Metal';
        $data['status'] = 'PENDING';

        PceHeader::create($data);

        return redirect()->route('pcelist')->with('success', 'PCE header berhasil dibuat.');
    }

    public function edit(PceHeader $pceHeader)
    {
        return view('pce_headers.edit', compact('pceHeader'));
    }

    public function update(Request $request, PceHeader $pceHeader)
    {
        $data = $request->validate([
            'pce_number' => ['required', 'string', 'max:60', "unique:pce_headers,pce_number,{$pceHeader->id}"],
            'project_name' => ['nullable', 'string', 'max:150'],
            'end_user_id' => ['nullable', 'exists:companies,id'],
            'area' => ['nullable', 'string', 'max:120'],
            'drawing_no' => ['nullable', 'string', 'max:80'],
            'document_no' => ['nullable', 'string', 'max:80'],
            'revision' => ['nullable', 'string', 'max:20'],
            'pce_date' => ['nullable', 'date'],
            'sales_user_id' => ['nullable', 'exists:marketings,id'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $pceHeader->update($data);

        return redirect()->route('pcelist')->with('success', 'PCE header berhasil diupdate.');
    }

    public function destroy(PceHeader $pceHeader)
    {
        $pceHeader->delete();

        return redirect()->route('pcelist')->with('success', 'PCE header berhasil dihapus.');
    }
}
