<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Test Route
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Toko Buku Berjalan'
    ]);
});

// ===========================
// AUTH ROUTES (Tanpa Login)
// ===========================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);