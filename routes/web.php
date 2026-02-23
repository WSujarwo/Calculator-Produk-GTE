<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\ShapeController;
use App\Http\Controllers\Master\ProductShapeController;
use App\Http\Controllers\Master\ProductTypeConfigController;
use App\Http\Controllers\Master\ProductCatalogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/calculation', function () {
    return view('calculation');
})->middleware(['auth', 'verified'])->name('calculation');

Route::get('/calculation/rti', function () {
    return view('calculation.rti');
})->name('calculation.rti');

Route::get('/calculation/gpp', function () {
    return view('calculation.gpp');
})->name('calculation.gpp');

Route::get('/calculation/ejm', function () {
    return view('calculation.ejm');
})->name('calculation.ejm');

Route::get('/extractor/rti', function () {
    return view('extractor.rti');
})->name('extractor.rti');

Route::get('/extractor/gpp', function () {
    return view('extractor.gpp');
})->name('extractor.gpp');

Route::get('/extractor/ejm', function () {
    return view('extractor.ejm');
})->name('extractor.ejm');

Route::get('/quotation', function () {
    return view('quotation');
})->middleware(['auth', 'verified'])->name('quotation');

Route::get('/orderlist', function () {
    return view('orderlist');
})->middleware(['auth', 'verified'])->name('orderlist');






Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth','role:admin'])->get('/admin', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth','role:logistik'])->get('/logistik', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth','role:ppc'])->get('/ppc', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth','role:estimator'])->get('/estimator', function () {
    return redirect('/dashboard');
});

// Master Data Routes
Route::prefix('master')->name('master.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('shapes', ShapeController::class)->except(['show']);

    // mapping
    Route::resource('product-shapes', ProductShapeController::class)->except(['show'])
        ->parameters(['product-shapes' => 'product_shape']);

    // type configs
    Route::resource('type-configs', ProductTypeConfigController::class)->except(['show'])
        ->parameters(['type-configs' => 'type_config']);
});

// Dropdown JSON (buat calculator)
Route::prefix('api/catalog')->name('api.catalog.')->group(function () {
    Route::get('/products', [ProductCatalogController::class, 'products'])->name('products');
    Route::get('/products/{product}/shapes', [ProductCatalogController::class, 'shapesByProduct'])->name('shapes');
    Route::get('/products/{product}/shapes/{shape}/types', [ProductCatalogController::class, 'typesByProductShape'])->name('types');
});

require __DIR__.'/auth.php';
