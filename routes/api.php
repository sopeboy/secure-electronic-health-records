<?php

use App\Http\Controllers\Api\LabResultApiController;
use Illuminate\Support\Facades\Route;

// Lab integration: token required, max 30 requests per minute per token.
Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    Route::post('/lab-results', [LabResultApiController::class, 'store'])->name('api.lab-results.store');
});
