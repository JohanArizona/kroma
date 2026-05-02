<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChapterPageController;

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
        Route::get('/comics', [ComicController::class, 'index']);

        Route::middleware('admin')->prefix('comics')->group(function () {
            Route::post('/', [ComicController::class, 'store']);
            Route::patch('/{id}', [ComicController::class, 'updateMetadata']);
            Route::patch('/{id}/media', [ComicController::class, 'updateMedia']);
            Route::delete('/{id}', [ComicController::class, 'destroy']);
        });

        // --- LAYANAN EPISODE & HALAMAN (Tugas Rahma) ---
        Route::middleware('admin')->group(function () {
            Route::post('/comics/{comic_id}/chapters', [ChapterController::class, 'store']);
            Route::patch('/chapters/{id}', [ChapterController::class, 'update']);
            Route::delete('/chapters/{id}', [ChapterController::class, 'destroy']);
            Route::post('/chapters/{chapter_id}/pages', [ChapterPageController::class, 'bulkUpload']);
            Route::put('/chapters/{chapter_id}/pages/reorder', [ChapterPageController::class, 'reorder']);
        });

    });

});