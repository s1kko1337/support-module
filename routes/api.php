<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CertificationController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\StudentCharacteristicsController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\SubjectsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
        ->middleware('throttle:25,1')
        ->name('verification.send');
});

// Маршруты, требующие верифицированного email
Route::prefix('v1')->middleware(['throttle:api', 'auth:sanctum', 'verified', 'can:use-Api'])->group(function () {
    Route::get('/user', static function (Request $request) {
        return $request->user();});
    Route::post('password/change', [PasswordResetController::class, 'change']);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('events', EventController::class);
    Route::apiResource('subjects', SubjectsController::class);
    Route::apiResource('certifications', CertificationController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('studentCharacteristics', StudentCharacteristicsController::class);
});
