<?php

use Illuminate\Support\Facades\Route;
use Modules\Motorcycle\Http\Controllers\MotorcycleController;

Route::get('motorcycles', [MotorcycleController::class, 'index'])->name('motorcycle.index');

// NOTE: the literal `motorcycles/create` route must be registered before the
// wildcard `motorcycles/{motorcycle:slug}` route below - otherwise Laravel
// matches the wildcard first and treats "create" as the slug, causing a
// spurious 404 (model not found).
Route::middleware(['auth', 'verified', 'permission:motorcycles.models.manage'])->group(function () {
    Route::get('motorcycles/create', [MotorcycleController::class, 'create'])->name('motorcycle.create');
    Route::post('motorcycles', [MotorcycleController::class, 'store'])->name('motorcycle.store');
    Route::get('motorcycles/{motorcycle}/edit', [MotorcycleController::class, 'edit'])->name('motorcycle.edit');
    Route::put('motorcycles/{motorcycle}', [MotorcycleController::class, 'update'])->name('motorcycle.update');
    Route::delete('motorcycles/{motorcycle}', [MotorcycleController::class, 'destroy'])->name('motorcycle.destroy');
});

Route::get('motorcycles/{motorcycle:slug}', [MotorcycleController::class, 'show'])->name('motorcycle.show');
