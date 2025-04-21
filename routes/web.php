<?php

use Illuminate\Support\Facades\Route;

// Fallback route to prevent "Route [login] not defined" errors
Route::get('/login', function () {
    return response()->json(['message' => 'Use /api/auth/login for API authentication'], 401);
})->name('login');

Route::get('/', function () {
    return view('welcome');
});
