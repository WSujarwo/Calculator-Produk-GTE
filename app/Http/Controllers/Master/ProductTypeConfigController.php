<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductTypeConfig;
use App\Models\Shape;
use Illuminate\Http\Request;

class ProductTypeConfigController extends Controller
{
    public function index(Request $request)
    {
        $q = ProductTypeConfig::query()->with(['product','shape']);

        if ($request->filled('product_id')) $q->where('product_id', $request->product_id);
        if ($request->filled('shape_id'))   $q->where('shape_id', $request->shape_id);
        if ($request->filled('is_active'))  $q->where('is_active', (int)$request->is_active);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where(function ($w) use ($s) {
                $w->where('type_code', 'like', "%{$s}%")
                  ->orWhere('type_name', 'like', "%{$s}%");
            });
        }

        $types = $q->orderBy('product_id')
            ->orderBy('shape_id')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        $products = Product::orderBy('product_code')->get(['id','product_code','product_name']);
        $shapes   = Shape::orderBy('shape_code')->get(['id','shape_code','shape_name']);

        return view('master.type_configs.index', compact('types','products','shapes'));
    }

    public function create()
    {
        $products = Product::orderBy('product_code')->get(['id','product_code','product_name']);
        $shapes   = Shape::orderBy('shape_code')->get(['id','shape_code','shape_name']);

        return view('master.type_configs.create', compact('products','shapes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'shape_id'   => ['nullable','exists:shapes,id'],
            'type_code'  => ['required','string','max:120'],
            'type_name'  => ['required','string','max:200'],
            'notes'      => ['nullable','string'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['sort_order'] = (int)($data['sort_order'] ?? 0);
        $data['is_active']  = (bool)($data['is_active'] ?? true);

        // enforce unique (product_id, shape_id, type_code)
        $exists = ProductTypeConfig::where('product_id', $data['product_id'])
            ->where('type_code', $data['type_code'])
            ->when(array_key_exists('shape_id', $data), fn($q) => $q->where('shape_id', $data['shape_id']))
            ->exists();

        if ($exists) {
            return back()->withErrors(['type_code' => 'Type code already exists for this product/shape.'])->withInput();
        }

        ProductTypeConfig::create($data);

        return redirect()->route('master.type-configs.index')->with('success', 'Type configuration created.');
    }

    public function edit(ProductTypeConfig $type_config)
    {
        $products = Product::orderBy('product_code')->get(['id','product_code','product_name']);
        $shapes   = Shape::orderBy('shape_code')->get(['id','shape_code','shape_name']);

        return view('master.type_configs.edit', compact('type_config','products','shapes'));
    }

    public function update(Request $request, ProductTypeConfig $type_config)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'shape_id'   => ['nullable','exists:shapes,id'],
            'type_code'  => ['required','string','max:120'],
            'type_name'  => ['required','string','max:200'],
            'notes'      => ['nullable','string'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['sort_order'] = (int)($data['sort_order'] ?? 0);
        $data['is_active']  = (bool)($data['is_active'] ?? false);

        $exists = ProductTypeConfig::where('product_id', $data['product_id'])
            ->where('type_code', $data['type_code'])
            ->where('id', '!=', $type_config->id)
            ->when(array_key_exists('shape_id', $data), fn($q) => $q->where('shape_id', $data['shape_id']))
            ->exists();

        if ($exists) {
            return back()->withErrors(['type_code' => 'Type code already exists for this product/shape.'])->withInput();
        }

        $type_config->update($data);

        return redirect()->route('master.type-configs.index')->with('success', 'Type configuration updated.');
    }

    public function destroy(ProductTypeConfig $type_config)
    {
        $type_config->delete();
        return redirect()->route('master.type-configs.index')->with('success', 'Type configuration deleted.');
    }
}