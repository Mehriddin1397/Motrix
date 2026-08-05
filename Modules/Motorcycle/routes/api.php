<?php

use Illuminate\Support\Facades\Route;
use Modules\Motorcycle\Http\Controllers\MotorcycleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('motorcycles', MotorcycleController::class)->names('motorcycle');
});
