<?php

use Illuminate\Support\Facades\Route;
use Modules\Market\Http\Controllers\MarketController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('markets', MarketController::class)->names('market');
});
