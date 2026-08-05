<?php

use Illuminate\Support\Facades\Route;
use Modules\Parts\Http\Controllers\PartsController;

Route::get('parts', [PartsController::class, 'index'])->name('parts.index');

// NOTE: the literal `parts/create` route must be registered before the
// wildcard `parts/{part:slug}` route below - otherwise Laravel matches the
// wildcard first and treats "create" as the slug, causing a spurious 404
// (model not found).
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('permission:parts.create')->group(function () {
        Route::get('parts/create', [PartsController::class, 'create'])->name('parts.create');
        Route::post('parts', [PartsController::class, 'store'])->name('parts.store');
    });

    // Ownership vs. moderator access is resolved per-record in the controller
    // via PartPolicy (see PartsController::edit/update/destroy).
    Route::get('parts/{part}/edit', [PartsController::class, 'edit'])->name('parts.edit');
    Route::put('parts/{part}', [PartsController::class, 'update'])->name('parts.update');
    Route::delete('parts/{part}', [PartsController::class, 'destroy'])->name('parts.destroy');
});

Route::get('parts/{part:slug}', [PartsController::class, 'show'])->name('parts.show');
