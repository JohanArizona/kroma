<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComicController;

Route::prefix('v1')->group(function () {

    // --- LAYANAN AUTENTIKASI ---
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:api')->group(function () {

        // --- LAYANAN PROFIL ---
        Route::prefix('profile')->group(function () {
            Route::get('/me', [ProfileController::class, 'getProfile']);
            Route::patch('/name', [ProfileController::class, 'updateName']);
            Route::patch('/avatar', [ProfileController::class, 'updateAvatar']);
            Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
        });

        // --- LAYANAN MASTER KOMIK (Tugas Johan) ---
        // Member & Admin
        Route::get('/comics', [ComicController::class, 'index']);

        // Admin Saja
        Route::middleware('admin')->prefix('comics')->group(function () {
            Route::post('/', [ComicController::class, 'store']);
            Route::patch('/{id}', [ComicController::class, 'updateMetadata']);
            Route::patch('/{id}/media', [ComicController::class, 'updateMedia']);
            Route::delete('/{id}', [ComicController::class, 'destroy']);
        });

    });

});