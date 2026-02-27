<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\ShapeController;
use App\Http\Controllers\Master\ProductShapeController;
use App\Http\Controllers\Master\ProductTypeConfigController;
use App\Http\Controllers\Master\ProductCatalogController;
use App\Http\Controllers\Master\CostProductController;
use App\Http\Controllers\Master\MaterialController;
use App\Http\Controllers\Settings\RolePermissionController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Settings\CustomerController;
use App\Http\Controllers\Settings\DataValidasiEjmBellowconvController;
use App\Http\Controllers\Settings\DataValidasiEjmProsesController;
use App\Http\Controllers\Settings\EjmExpansionJointController;
use App\Http\Controllers\Settings\DataValidasiEjmMaterialController;
use App\Http\Controllers\Settings\EjmValidationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\Calculation\GppCalculationController;
use App\Http\Controllers\Calculation\EjmCalculationController;
use App\Http\Controllers\Calculation\PceController;


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

Route::get('/calculation/gpp', [GppCalculationController::class, 'index'])->name('calculation.gpp');
Route::post('/calculation/gpp/validate', [GppCalculationController::class, 'validateInput'])->name('calculation.gpp.validate');

Route::get('/calculation/ejm', [EjmCalculationController::class, 'index'])->name('calculation.ejm');

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

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');

    Route::get('/setting/customer', [CustomerController::class, 'index'])->name('setting.customer.index');
    Route::get('/setting/gpp-validation', [SettingController::class, 'gppValidation'])->name('setting.gpp-validation');
    Route::post('/setting/gpp-validation/store', [SettingController::class, 'gppValidationStore'])->name('setting.gpp-validation.store');
    Route::post('/setting/gpp-validation/update', [SettingController::class, 'gppValidationUpdate'])->name('setting.gpp-validation.update');
    Route::get('/setting/ejm-validation', [EjmValidationController::class, 'index'])->name('setting.ejm-validation.index');
    Route::get('/setting/ejm-validation/create', [EjmValidationController::class, 'create'])->name('setting.ejm-validation.create');
    Route::get('/setting/ejm-validation/template/csv', [EjmValidationController::class, 'templateCsv'])->name('setting.ejm-validation.template.csv');
    Route::get('/setting/ejm-validation/template/excel', [EjmValidationController::class, 'templateExcel'])->name('setting.ejm-validation.template.excel');
    Route::post('/setting/ejm-validation/store', [EjmValidationController::class, 'store'])->name('setting.ejm-validation.store');
    Route::post('/setting/ejm-validation/update', [EjmValidationController::class, 'update'])->name('setting.ejm-validation.update');
    Route::post('/setting/ejm-validation/import', [EjmValidationController::class, 'import'])->name('setting.ejm-validation.import');
    Route::get('/setting/ejm-validation-proses', [DataValidasiEjmProsesController::class, 'index'])->name('setting.ejm-validation-proses.index');
    Route::get('/setting/ejm-validation-proses/create', [DataValidasiEjmProsesController::class, 'create'])->name('setting.ejm-validation-proses.create');
    Route::post('/setting/ejm-validation-proses/store', [DataValidasiEjmProsesController::class, 'store'])->name('setting.ejm-validation-proses.store');
    Route::post('/setting/ejm-validation-proses/update', [DataValidasiEjmProsesController::class, 'update'])->name('setting.ejm-validation-proses.update');
    Route::post('/setting/ejm-validation-proses/delete', [DataValidasiEjmProsesController::class, 'destroy'])->name('setting.ejm-validation-proses.delete');
    Route::post('/setting/ejm-validation-proses/import', [DataValidasiEjmProsesController::class, 'import'])->name('setting.ejm-validation-proses.import');
    Route::get('/setting/ejm-validation-proses/template/csv', [DataValidasiEjmProsesController::class, 'templateCsv'])->name('setting.ejm-validation-proses.template.csv');
    Route::get('/setting/ejm-validation-proses/template/excel', [DataValidasiEjmProsesController::class, 'templateExcel'])->name('setting.ejm-validation-proses.template.excel');
    Route::get('/setting/ejm-validation-proses/export/csv', [DataValidasiEjmProsesController::class, 'exportCsv'])->name('setting.ejm-validation-proses.export.csv');
    Route::get('/setting/ejm-validation-proses/export/excel', [DataValidasiEjmProsesController::class, 'exportExcel'])->name('setting.ejm-validation-proses.export.excel');
    Route::get('/setting/ejm-validation-bellowconv', [DataValidasiEjmBellowconvController::class, 'index'])->name('setting.ejm-validation-bellowconv.index');
    Route::get('/setting/ejm-validation-bellowconv/create', [DataValidasiEjmBellowconvController::class, 'create'])->name('setting.ejm-validation-bellowconv.create');
    Route::post('/setting/ejm-validation-bellowconv/store', [DataValidasiEjmBellowconvController::class, 'store'])->name('setting.ejm-validation-bellowconv.store');
    Route::post('/setting/ejm-validation-bellowconv/update', [DataValidasiEjmBellowconvController::class, 'update'])->name('setting.ejm-validation-bellowconv.update');
    Route::post('/setting/ejm-validation-bellowconv/delete', [DataValidasiEjmBellowconvController::class, 'destroy'])->name('setting.ejm-validation-bellowconv.delete');
    Route::post('/setting/ejm-validation-bellowconv/import', [DataValidasiEjmBellowconvController::class, 'import'])->name('setting.ejm-validation-bellowconv.import');
    Route::get('/setting/ejm-validation-bellowconv/template/csv', [DataValidasiEjmBellowconvController::class, 'templateCsv'])->name('setting.ejm-validation-bellowconv.template.csv');
    Route::get('/setting/ejm-validation-bellowconv/template/excel', [DataValidasiEjmBellowconvController::class, 'templateExcel'])->name('setting.ejm-validation-bellowconv.template.excel');
    Route::get('/setting/ejm-validation-bellowconv/export/csv', [DataValidasiEjmBellowconvController::class, 'exportCsv'])->name('setting.ejm-validation-bellowconv.export.csv');
    Route::get('/setting/ejm-validation-bellowconv/export/excel', [DataValidasiEjmBellowconvController::class, 'exportExcel'])->name('setting.ejm-validation-bellowconv.export.excel');
    Route::get('/setting/ejm-expansion-joint', [EjmExpansionJointController::class, 'index'])->name('setting.ejm-expansion-joint.index');
    Route::get('/setting/ejm-expansion-joint/create', [EjmExpansionJointController::class, 'create'])->name('setting.ejm-expansion-joint.create');
    Route::get('/setting/ejm-expansion-joint/template/csv', [EjmExpansionJointController::class, 'templateCsv'])->name('setting.ejm-expansion-joint.template.csv');
    Route::get('/setting/ejm-expansion-joint/template/excel', [EjmExpansionJointController::class, 'templateExcel'])->name('setting.ejm-expansion-joint.template.excel');
    Route::post('/setting/ejm-expansion-joint/store', [EjmExpansionJointController::class, 'store'])->name('setting.ejm-expansion-joint.store');
    Route::post('/setting/ejm-expansion-joint/update', [EjmExpansionJointController::class, 'update'])->name('setting.ejm-expansion-joint.update');
    Route::post('/setting/ejm-expansion-joint/import', [EjmExpansionJointController::class, 'import'])->name('setting.ejm-expansion-joint.import');
    Route::get('/setting/ejm-validation-material', [DataValidasiEjmMaterialController::class, 'index'])->name('setting.ejm-validation-material.index');
    Route::post('/setting/ejm-validation-material/import', [DataValidasiEjmMaterialController::class, 'import'])->name('setting.ejm-validation-material.import');
    Route::get('/setting/ejm-validation-material/template/csv', [DataValidasiEjmMaterialController::class, 'templateCsv'])->name('setting.ejm-validation-material.template.csv');
    Route::get('/setting/ejm-validation-material/template/excel', [DataValidasiEjmMaterialController::class, 'templateExcel'])->name('setting.ejm-validation-material.template.excel');

    Route::get('/setting/role-access', [RolePermissionController::class, 'index'])->name('setting.role-access');
    Route::put('/setting/role-access/roles/{role}', [RolePermissionController::class, 'updateRolePermissions'])->name('setting.role-access.roles.update');
    Route::put('/setting/role-access/users/{user}', [RolePermissionController::class, 'updateUserRole'])->name('setting.role-access.users.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/quotations/inline-companies', [QuotationController::class, 'storeInlineCompany'])->name('quotations.inline-companies.store');
    Route::post('/quotations/inline-marketings', [QuotationController::class, 'storeInlineMarketing'])->name('quotations.inline-marketings.store');
    Route::resource('quotations', QuotationController::class);

    Route::prefix('calculation/ejm/pce')->name('calculation.ejm.pce.')->group(function () {
        Route::get('/', [PceController::class, 'index'])->name('index');
        Route::post('/headers', [PceController::class, 'storeHeader'])->name('headers.store');
        Route::put('/headers/{pceHeader}', [PceController::class, 'updateHeader'])->name('headers.update');
        Route::post('/headers/{pceHeader}/items', [PceController::class, 'storeItem'])->name('items.store');
        Route::put('/headers/{pceHeader}/items/{pceItem}', [PceController::class, 'updateItem'])->name('items.update');
        Route::get('/lookup/validation', [PceController::class, 'lookupValidation'])->name('lookup.validation');
    });

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
Route::prefix('master')->name('master.')->middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('shapes', ShapeController::class)->except(['show']);

    // mapping
    Route::resource('product-shapes', ProductShapeController::class)->except(['show'])
        ->parameters(['product-shapes' => 'product_shape']);

    // type configs
    Route::resource('type-configs', ProductTypeConfigController::class)->except(['show'])
        ->parameters(['type-configs' => 'type_config']);

    // cost product
    Route::resource('cost-products', CostProductController::class)->except(['show'])
        ->parameters(['cost-products' => 'cost_product']);
    Route::post('cost-products/import', [CostProductController::class, 'import'])->name('cost-products.import');
    Route::get('cost-products-template', [CostProductController::class, 'template'])->name('cost-products.template');

    // materials
    Route::resource('materials', MaterialController::class)->except(['show']);
    Route::post('materials/import', [MaterialController::class, 'import'])->name('materials.import');
    Route::get('materials-template', [MaterialController::class, 'template'])->name('materials.template');

});

Route::prefix('settings')->name('settings.')->group(function () {

    Route::resource('companies', CompanyController::class);
    Route::resource('marketings', MarketingController::class);

});

// Dropdown JSON (buat calculator)
Route::prefix('api/catalog')->name('api.catalog.')->group(function () {
    Route::get('/products', [ProductCatalogController::class, 'products'])->name('products');
    Route::get('/products/{product}/shapes', [ProductCatalogController::class, 'shapesByProduct'])->name('shapes');
    Route::get('/products/{product}/shapes/{shape}/types', [ProductCatalogController::class, 'typesByProductShape'])->name('types');
});

require __DIR__.'/auth.php';
