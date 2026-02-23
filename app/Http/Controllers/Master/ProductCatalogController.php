<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductShape;
use App\Models\ProductTypeConfig;

class ProductCatalogController extends Controller
{
    public function products()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('product_code')
            ->get(['id', 'product_code', 'product_name']);

        return response()->json($products);
    }

    public function shapesByProduct(Product $product)
    {
        $shapes = ProductShape::query()
            ->with('shape:id,shape_code,shape_name,is_active')
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->get()
            ->pluck('shape')
            ->filter(fn ($shape) => $shape && $shape->is_active)
            ->unique('id')
            ->values()
            ->map(fn ($shape) => [
                'id' => $shape->id,
                'shape_code' => $shape->shape_code,
                'shape_name' => $shape->shape_name,
            ]);

        return response()->json($shapes);
    }

    public function typesByProductShape(Product $product, $shape)
    {
        $types = ProductTypeConfig::query()
            ->where('product_id', $product->id)
            ->where('shape_id', $shape)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('type_code')
            ->get(['id', 'type_code', 'type_name', 'sort_order']);

        return response()->json($types);
    }
}
