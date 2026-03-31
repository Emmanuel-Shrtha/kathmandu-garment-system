<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

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