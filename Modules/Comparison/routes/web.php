<?php

use Illuminate\Support\Facades\Route;
use Modules\Comparison\Http\Controllers\ComparisonController;

Route::get('compare', [ComparisonController::class, 'index'])->name('comparison.index');
