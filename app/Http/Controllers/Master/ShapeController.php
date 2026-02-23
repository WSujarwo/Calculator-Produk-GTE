<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Shape;
use Illuminate\Http\Request;

class ShapeController extends Controller
{
    public function index(Request $request)
    {
        $q = Shape::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where(function ($w) use ($s) {
                $w->where('shape_code', 'like', "%{$s}%")
                  ->orWhere('shape_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('is_active')) {
            $q->where('is_active', (int)$request->is_active);
        }

        $shapes = $q->orderBy('shape_code')->paginate(15)->withQueryString();

        return view('master.shapes.index', compact('shapes'));
    }

    public function create()
    {
        return view('master.shapes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shape_code' => ['required','string','max:60','unique:shapes,shape_code'],
            'shape_name' => ['required','string','max:120'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        Shape::create($data);

        return redirect()->route('master.shapes.index')->with('success', 'Shape created.');
    }

    public function edit(Shape $shape)
    {
        return view('master.shapes.edit', compact('shape'));
    }

    public function update(Request $request, Shape $shape)
    {
        $data = $request->validate([
            'shape_code' => ['required','string','max:60',"unique:shapes,shape_code,{$shape->id}"],
            'shape_name' => ['required','string','max:120'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $shape->update($data);

        return redirect()->route('master.shapes.index')->with('success', 'Shape updated.');
    }

    public function destroy(Shape $shape)
    {
        $shape->delete();
        return redirect()->route('master.shapes.index')->with('success', 'Shape deleted.');
    }
}