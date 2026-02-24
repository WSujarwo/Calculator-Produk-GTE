<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = [
            ['code' => 'CUS-001', 'name' => 'PT Sukses Maju', 'city' => 'Jakarta', 'pic' => 'Andi'],
            ['code' => 'CUS-002', 'name' => 'CV Prima Teknik', 'city' => 'Bandung', 'pic' => 'Rina'],
            ['code' => 'CUS-003', 'name' => 'PT Mitra Packaging', 'city' => 'Surabaya', 'pic' => 'Budi'],
            ['code' => 'CUS-004', 'name' => 'PT Karya Nusantara', 'city' => 'Semarang', 'pic' => 'Siti'],
        ];

        return view('settings.customer', compact('customers'));
    }
}

