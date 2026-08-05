<?php

use Illuminate\Support\Facades\Route;
use Modules\Video\Http\Controllers\VideoController;

Route::get('videos', [VideoController::class, 'index'])->name('video.index');

// NOTE: the literal `videos/create` route must be registered before the
// wildcard `videos/{video:slug}` route below - otherwise Laravel matches the
// wildcard first and treats "create" as the slug, causing a spurious 404
// (model not found).
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('videos/create', [VideoController::class, 'create'])->name('video.create');
    Route::post('videos', [VideoController::class, 'store'])->name('video.store');
    Route::get('videos/{video}/edit', [VideoController::class, 'edit'])->name('video.edit');
    Route::put('videos/{video}', [VideoController::class, 'update'])->name('video.update');
    Route::delete('videos/{video}', [VideoController::class, 'destroy'])->name('video.destroy');
});

Route::get('videos/{video:slug}', [VideoController::class, 'show'])->name('video.show');
