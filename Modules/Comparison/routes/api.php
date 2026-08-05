<?php

use Illuminate\Support\Facades\Route;
use Modules\Comparison\Http\Controllers\ComparisonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('comparisons', ComparisonController::class)->names('comparison');
});
