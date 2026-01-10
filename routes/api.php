<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TodoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // Public routes with strict rate limiting
    Route::middleware(['throttle.public'])->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle.login'); // Extra strict for registration
        
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle.login'); // Extra strict for login
    });
    
    // Protected routes with authenticated rate limiting
    Route::middleware(['auth:sanctum', 'throttle.auth'])->group(function () {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        // Todo routes
        Route::apiResource('todos', TodoController::class);
    });
});