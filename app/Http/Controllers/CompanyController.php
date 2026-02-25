<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.companies.view')->only(['index']);
        $this->middleware('permission:settings.companies.create')->only(['create', 'store', 'import']);
        $this->middleware('permission:settings.companies.edit')->only(['edit', 'update']);
        $this->middleware('permission:settings.companies.delete')->only(['destroy']);
    }
    
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('settings.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('settings.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_code' => 'required|unique:companies,company_code',
            'company_name' => 'required',
            'email' => 'nullable|email',
        ]);

        Company::create($request->all());

        return redirect()->route('settings.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        return view('settings.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'company_code' => 'required|unique:companies,company_code,' . $company->id,
            'company_name' => 'required',
            'email' => 'nullable|email',
        ]);

        $company->update($request->all());

        return redirect()->route('settings.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('settings.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
