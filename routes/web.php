
<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('app');
})->name('login');

// Маршруты для страниц верификации email
Route::get('/email/verify', function () {
    return view('app');
});

Route::get('/email/verify/{id}/{hash}', function () {
    return view('app');
});

// Все остальные запросы направляем в SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

// Маршруты для сброса пароля
Route::post('/forgot-password', function () {
    return view('app');
});

Route::get('/reset-password/{token}', function () {
    return view('app');
});
