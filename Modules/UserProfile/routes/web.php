<?php

use Illuminate\Support\Facades\Route;
use Modules\UserProfile\Http\Controllers\UserProfileController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('userprofiles', UserProfileController::class)->names('userprofile');
});
