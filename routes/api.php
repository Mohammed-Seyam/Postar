<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('videos', \App\Http\Controllers\VideoController::class);
    Route::apiResource('schedule', \App\Http\Controllers\ScheduleController::class);

    Route::post('subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe']);

    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [\App\Http\Controllers\DashboardController::class, 'stats']);
        Route::get('calendar', [\App\Http\Controllers\DashboardController::class, 'calendar']);
        Route::get('storage', [\App\Http\Controllers\DashboardController::class, 'storage']);
    });
});

Route::post('webhook/stripe', [\App\Http\Controllers\SubscriptionController::class, 'webhook']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
