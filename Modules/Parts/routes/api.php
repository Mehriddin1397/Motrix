<?php

use Illuminate\Support\Facades\Route;
use Modules\Parts\Http\Controllers\PartsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('parts', PartsController::class)->names('parts');
});
