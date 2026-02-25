<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master.marketing.view')->only(['index']);
        $this->middleware('permission:master.marketing.create')->only(['create', 'store', 'import']);
        $this->middleware('permission:master.marketing.edit')->only(['edit', 'update']);
        $this->middleware('permission:master.marketing.delete')->only(['destroy']);
    }

    public function index()
    {
        $marketings = Marketing::latest()->paginate(10);
        return view('settings.marketings.index', compact('marketings'));
    }

    public function create()
    {
        return view('settings.marketings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'marketing_no' => 'required|unique:marketings,marketing_no',
            'name' => 'required',
            'email' => 'required|email|unique:marketings,email',
        ]);

        Marketing::create($request->all());

        return redirect()->route('settings.marketings.index')
            ->with('success', 'Marketing created successfully.');
    }

    public function edit(Marketing $marketing)
    {
        return view('settings.marketings.edit', compact('marketing'));
    }

    public function update(Request $request, Marketing $marketing)
    {
        $request->validate([
            'marketing_no' => 'required|unique:marketings,marketing_no,' . $marketing->id,
            'name' => 'required',
            'email' => 'required|email|unique:marketings,email,' . $marketing->id,
        ]);

        $marketing->update($request->all());

        return redirect()->route('settings.marketings.index')
            ->with('success', 'Marketing updated successfully.');
    }

    public function destroy(Marketing $marketing)
    {
        $marketing->delete();

        return redirect()->route('settings.marketings.index')
            ->with('success', 'Marketing deleted successfully.');
    }
}
