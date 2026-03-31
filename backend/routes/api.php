<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ConflictController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'time' => now(),
    ]);
});

// 🔥 CORE CLAIM API
Route::post('/scan/claim', [ScanController::class, 'claim']);
Route::get('/conflicts', [ConflictController::class, 'index']);
Route::post('/conflicts/{bundle}/resolve', [ConflictController::class, 'resolve']);
Route::post('/bundles/bulk', [BundleController::class, 'store']);
Route::get('/bundles', [BundleController::class, 'index']);
Route::get('/dashboard/supervisor', [DashboardController::class, 'supervisor']);