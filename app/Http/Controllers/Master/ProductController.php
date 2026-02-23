<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where(function ($w) use ($s) {
                $w->where('product_code', 'like', "%{$s}%")
                  ->orWhere('product_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('is_active')) {
            $q->where('is_active', (int)$request->is_active);
        }

        $products = $q->orderBy('product_code')->paginate(15)->withQueryString();

        return view('master.products.index', compact('products'));
    }

    public function create()
    {
        return view('master.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_code' => ['required','string','max:60','unique:products,product_code'],
            'product_name' => ['required','string','max:120'],
            'is_active'    => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        Product::create($data);

        return redirect()->route('master.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('master.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'product_code' => ['required','string','max:60',"unique:products,product_code,{$product->id}"],
            'product_name' => ['required','string','max:120'],
            'is_active'    => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $product->update($data);

        return redirect()->route('master.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('master.products.index')->with('success', 'Product deleted.');
    }
}