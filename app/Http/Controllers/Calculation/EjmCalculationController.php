<?php

namespace App\Http\Controllers\Calculation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EjmCalculationController extends Controller
{
    private const TABLE = 'validasi_dataejm_can_length_calculations';

    public function index(Request $request): View
    {
        $query = DB::table(self::TABLE)->orderBy('nb');

        if ($request->filled('nb')) {
            $query->where('nb', (int) $request->input('nb'));
        }
        if ($request->filled('size_inch')) {
            $query->where('size_inch', (int) $request->input('size_inch'));
        }

        $rows = $query->paginate(50)->withQueryString();
        $sizes = DB::table(self::TABLE)->select('size_inch')->distinct()->orderBy('size_inch')->pluck('size_inch');

        return view('calculation.ejm', [
            'rows' => $rows,
            'sizes' => $sizes,
            'selectedNb' => $request->input('nb'),
            'selectedSize' => $request->input('size_inch'),
        ]);
    }
}
