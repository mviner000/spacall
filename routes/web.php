<?php

use App\Http\Controllers\SecurityDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Hello World';
});

// Group ALL Security Dashboard routes under 'web' middleware
// This ensures the Session is started and CSRF tokens work for all these routes
Route::middleware(['web'])->group(function () {
    
    // The page load (Starts the session & generates the CSRF token)
    Route::get('/security-dashboard', [SecurityDashboardController::class, 'index'])
        ->name('security.dashboard');

    // The login POST (Verifies the CSRF token against the session)
    Route::post('/security-dashboard/auth', [SecurityDashboardController::class, 'authenticate'])
        ->name('security.auth');

    // Logout
    Route::post('/security-dashboard/logout', [SecurityDashboardController::class, 'logout'])
        ->name('security.logout');

    // Stats
    Route::get('/security-dashboard/stats', [SecurityDashboardController::class, 'stats'])
        ->name('security.stats');
});