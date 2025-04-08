<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum;

Route::get('/user', static function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
