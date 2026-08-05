<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\NewsController;

Route::get('news', [NewsController::class, 'index'])->name('news.index');

// NOTE: the literal `news/create` route must be registered before the
// wildcard `news/{article:slug}` route below - otherwise Laravel matches the
// wildcard first and treats "create" as the slug, causing a spurious 404
// (model not found).
Route::middleware(['auth', 'verified', 'permission:news.manage'])->group(function () {
    Route::get('news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('news', [NewsController::class, 'store'])->name('news.store');
    Route::get('news/{article}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('news/{article}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('news/{article}', [NewsController::class, 'destroy'])->name('news.destroy');
});

Route::get('news/{article:slug}', [NewsController::class, 'show'])->name('news.show');
