<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Company;
use App\Models\Marketing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.quotations.view')->only(['index']);
        $this->middleware('permission:settings.quotations.create')->only(['create', 'store', 'import']);
        $this->middleware('permission:settings.quotations.edit')->only(['edit', 'update']);
        $this->middleware('permission:settings.quotations.delete')->only(['destroy']);
        $this->middleware('permission:settings.quotations.create')->only(['storeInlineCompany', 'storeInlineMarketing']);
    }

    public function index()
    {
        $quotations = Quotation::with(['company', 'marketing'])
            ->latest()
            ->paginate(10);

        return view('settings.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $companies = Company::orderBy('company_name')->get();
        $marketings = Marketing::orderBy('name')->get();

        return view('settings.quotations.create', compact('companies', 'marketings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
            'marketing_id' => 'required|exists:marketings,id',
        ]);

        // Auto generate quotation number
        $last = Quotation::latest()->first();
        $number = $last ? intval(substr($last->quotation_no, -4)) + 1 : 1;
        $quotationNo = 'QUO-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        $company = Company::find($request->company_id);

        Quotation::create([
            'quotation_no' => $quotationNo,
            'quotation_date' => $request->quotation_date,
            'company_id' => $request->company_id,
            'marketing_id' => $request->marketing_id,
            'company_address' => $company->address,
            'status' => 'DRAFT',
        ]);

        return redirect()->route('settings.quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function edit(Quotation $quotation)
    {
        $companies = Company::orderBy('company_name')->get();
        $marketings = Marketing::orderBy('name')->get();

        return view('settings.quotations.edit', compact('quotation', 'companies', 'marketings'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'quotation_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
            'marketing_id' => 'required|exists:marketings,id',
        ]);

        $company = Company::find($request->company_id);

        $quotation->update([
            'quotation_date' => $request->quotation_date,
            'company_id' => $request->company_id,
            'marketing_id' => $request->marketing_id,
            'company_address' => $company->address,
        ]);

        return redirect()->route('settings.quotations.index')
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('settings.quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    public function storeInlineCompany(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_code' => 'required|string|max:255|unique:companies,company_code',
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $company = Company::create($validated);

        return response()->json([
            'id' => $company->id,
            'label' => trim($company->company_code . ' - ' . $company->company_name),
            'address' => $company->address,
        ], 201);
    }

    public function storeInlineMarketing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'marketing_no' => 'required|string|max:255|unique:marketings,marketing_no',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:marketings,email',
            'phone' => 'nullable|string|max:255',
        ]);

        $marketing = Marketing::create($validated);

        return response()->json([
            'id' => $marketing->id,
            'label' => trim($marketing->marketing_no . ' - ' . $marketing->name),
        ], 201);
    }
}
