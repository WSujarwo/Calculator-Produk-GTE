<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductShape;
use App\Models\Shape;
use Illuminate\Http\Request;

class ProductShapeController extends Controller
{
    public function index(Request $request)
    {
        $q = ProductShape::query()->with(['product','shape']);

        if ($request->filled('product_id')) $q->where('product_id', $request->product_id);
        if ($request->filled('shape_id')) $q->where('shape_id', $request->shape_id);
        if ($request->filled('is_active')) $q->where('is_active', (int)$request->is_active);

        $mappings = $q->orderByDesc('id')->paginate(15)->withQueryString();

        $products = Product::orderBy('product_code')->get(['id','product_code','product_name']);
        $shapes   = Shape::orderBy('shape_code')->get(['id','shape_code','shape_name']);

        return view('master.product_shapes.index', compact('mappings','products','shapes'));
    }

    public function create()
    {
        $products = Product::orderBy('product_code')->get(['id','product_code','product_name']);
        $shapes   = Shape::orderBy('shape_code')->get(['id','shape_code','shape_name']);

        return view('master.product_shapes.create', compact('products','shapes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'shape_id'   => ['required','exists:shapes,id'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        // prevent duplicate mapping
        ProductShape::updateOrCreate(
            ['product_id' => $data['product_id'], 'shape_id' => $data['shape_id']],
            ['is_active' => $data['is_active']]
        );

        return redirect()->route('master.product-shapes.index')->with('success', 'Mapping saved.');
    }

    public function edit(ProductShape $product_shape)
    {
        $products = Product::orderBy('product_code')->get(['id','product_code','product_name']);
        $shapes   = Shape::orderBy('shape_code')->get(['id','shape_code','shape_name']);

        return view('master.product_shapes.edit', [
            'mapping'  => $product_shape->load(['product','shape']),
            'products' => $products,
            'shapes'   => $shapes,
        ]);
    }

    public function update(Request $request, ProductShape $product_shape)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'shape_id'   => ['required','exists:shapes,id'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        // pastikan unique constraint tidak bentrok
        $exists = ProductShape::where('product_id', $data['product_id'])
            ->where('shape_id', $data['shape_id'])
            ->where('id', '!=', $product_shape->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['shape_id' => 'Mapping already exists for this product + shape.'])->withInput();
        }

        $product_shape->update($data);

        return redirect()->route('master.product-shapes.index')->with('success', 'Mapping updated.');
    }

    public function destroy(ProductShape $product_shape)
    {
        $product_shape->delete();
        return redirect()->route('master.product-shapes.index')->with('success', 'Mapping deleted.');
    }
}