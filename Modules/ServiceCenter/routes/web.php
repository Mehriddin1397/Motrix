<?php

use Illuminate\Support\Facades\Route;
use Modules\ServiceCenter\Http\Controllers\ServiceCenterController;

Route::get('services', [ServiceCenterController::class, 'index'])->name('servicecenter.index');

// NOTE: the literal `services/create` route must be registered before the
// wildcard `services/{service}` route below - otherwise Laravel matches the
// wildcard first and treats "create" as the route parameter, causing a
// spurious 404 (model not found).
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('services/create', [ServiceCenterController::class, 'create'])->name('servicecenter.create');
    Route::post('services', [ServiceCenterController::class, 'store'])->name('servicecenter.store');
    Route::get('services/{service}/edit', [ServiceCenterController::class, 'edit'])->name('servicecenter.edit');
    Route::put('services/{service}', [ServiceCenterController::class, 'update'])->name('servicecenter.update');
    Route::delete('services/{service}', [ServiceCenterController::class, 'destroy'])->name('servicecenter.destroy');
});

Route::get('services/{service}', [ServiceCenterController::class, 'show'])->name('servicecenter.show');
