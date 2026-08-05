<?php

use Illuminate\Support\Facades\Route;
use Modules\Market\Http\Controllers\ConversationController;
use Modules\Market\Http\Controllers\MarketController;

Route::get('market', [MarketController::class, 'index'])->name('market.index');

// NOTE: literal-segment routes (create, conversations) must be registered
// before the wildcard `market/{market}` route below - otherwise Laravel
// matches the wildcard first and treats "create"/"conversations" as the
// {market} route parameter, causing a spurious 404 (model not found).
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('permission:listings.create')->group(function () {
        Route::get('market/create', [MarketController::class, 'create'])->name('market.create');
        Route::post('market', [MarketController::class, 'store'])->name('market.store');
    });

    Route::get('market/conversations', [ConversationController::class, 'index'])->name('market.conversations.index');
    Route::get('market/conversations/{conversation}', [ConversationController::class, 'show'])->name('market.conversations.show');
    Route::post('market/conversations/{conversation}/messages', [ConversationController::class, 'store'])->name('market.conversations.store');

    // Ownership vs. moderator access is resolved per-record in the controller
    // via ListingPolicy (see MarketController::edit/update/destroy).
    Route::get('market/{market}/edit', [MarketController::class, 'edit'])->name('market.edit');
    Route::put('market/{market}', [MarketController::class, 'update'])->name('market.update');
    Route::delete('market/{market}', [MarketController::class, 'destroy'])->name('market.destroy');
});

Route::get('market/{market}', [MarketController::class, 'show'])->name('market.show');
