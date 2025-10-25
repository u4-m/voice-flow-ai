<?php

use App\Http\Controllers\TranscriptionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (authentication)
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Current user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Transcriptions
    Route::apiResource('transcriptions', TranscriptionsController::class)->except(['update']);
});
