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
        $this->middleware('permission:settings.quotations.view')->only(['index', 'show']);
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
        $returnTo = request()->query('return_to');
        $companies = Company::orderBy('company_name')->get();
        $marketings = Marketing::orderBy('name')->get();
        $customerOptions = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'label' => trim($company->company_code . ' - ' . $company->company_name),
                'address' => $company->address ?? '',
            ];
        })->values();
        $marketingOptions = $marketings->map(function ($marketing) {
            return [
                'id' => $marketing->id,
                'label' => trim($marketing->marketing_no . ' - ' . $marketing->name),
            ];
        })->values();

        return view('settings.quotations.create', compact('companies', 'marketings', 'customerOptions', 'marketingOptions', 'returnTo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
            'marketing_id' => 'required|exists:marketings,id',
            'revision_no' => 'nullable|integer|min:0',
            'attention' => 'nullable|string|max:255',
            'delivery_to' => 'nullable|string|max:255',
            'delivery_term' => 'nullable|string|max:255',
            'payment_days' => 'nullable|integer|min:0',
            'delivery_time_days' => 'nullable|integer|min:0',
            'scope_of_work' => 'nullable|string|max:255',
            'price_validity_weeks' => 'nullable|integer|min:0',
            'company_address' => 'nullable|string',
            'result_status' => 'required|in:GAGAL,PENDING,SUKSES',
            'return_to' => 'nullable|in:gpp',
        ]);

        // Auto generate quotation number
        $last = Quotation::latest()->first();
        $number = $last ? intval(substr($last->quotation_no, -4)) + 1 : 1;
        $quotationNo = 'QUO-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        $company = Company::find($validated['company_id']);

        $quotation = Quotation::create([
            'quotation_no' => $quotationNo,
            'quotation_date' => $validated['quotation_date'],
            'revision_no' => $validated['revision_no'] ?? 0,
            'company_id' => $validated['company_id'],
            'marketing_id' => $validated['marketing_id'],
            'attention' => $validated['attention'] ?? null,
            'delivery_to' => $validated['delivery_to'] ?? null,
            'delivery_term' => $validated['delivery_term'] ?? null,
            'payment_days' => $validated['payment_days'] ?? null,
            'delivery_time_days' => $validated['delivery_time_days'] ?? null,
            'scope_of_work' => $validated['scope_of_work'] ?? null,
            'price_validity_weeks' => $validated['price_validity_weeks'] ?? null,
            'company_address' => $validated['company_address'] ?? $company->address,
            'status' => 'DRAFT',
            'result_status' => $validated['result_status'],
        ]);

        if (($validated['return_to'] ?? null) === 'gpp') {
            return redirect()
                ->route('calculation.gpp', ['quotation_id' => $quotation->id])
                ->with('status', "Quotation {$quotation->quotation_no} berhasil dibuat. Lanjut input produk GPP.");
        }

        return redirect()->route('settings.quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function edit(Quotation $quotation)
    {
        $companies = Company::orderBy('company_name')->get();
        $marketings = Marketing::orderBy('name')->get();
        $customerOptions = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'label' => trim($company->company_code . ' - ' . $company->company_name),
                'address' => $company->address ?? '',
            ];
        })->values();
        $marketingOptions = $marketings->map(function ($marketing) {
            return [
                'id' => $marketing->id,
                'label' => trim($marketing->marketing_no . ' - ' . $marketing->name),
            ];
        })->values();

        return view('settings.quotations.edit', compact('quotation', 'companies', 'marketings', 'customerOptions', 'marketingOptions'));
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['company', 'marketing']);

        return view('settings.quotations.show', compact('quotation'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'quotation_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
            'marketing_id' => 'required|exists:marketings,id',
            'revision_no' => 'nullable|integer|min:0',
            'attention' => 'nullable|string|max:255',
            'delivery_to' => 'nullable|string|max:255',
            'delivery_term' => 'nullable|string|max:255',
            'payment_days' => 'nullable|integer|min:0',
            'delivery_time_days' => 'nullable|integer|min:0',
            'scope_of_work' => 'nullable|string|max:255',
            'price_validity_weeks' => 'nullable|integer|min:0',
            'company_address' => 'nullable|string',
            'result_status' => 'required|in:GAGAL,PENDING,SUKSES',
        ]);

        $company = Company::find($validated['company_id']);

        $quotation->update([
            'quotation_date' => $validated['quotation_date'],
            'revision_no' => $validated['revision_no'] ?? 0,
            'company_id' => $validated['company_id'],
            'marketing_id' => $validated['marketing_id'],
            'attention' => $validated['attention'] ?? null,
            'delivery_to' => $validated['delivery_to'] ?? null,
            'delivery_term' => $validated['delivery_term'] ?? null,
            'payment_days' => $validated['payment_days'] ?? null,
            'delivery_time_days' => $validated['delivery_time_days'] ?? null,
            'scope_of_work' => $validated['scope_of_work'] ?? null,
            'price_validity_weeks' => $validated['price_validity_weeks'] ?? null,
            'company_address' => $validated['company_address'] ?? $company->address,
            'result_status' => $validated['result_status'],
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
