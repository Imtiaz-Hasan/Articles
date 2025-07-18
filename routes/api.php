<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\DailyRateLimit;

// AUTH ROUTES
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])
        ->middleware(['throttle:60,1', DailyRateLimit::class]);
    Route::middleware(['auth:api', 'throttle:60,1', DailyRateLimit::class])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// ARTICLE ROUTES (Authenticated)
Route::middleware(['auth:api', 'throttle:60,1', DailyRateLimit::class])->group(function () {
    Route::get('articles/mine', [ArticleController::class, 'mine']);
    Route::post('articles', [ArticleController::class, 'store']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    Route::put('articles/{id}', [ArticleController::class, 'update']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);

    // CATEGORY ROUTES (Authenticated)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
});

// PUBLIC ARTICLE ROUTES (No Auth, but rate-limited)
Route::middleware(['throttle:60,1', DailyRateLimit::class])->group(function () {
    Route::get('articles', [ArticleController::class, 'publicIndex']);
    Route::get('articles/public/{id}', [ArticleController::class, 'publicShow']);
}); 