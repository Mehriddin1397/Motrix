<?php

use Illuminate\Support\Facades\Route;
use Modules\AiAssistant\Http\Controllers\AiAssistantController;

Route::get('ai-assistant', [AiAssistantController::class, 'index'])->name('aiassistant.index');

Route::post('ai-assistant/messages', [AiAssistantController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('aiassistant.store');
