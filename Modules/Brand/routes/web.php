<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\BrandController;

Route::get('brands', [BrandController::class, 'index'])->name('brand.index');

// NOTE: the literal `brands/create` route must be registered before the
// wildcard `brands/{brand}` route below - otherwise Laravel matches the
// wildcard first and treats "create" as the route parameter, causing a
// spurious 404 (model not found).
Route::middleware(['auth', 'verified', 'permission:motorcycles.brands.manage'])->group(function () {
    Route::get('brands/create', [BrandController::class, 'create'])->name('brand.create');
    Route::post('brands', [BrandController::class, 'store'])->name('brand.store');
    Route::get('brands/{brand}/edit', [BrandController::class, 'edit'])->name('brand.edit');
    Route::put('brands/{brand}', [BrandController::class, 'update'])->name('brand.update');
    Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');
});

Route::get('brands/{brand}', [BrandController::class, 'show'])->name('brand.show');
