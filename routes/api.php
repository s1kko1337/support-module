<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\EmailVerificationController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Маршрут для получения данных пользователя
Route::get('/user', static function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Публичные маршруты
Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Сброс и изменение пароля
    Route::post('forgot-password', [PasswordResetController::class, 'revoke']);
    Route::get('reset-password/{token}', [PasswordResetController::class, 'invoke'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// Маршруты, требующие аутентификации, но не требующие верифицированного email
Route::prefix('v1')->middleware(['throttle:api', 'auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Маршруты для верификации email
    // Проверка статуса верификации
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    // Верификация email по ссылке из письма
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    // Повторная отправка письма для верификации
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Маршруты, требующие верифицированного email
Route::prefix('v1')->middleware(['throttle:api', 'auth:sanctum', 'verified'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
});
