<?php

use Illuminate\Support\Facades\Route;
use Modules\ServiceCenter\Http\Controllers\ServiceCenterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('servicecenters', ServiceCenterController::class)->names('servicecenter');
});
