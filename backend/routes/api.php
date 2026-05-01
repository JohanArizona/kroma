<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::prefix('v1')->group(function () {
    
    // --- LAYANAN AUTENTIKASI ---
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // --- LAYANAN PROFIL (Harus Login Dulu) ---
    // Middleware 'auth:api' akan melindungi endpoint ini (wajib bawa Bearer Token)
    Route::middleware('auth:api')->prefix('profile')->group(function () {
        Route::get('/me', [ProfileController::class, 'getProfile']);
        Route::patch('/name', [ProfileController::class, 'updateName']);
        Route::patch('/avatar', [ProfileController::class, 'updateAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
    });

});