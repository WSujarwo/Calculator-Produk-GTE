<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Company;
use App\Models\Marketing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master.quotations.view')->only(['index']);
        $this->middleware('permission:master.quotations.create')->only(['create', 'store', 'import']);
        $this->middleware('permission:master.quotations.edit')->only(['edit', 'update']);
        $this->middleware('permission:master.quotations.delete')->only(['destroy']);
    }

    public function index()
    {
        $quotations = Quotation::with(['company', 'marketing'])
            ->latest()
            ->paginate(10);

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $companies = Company::all();
        $marketings = Marketing::all();

        return view('quotations.create', compact('companies', 'marketings'));
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

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function edit(Quotation $quotation)
    {
        $companies = Company::all();
        $marketings = Marketing::all();

        return view('quotations.edit', compact('quotation', 'companies', 'marketings'));
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

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }
}